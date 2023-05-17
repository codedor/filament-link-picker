<?php

namespace Codedor\LinkPicker;

use Closure;
use Codedor\LinkPicker\Facades\LinkCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Link
{
    public null|string $description = null;

    public null|string $group = null;

    public null|Closure $buildUsing = null;

    public null|Closure $schema = null;

    public array $parameters = [];

    public function __construct(
        public string $route,
        public null|string $label = null,
    ) {
        $this->route = Str::of($this->route)->replace(LinkCollection::getRoutePrefixes(), '')->trim('.');
        $this->label ??= Str::of($this->route)->after('.')->title();
        $this->group = Str::of($this->route)->before('.')->replace('-', ' ')->title();
    }

    public static function make(string $route, null|string $label = null): self
    {
        return new self($route, $label);
    }

    public function route(string $route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function label(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function schema(Closure $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function getSchema(): Collection
    {
        return Collection::wrap(
            is_null($this->schema) ? [] : call_user_func($this->schema)
        );
    }

    public function group(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function parameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function buildUsing(Closure $closure)
    {
        $this->buildUsing = $closure;

        return $this;
    }

    public function build(null|array $parameters = null): string|null
    {
        $parameters ??= $this->parameters ?? null;

        if ($this->buildUsing) {
            return call_user_func($this->buildUsing, $this->parameters($parameters));
        }

        return route($this->route, $parameters);
    }
}
