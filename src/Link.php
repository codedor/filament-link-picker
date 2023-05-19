<?php

namespace Codedor\LinkPicker;

use Closure;
use Codedor\LocaleCollection\Facades\LocaleCollection;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\ImplicitRouteBinding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Reflector;
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
        return $this->label ?? Str::of($this->getCleanRoute())->after('.')->title();
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
        return $this->group ?? Str::of($this->getCleanRoute())->before('.')->replace('-', ' ')->title();
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

    public function getParameter(string $key): mixed
    {
        return $this->getParameters()[$key] ?? null;
    }

    public function buildUsing(Closure $closure)
    {
        $this->buildUsing = $closure;

        return $this;
    }

    public function getCleanRoute()
    {
        $route = Route::getRoutes()->getByName($this->route);
        if (class_exists(LocaleCollection::class) && ($route->wheres['translatable_prefix'] ?? false)) {
            return Str::after($this->route, $route->wheres['translatable_prefix'] . '.');
        }

        return $this->route;
    }

    public function build(null|array $parameters = null): string|null
    {
        $parameters ??= $this->getParameters();

        if ($this->buildUsing) {
            return call_user_func($this->buildUsing, $this->parameters($parameters));
        }


        $route = $this->resolveParameters($parameters);

        if (function_exists('translate_route')) {
            return translate_route($this->getCleanRoute(), null, $route->parameters);
        }

        return route($this->route, $parameters);
    }

    public function resolveParameters(array $parameters)
    {
        $route = Route::getRoutes()->getByName($this->route);

        $route->parameters = $parameters;
        $bindings = $route->bindingFields();

        $route->setBindingFields([]);

        ImplicitRouteBinding::resolveForRoute(app(), $route);

        $route->setBindingFields($bindings);

        return $route;
    }
}
