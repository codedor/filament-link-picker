<?php

namespace Codedor\LinkPicker\Filament;

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
                ->label(fn ($state) => $state ? 'Edit' : 'Add')
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

                    $schema->each(function (Field $field) use (&$state, $statePath, $get, $actionNestingIndex) {
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
                    $schema->each(function (Field $field) use (&$state, $statePath, $get, $actionNestingIndex) {
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
                ->label('Delete')
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
            'label' => $route->getLabel(),
            'parameters' => $parameters,
            'newTab' => $state['newTab'] ?? false,
        ];
    }

    private function getFormSchemaForRoute(?string $selectedRoute): Collection
    {
        $routeField = Select::make('route')
            ->label('Route')
            ->options(function () {
                return LinkCollection::values()
                    ->unique(fn (Link $link) => $link->getCleanRouteName())
                    ->mapWithKeys(fn (Link $link) => [
                        $link->getCleanRouteName() => "{$link->getGroup()} - {$link->getLabel()}",
                    ]);
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

        // If the schema is empty, we'll check if there are any parameters
        if ($schema->isEmpty()) {
            $route = $link->getRoute();

            $schema = collect($route->signatureParameters())
                ->filter(function (ReflectionParameter $parameter) {
                    return $parameter->getType() && class_exists(Reflector::getParameterClassName($parameter));
                })
                ->map(function (ReflectionParameter $parameter) {
                    $model = Reflector::getParameterClassName($parameter);

                    return Select::make("parameters.{$parameter->name}")
                        ->label(Str::title($parameter->name))
                        ->required(! $parameter->allowsNull())
                        ->searchable(false) //disabled because we get alpine errors
                        ->options($model::withoutGlobalScopes()->pluck(
                            $model::$linkPickerTitleField ?? 'id',
                            (new $model)->getKeyName(),
                        ));
                });
        }

        return $schema
            ->prepend($routeField)
            ->add(Checkbox::make('newTab')->label('Open in a new tab'));
    }
}
