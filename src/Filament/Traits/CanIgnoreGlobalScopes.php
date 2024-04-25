<?php

namespace Codedor\LinkPicker\Filament\Traits;

use Closure;

trait CanIgnoreGlobalScopes
{
    public array|Closure $ignoredGlobalScopes = [];

    public function ignoredGlobalScopes(array|Closure $scopes)
    {
        $this->ignoredGlobalScopes = $scopes;

        return $this;
    }

    public function getIgnoredGlobalScopes(): array
    {
        return [
            ...config('filament-link-picker.ignored_global_scopes', []),
            ...$this->evaluate($this->ignoredGlobalScopes),
        ];
    }
}
