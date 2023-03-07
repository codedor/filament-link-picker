<?php

namespace Codedor\LinkPicker\Forms\Components;

use Codedor\LinkPicker\Facades\LinkCollection;
use Filament\Forms\Components\Field;

class LinkPickerInput extends Field
{
    protected string $view = 'filament-link-picker::forms.components.link-picker-input';

    public function setUp(): void
    {
        // Register some listeners
        $this->registerListeners([

        ]);
    }

    public function getLinkPickerOptions()
    {
        return LinkCollection::all();
    }

    public function getState()
    {
        // TODO: rewrite after saving code etc. has been created
        $state = parent::getState() ?? [];

        if (is_string($state)) {
            $state = LinkCollection::route($state);
        }

        return $state;
    }
}
