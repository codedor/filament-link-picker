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
        if (is_filament_livewire_route($request)) {
            return $next($request);
        }

        $response = $next($request);

        if ($response->getContent()) {
            $response->setContent(parse_link_picker_json($response->getContent()));
        }

        return $response;
    }
}
