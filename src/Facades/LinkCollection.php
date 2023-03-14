<?php

namespace Codedor\LinkPicker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Codedor\LinkPicker\LinkCollection routes()
 * @method static null | \Codedor\LinkPicker\Link route(string $routeName)
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
