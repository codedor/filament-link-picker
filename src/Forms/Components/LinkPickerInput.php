<?php

namespace Codedor\LinkPicker\Forms\Components;

use Codedor\LinkPicker\Facades\LinkCollection;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;

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

    public function getSelectedDescription()
    {
        $state = $this->getState();
        $route = LinkCollection::cleanRoute($state['route']);
        if (! $route) {
            return [];
        }

        $route->parameters($state['parameters'] ?? []);

        $parameters = $route->getParameters();
        $resolvedRoute = $route->resolveParameters($parameters);

        foreach ($resolvedRoute->parameters as $key => $value) {
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
}
