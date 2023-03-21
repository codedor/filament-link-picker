<?php

namespace Codedor\LinkPicker\Tests;

use Codedor\LinkPicker\Providers\LinkPickerServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LinkPickerServiceProvider::class,
            FilamentServiceProvider::class,
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
        ];
    }
}
