<?php

use Codedor\LinkPicker\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function registerRoute(): void
{
    Route::get('route/{parameter}', fn () => '')
        ->name('route.name')
        ->linkPicker();
}
