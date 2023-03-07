<?php

namespace Codedor\LinkPicker;

use Illuminate\Support\Collection;

class LinkCollection extends Collection
{
    public function addGroup(string $name, array $items)
    {
        return $this->add([
            'name' => $name,
            'items' => $items,
        ]);
    }

    public function routes(): self
    {
        return $this->pluck('items')->flatten();
    }

    public function route(string $routeName): null|Link
    {
        return $this->routes()->firstWhere('route', $routeName);
    }
}
