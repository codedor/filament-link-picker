<?php

namespace Codedor\LinkPicker;

use Closure;
use Illuminate\Routing\ImplicitRouteBinding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Link
{
    protected null|string $description = null;

    protected null|string $group = null;

    protected null|Closure $buildUsing = null;

    protected null|Closure $schema = null;

    protected array $parameters = [];

    public function __construct(
        public string $routeName,
        public null|string $label = null,
    ) {
    }

    public static function make(string $routeName, null|string $label = null): self
    {
        return new self($routeName, $label);
    }

    public function routeName(string $routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function label(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? Str::of($this->getCleanRouteName())->after('.')->title();
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
        return $this->group ?? Str::of($this->getCleanRouteName())->before('.')->replace('-', ' ')->title();
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

    public function getCleanRouteName()
    {
        $route = $this->getRoute();

        if (app(PackageChecker::class)->localeCollectionClassExists() && ($route->wheres['translatable_prefix'] ?? false)) {
            return Str::after($this->routeName, $route->wheres['translatable_prefix'] . '.');
        }

        return $this->routeName;
    }

    public function getRoute()
    {
        return Route::getRoutes()->getByName($this->routeName);
    }

    public function build(null|array $parameters = null): string|null
    {
        $parameters ??= $this->getParameters();

        if ($this->buildUsing) {
            return call_user_func($this->buildUsing, $this->parameters($parameters));
        }

        $route = $this->resolveParameters($parameters);

        if (app(PackageChecker::class)->translateRouteFunctionExists()) {
            return translate_route($this->getCleanRouteName(), null, $route->parameters);
        }

        return route($this->routeName, $parameters);
    }

    public function resolveParameters(array $parameters)
    {
        $route = $this->getRoute();

        $route->parameters = $parameters;
        $bindings = $route->bindingFields();

        $route->setBindingFields([]);

        ImplicitRouteBinding::resolveForRoute(app(), $route);

        $route->setBindingFields($bindings);

        return $route;
    }
}
