<?php

namespace Codedor\LinkPicker\Filament;

use App\Models\Event;
use Codedor\LinkPicker\Facades\LinkCollection;
use Codedor\LinkPicker\Link;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use ReflectionParameter;

class LinkPickerInput extends Field
{
    protected string $view = 'filament-link-picker::filament.link-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerActions([
            Action::make('link-picker-modal')
                ->label(fn ($state) => $state
                    ? __('filament-link-picker::input.edit link')
                    : __('filament-link-picker::input.select link')
                )
                ->icon(fn ($state) => $state ? 'heroicon-o-pencil' : 'heroicon-o-plus')
                ->color('gray')
                ->iconSize('sm')
                ->fillForm(function (Get $get, Component $component, \Livewire\Component $livewire): array {
                    $statePath = $component->getStatePath(false);

                    $schema = $this->getFormSchemaForRoute($get("{$statePath}.route"));

                    $state = [
                        'route' => $get("{$statePath}.route"),
                        'newTab' => $get("{$statePath}.newTab"),
                        'parameters' => $get("{$statePath}.parameters") ?: [],
                    ];

                    $actionNestingIndex = array_key_last($livewire->mountedFormComponentActions);

                    $schema->each(function (Field $field) use (&$state, $statePath, $get, $actionNestingIndex, $livewire) {
                        $fieldStatePath = $field->statePath;

                        data_fill(
                            $state,
                            $fieldStatePath,
                            data_get(
                                $livewire->mountedFormComponentActionsData[$actionNestingIndex] ?? [],
                                "{$statePath}.{$fieldStatePath}"
                            ) ?? $get("{$statePath}.{$fieldStatePath}") ?? null
                        );
                    });

                    return $state;
                })
                ->form(function (Get $get, Component $component, \Livewire\Component $livewire, Form $form) {
                    $statePath = $component->getStatePath(false);

                    $actionNestingIndex = array_key_last($livewire->mountedFormComponentActions);

                    $schema = $this->getFormSchemaForRoute(
                        $livewire->mountedFormComponentActionsData[$actionNestingIndex]['route'] ?? $get("{$statePath}.route") ?? null
                    );

                    $state = $livewire->mountedFormComponentActionsData[$actionNestingIndex] ?? [];

                    // since the fields are dynamic we have to fill the state manually,
                    // else validation will fail because property is not in the state
                    $schema->each(function (Field $field) use (&$state, $statePath, $get, $actionNestingIndex, $livewire) {
                        $fieldStatePath = $field->statePath;

                        data_fill(
                            $state,
                            $fieldStatePath,
                            data_get(
                                $livewire->mountedFormComponentActionsData[$actionNestingIndex] ?? [],
                                "{$statePath}.{$fieldStatePath}"
                            ) ?? $get("{$statePath}.{$fieldStatePath}") ?? null
                        );
                    });

                    $livewire->mountedFormComponentActionsData[$actionNestingIndex] = $state;
                    $form->fill($state);

                    return $schema->toArray();
                })
                ->action(function (Set $set, array $data, Component $component) {
                    $set($component->getStatePath(false), $data);
                }),

            Action::make('link-picker-clear')
                ->label(__('filament-link-picker::input.remove link'))
                ->icon('heroicon-o-trash')
                ->iconSize('sm')
                ->color('danger')
                ->action(function (Set $set) {
                    $set($this->getStatePath(false), null);
                }),
        ]);
    }

    public function getRouteDescription()
    {
        $state = $this->getState();

        if (! $state) {
            return null;
        }

        $route = LinkCollection::cleanRoute($state['route']);

        if (! $route) {
            return [];
        }

        $route->parameters($state['parameters'] ?? []);

        $parameters = $route->getParameters();
        $resolvedRoute = $route->resolveParameters($parameters);

        foreach ($resolvedRoute->parameters ?? [] as $key => $value) {
            if ($value instanceof Model) {
                $value = $value->{$value::$linkPickerTitleField ?? 'id'};
                $parameters[$key] = $value;
            }
        }

        return [
            'group' => $route->getGroup(),
            'label' => $route->getLabel(),
            'parameters' => $parameters,
            'newTab' => $state['newTab'] ?? false,
        ];
    }

    private function getFormSchemaForRoute(?string $selectedRoute): Collection
    {
        $routeField = Select::make('route')
            ->label(__('filament-link-picker::input.route label'))
            ->options(function () {
                return LinkCollection::values()
                    ->unique(fn (Link $link) => $link->getCleanRouteName())
                    ->groupBy(fn (Link $link) => $link->getGroup())
                    ->sortKeys()
                    ->map(fn (Collection $links) => $links->mapWithKeys(fn (Link $link) => [
                        $link->getCleanRouteName() => $link->getLabel(),
                    ]));
            })
            ->required()
            ->live();

        if (! $selectedRoute) {
            return collect([$routeField]);
        }

        $link = LinkCollection::firstByCleanRouteName($selectedRoute);

        if (is_null($link)) {
            return collect([$routeField]);
        }

        $schema = $link->getSchema();

        $routeParameters = $this->routeParameters($link->getRoute());

        // If the schema is empty, we'll check if there are any parameters
        if ($schema->isEmpty()) {
            $schema = $routeParameters
                ->map(function (ReflectionParameter $parameter) {
                    $model = Reflector::getParameterClassName($parameter);

                    return Select::make("parameters.{$parameter->name}")
                        ->label(Str::title($parameter->name))
                        ->required(! $parameter->allowsNull())
                        ->searchable()
                        ->options($model::withoutGlobalScopes()->pluck(
                            $model::$linkPickerTitleField ?? 'id',
                            (new $model)->getKeyName(),
                        ))
                        ->live();
                });
        }

        if ($anchorData = $link->getWithAnchors()) {
            if (! data_get($anchorData, 'parameter')) {
                $anchorData['parameter'] = $routeParameters->first()->name;
            }

            if (! data_get($anchorData, 'model')) {
                $anchorData['model'] = Reflector::getParameterClassName($routeParameters->first());
            }

            $schema->add(
                Select::make('parameters.anchor')
                    ->hidden(fn (Get $get) => ! $get("parameters.{$anchorData['parameter']}"))
                    ->options(function (Get $get) use ($anchorData) {
                        $record = $anchorData['model']::find($get("parameters.{$anchorData['parameter']}"));

                        if (method_exists($record, 'isTranslatableAttribute') && $record->isTranslatableAttribute($anchorData['field'])) {
                            return $record?->getTranslation($anchorData['field'], referer_locale())->anchorList();
                        }

                        return $record?->{$anchorData['field']}->anchorList();
                    })
            );

        }

        return $schema
            ->prepend($routeField)
            ->add(
                Checkbox::make('newTab')
                    ->label(__('filament-link-picker::input.new tab label'))
            );
    }

    protected function routeParameters(Route $route): Collection
    {
        return collect($route->signatureParameters())
            ->filter(function (ReflectionParameter $parameter) {
                $className = Reflector::getParameterClassName($parameter);

                return $parameter->getType()
                    && class_exists($className)
                    && is_subclass_of($className, Model::class);
            });
    }
}
