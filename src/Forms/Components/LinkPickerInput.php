<?php

namespace Codedor\LinkPicker\Forms\Components;

use Codedor\LinkPicker\Facades\LinkCollection;
use Filament\Forms\Components\Field;

class LinkPickerInput extends Field
{
    protected string $view = 'filament-link-picker::forms.components.link-picker-input';

    public function getLinkPickerOptions()
    {
        return LinkCollection::all();
    }

    public function getState()
    {
        $state = parent::getState() ?? [];

        if (is_string($state)) {
            $state = LinkCollection::route($state);
        }

        return $state;
    }
}
