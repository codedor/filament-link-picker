<?php

use Codedor\LinkPicker\Facades\LinkCollection;
use Codedor\LinkPicker\Link;
use Illuminate\Support\HtmlString;

if (! function_exists('lroute')) {
    function lroute(null|string|array|Link $link, null|array $parameters = null, bool $withTarget = true): HtmlString|string|null
    {
        if (is_string($link)) {
            $link = json_decode($link, true);
        }

        if (blank($link)) {
            return null;
        }

        if ($link instanceof Link) {
            return $link->build($parameters);
        }

        $url = LinkCollection::firstByCleanRouteName($link['route'])
            ?->build($link['parameters'] ?? []);

        if ($withTarget && ($link['newTab'] ?? false)) {
            $url .= '" target="_blank';
        }

        return new HtmlString($url);
    }
}
