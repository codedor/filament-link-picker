<?php

use Codedor\LinkPicker\Link;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    registerRoute();

    $this->parameters = ['parameter' => 'value'];
    $this->routeToCheck = route('route.name', $this->parameters);
});

it('can use lroute helper with a link object', function () {
    $link = Link::make('route.name')->parameters($this->parameters);
    $route = lroute($link);

    expect($route)->toBe($this->routeToCheck);
});

it('can use lroute helper with an array', function () {
    $route = lroute([
        'route' => 'route.name',
        'parameters' => $this->parameters,
    ]);

    expect($route)->toBe($this->routeToCheck);
});

it('returns null when a null is given', function () {
    expect(lroute(null))->toBeNull();
});
