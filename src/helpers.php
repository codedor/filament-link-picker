<?php

use Codedor\LinkPicker\Facades\LinkCollection;
use Codedor\LinkPicker\Link;

if (! function_exists('lroute')) {
    function lroute(string|Link $link, array $parameters = []): string
    {
        if ($link instanceof Link) {
            return $link->resolve($parameters);
        }

        return LinkCollection::route($link)->resolve($parameters);
    }
}
