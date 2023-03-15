<?php

namespace Codedor\LinkPicker;

use Illuminate\Support\Collection;

class LinkCollection extends Collection
{
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
