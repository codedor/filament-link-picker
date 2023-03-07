<?php

namespace Codedor\LinkPicker\Http\Livewire;

use Codedor\LinkPicker\Facades\LinkCollection;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class LinkPicker extends Component implements HasForms
{
    use InteractsWithForms;

    public string $statePath;

    public string $state = '';

    public array $fields = [];

    public bool $newTab = false;

    public function render()
    {
        return view('filament-link-picker::livewire.link-picker', [
            'routes' => LinkCollection::all(),
        ]);
    }

    public function updatedState()
    {
        $this->fields = [];
        $this->newTab = false;

        $this->form->fill();
    }

    public function submit()
    {
        $this->dispatchBrowserEvent('filament-link-picker.submit', [
            'statePath' => $this->statePath,
            'state' => [
                'route' => $this->state,
                'parameters' => $this->fields,
                'newTab' => $this->newTab,
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        $schema = LinkCollection::route($this->state)?->schema ?? [];

        return array_merge($schema, [
            Checkbox::make('newTab')
                ->label('Open in a new tab'),
        ]);
    }
}
