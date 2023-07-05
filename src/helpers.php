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

        return LinkCollection::firstByCleanRouteName($link['route'])
            ?->build($link['parameters'] ?? []);
    }
}

if (! function_exists('parse_link_picker_json')) {
    function parse_link_picker_json(string $content): string
    {
        return preg_replace_callback(
            '/#link-picker=\[\[([^"]*)]]/',
            function ($matches) {
                $data = html_entity_decode($matches[1]);

                $json = json_decode($data, true);

                if (! $json) {
                    return '';
                }

                $url = lroute($json);

                if (array_key_exists('newTab', $json) && $json['newTab']) {
                    return $url . '" target="_blank';
                }

                return $url;
            },
            $content
        );
    }
}
