<?php

namespace Codedor\LinkPicker\Http\Middleware;

use Closure;

class ParsesLinkPicker
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $locale = $request->segment(1);

        // Handle livewire requests
        if (request()->hasHeader('X-Livewire') && $request->has('locale')) {
            $locale = $request->get('locale');
        }

        if ($response->getContent()) {
            $response->setContent(parse_link_picker_json($response->getContent()));
        }

        return $response;
    }
}
