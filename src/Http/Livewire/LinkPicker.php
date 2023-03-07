<?php

namespace Codedor\LinkPicker\Http\Livewire;

use Codedor\LinkPicker\Facades\LinkCollection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class LinkPicker extends Component implements HasForms
{
    use InteractsWithForms;

    public string $statePath;

    public string $state = '';

    public array $fields = [];

    public function render()
    {
        return view('filament-link-picker::livewire.link-picker', [
            'routes' => LinkCollection::all(),
        ]);
    }

    public function submit()
    {
        $this->dispatchBrowserEvent('filament-link-picker.submit', [
            'statePath' => $this->statePath,
            'state' => [
                'route' => $this->state,
                'parameters' => $this->fields,
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        return LinkCollection::route($this->state)?->schema ?? [];
    }
}
