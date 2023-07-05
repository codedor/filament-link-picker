<?php

beforeEach(function () {
    registerRoute();
    registerRouteWithoutParameters();

    mockPackageChecker();
});

it('will return empty string if content is empty', function () {
    $this->assertEquals('', parse_link_picker_json(''));
});

it('will return original content if regex does not match', function () {
    $content = '<a href="https://example.com">Example</a>';

    $this->assertEquals($content, parse_link_picker_json($content));
});

it('will return value with given content', function ($content, $attributes) {
    $content = is_array($content) ? htmlentities(json_encode($content)) : $content;

    expect(parse_link_picker_json("<a href=\"#link-picker=[[{$content}]]\">Example</a>"))
        ->toBe("<a {$attributes}>Example</a>");
})->with([
    'empty href' => [
        '',
        'href=""',
    ],
    'route name' => [
        [
            'route' => 'route.without-parameters',
            'parameters' => [],
        ],
        'href="http://localhost/route"',
    ],
    'route name with parameters' => [
        [
            'route' => 'route.name',
            'parameters' => [
                'parameter' => 'value',
            ],
            'newTab' => false,
        ],
        'href="http://localhost/route/value"',
    ],
    'route name opens new tab' => [
        [
            'route' => 'route.without-parameters',
            'parameters' => [],
            'newTab' => true,
        ],
        'href="http://localhost/route" target="_blank"',
    ],
]);
