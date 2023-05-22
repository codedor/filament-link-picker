<?php

namespace Codedor\LinkPicker;

use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
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

    /**
     * @return static<TKey, TValue>
     */
    public function routes(): self
    {
        return $this->flatten();
    }

    public function route(string $routeName): null|Link
    {
        return $this->routes()->first(function (Link $link) use ($routeName) {
            return $link->getRouteName() === $routeName;
        });
    }

    public function firstByCleanRouteName(string $routeName)
    {
        return $this->first(fn (Link $link) => $link->getCleanRouteName() === $routeName);
    }
}
