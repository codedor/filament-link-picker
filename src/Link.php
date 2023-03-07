<?php

namespace Codedor\LinkPicker;

use Closure;

class Link
{
    public null|Closure $resolveUsing = null;

    public function __construct(
        public string $route,
        public null|string $label = null,
        public null|string $description = null,
        public array $schema = [],
    ) {
        $this->label ??= $this->route;
    }

    public static function make(string $route, null|string $label = null): self
    {
        return new self($route, $label);
    }

    public function label(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function schema(array $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function resolveUsing(Closure $closure)
    {
        $this->resolveUsing = $closure;

        return $this;
    }

    public function resolve(array $parameters = [])
    {
        if ($this->resolveUsing) {
            return call_user_func($this->resolveUsing, $parameters);
        }

        return route("en.filament-demotest.{$this->route}", $parameters);
    }
}
