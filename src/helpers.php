<?php

use Codedor\LinkPicker\Facades\LinkCollection;
use Codedor\LinkPicker\Link;

if (! function_exists('lroute')) {
    function lroute(null|array|Link $link, null|array $parameters = null): string|null
    {
        if (blank($link)) {
            return null;
        }

        if ($link instanceof Link) {
            return $link->build($parameters);
        }

        return LinkCollection::first(fn (Link $linkObject) => $link['route'] === $linkObject->getCleanRoute())
            ?->build($link['parameters'] ?? []);
    }
}
