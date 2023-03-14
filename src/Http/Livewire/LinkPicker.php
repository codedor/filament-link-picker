<?php

namespace Codedor\LinkPicker\Http\Livewire;

use Codedor\LinkPicker\Facades\LinkCollection;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;
use ReflectionParameter;

class LinkPicker extends Component implements HasForms
{
    use InteractsWithForms;

    public string $statePath;

    public string $state = '';

    public array $parameters = [];

    public bool $newTab = false;

    public function render()
    {
        return view('filament-link-picker::livewire.link-picker', [
            'routes' => LinkCollection::unique('route')->groupBy('group'),
        ]);
    }

    public function updatedState()
    {
        $this->parameters = [];
        $this->newTab = false;

        $this->form->fill();
    }

    public function submit()
    {
        $this->validate();

        $this->dispatchBrowserEvent('filament-link-picker.submit', [
            'statePath' => $this->statePath,
            'state' => [
                'route' => $this->state,
                'parameters' => $this->parameters,
                'newTab' => $this->newTab,
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        if (empty($this->state)) {
            return [];
        }

        $schema = LinkCollection::route($this->state)?->getSchema();

        // If the schema is empty, we'll check if there are any parameters
        if ($schema->isEmpty()) {
            $route = Route::getRoutes()->getByName($this->state);

            $schema = collect($route->signatureParameters())
                ->filter(function (ReflectionParameter $parameter) {
                    // Only return classnames
                    return $parameter->getType() && class_exists($parameter->getType()->getName());
                })
                ->map(function (ReflectionParameter $parameter) {
                    $model = $parameter->getType()->getName();

                    return Select::make("parameters.{$parameter->name}")
                        ->label(Str::title($parameter->name))
                        ->rules($parameter->allowsNull() ? '' : 'required')
                        ->options($model::withoutGlobalScopes()->pluck(
                            $model::$linkPickerTitleField ?? 'id',
                            (new $model)->getKeyName(),
                        ));
                });
        }

        return $schema->merge([
            Checkbox::make('newTab')->label('Open in a new tab'),
        ])->toArray();
    }
}
