<?php

use Codedor\LinkPicker\Facades\LinkCollection;
use Codedor\LinkPicker\Link;

if (! function_exists('lroute')) {
    function lroute(null|array|Link $link, array $parameters = []): string|null
    {
        if (is_null($link)) {
            return null;
        }

        if ($link instanceof Link) {
            return $link->resolve($parameters);
        }

        return LinkCollection::route($link['route'])?->resolve($link['parameters']);
    }
}
