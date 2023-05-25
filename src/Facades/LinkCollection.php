<?php

namespace Codedor\LinkPicker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Codedor\LinkPicker\LinkCollection routes()
 * @method static null | \Codedor\LinkPicker\Link route(string $routeName)
 * @method static \Codedor\LinkPicker\LinkCollection addLink(\Codedor\LinkPicker\Link $link)
 * @method static \Codedor\LinkPicker\LinkCollection addGroup(string $group, iterable $links)
 * @method static \Codedor\LinkPicker\Link firstByCleanRouteName(string $routeName)
 *
 * @see \Codedor\LinkPicker\LinkCollection
 */
class LinkCollection extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Codedor\LinkPicker\LinkCollection::class;
    }
}
