<?php

namespace Codedor\LinkPicker\Http\Livewire;

use Codedor\LinkPicker\Facades\LinkCollection;
use Codedor\LinkPicker\Link;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use Livewire\Component;
use ReflectionParameter;

// @codeCoverageIgnoreStart
/**
 * @property-read \Filament\Forms\ComponentContainer $form
 */
class LinkPicker extends Component implements HasForms
{
    use InteractsWithForms;

    public string $statePath;

    public null|string $description = null;

    public string $route;

    public array $parameters;

    public bool $newTab;

    public array $initialState;

    public function mount(array $state = [])
    {
        $this->initialState = $state;

        $this->fillState($state);
    }

    public function render()
    {
        return view('filament-link-picker::livewire.link-picker', [
            'routes' => LinkCollection::unique(fn (Link $link) => $link->getCleanRouteName())->groupBy(fn (Link $link) => $link->getGroup()),
        ]);
    }

    public function fillState(array $state = [])
    {
        $this->parameters = $state['parameters'] ?? [];
        $this->newTab = $state['newTab'] ?? false;
        $this->route = $state['route'] ?? '';

        $this->form->fill(['data' => [
            'parameters' => $this->parameters,
        ]]);
    }

    public function updatedRoute($value)
    {
        if (blank($value)) {
            $this->description = null;
        }

        $this->parameters = [];
        $this->newTab = false;

        $this->form->fill();
    }

    public function cancel()
    {
        // Reset the form to the starting value
        foreach ($this->initialState as $key => $value) {
            $this->{$key} = $value;
        }

        // Close the modal
        $this->submit(false);
    }

    public function submit(bool $updateState = true)
    {
        if ($updateState && filled($this->route)) {
            $this->validate();
        }

        $this->dispatchBrowserEvent('filament-link-picker.submit', [
            'updateState' => $updateState,
            'statePath' => $this->statePath,
            'state' => filled($this->route) ? [
                'route' => $this->route,
                'parameters' => $this->parameters,
                'newTab' => $this->newTab,
            ] : null,
        ]);
    }

    protected function getFormSchema(): array
    {
        if (empty($this->route)) {
            return [];
        }

        $link = LinkCollection::firstByCleanRouteName($this->route);

        if (is_null($link)) {
            return [];
        }

        $this->description = $link->getDescription();
        $schema = $link->getSchema();

        // If the schema is empty, we'll check if there are any parameters
        if ($schema->isEmpty()) {
            $route = Route::getRoutes()->getByName($link->getRoute());

            $schema = collect($route->signatureParameters())
                ->filter(function (ReflectionParameter $parameter) {
                    return $parameter->getType() && class_exists(Reflector::getParameterClassName($parameter));
                })
                ->map(function (ReflectionParameter $parameter) {
                    $model = Reflector::getParameterClassName($parameter);

                    return Select::make("parameters.{$parameter->name}")
                        ->label(Str::title($parameter->name))
                        ->rules($parameter->allowsNull() ? '' : 'required')
                        ->searchable()
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
// @codeCoverageIgnoreEnd
