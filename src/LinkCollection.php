<?php

namespace Codedor\LinkPicker;

use Illuminate\Support\Collection;

class LinkCollection extends Collection
{
    protected array $routePrefixes = [];

    public function routePrefixes(array $routePrefixes): self
    {
        $this->routePrefixes = $routePrefixes;

        return $this;
    }

    public function getRoutePrefixes(): array
    {
        return $this->routePrefixes;
    }

    public function addLink(Link $link): self
    {
        $this->add($link);

        return $this;
    }

    public function addGroup(string $group, iterable $links): self
    {
        foreach ($links as $link) {
            $this->add($link->group($group));
        }

        return $this;
    }

    public function routes(): self
    {
        return $this->flatten();
    }

    public function route(string $routeName): null|Link
    {
        return $this->routes()->firstWhere('route', $routeName);
    }
}
