<?php

namespace Codedor\LinkPicker\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Str;

class ParsesLinkPicker
{
    public function handle($request, Closure $next)
    {
        if ($request->headers->has('X-LIVEWIRE') && $request->server('HTTP_REFERER')) {
            $referer = $request->server('HTTP_REFERER');
            $isFilament = collect(Filament::getPanels())->contains(function (Panel $panel) use ($referer) {
                return Str::startsWith($referer, $panel->getUrl());
            });

            if ($isFilament) {
                return $next($request);
            }
        }

        $response = $next($request);

        if ($response->getContent()) {
            $response->setContent(parse_link_picker_json($response->getContent()));
        }

        return $response;
    }
}
