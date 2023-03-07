<?php

namespace Codedor\LinkPicker\Facades;

use Illuminate\Support\Facades\Facade;

class LinkCollection extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Codedor\LinkPicker\LinkCollection::class;
    }
}
