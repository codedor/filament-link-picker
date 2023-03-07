<?php

namespace Codedor\LinkPicker\Providers;

use Codedor\LinkPicker\Http\Livewire\LinkPicker;
use Codedor\LinkPicker\LinkCollection;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LinkPickerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-link-picker')
            ->setBasePath(__DIR__ . '/../')
            ->hasConfigFile()
            ->hasViews('filament-link-picker');
    }

    public function registeringPackage()
    {
        $this->app->singleton(LinkCollection::class, function () {
            return new LinkCollection;
        });
    }

    public function bootingPackage()
    {
        Filament::serving(function () {
            Livewire::component('filament-link-picker', LinkPicker::class);
        });

        $this->registerLinks();
    }

    public function registerLinks()
    {
        //
    }
}
