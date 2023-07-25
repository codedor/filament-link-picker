<?php

namespace Codedor\LinkPicker\Providers;

use Codedor\LinkPicker\Facades\LinkCollection as FacadesLinkCollection;
use Codedor\LinkPicker\Http\Livewire\LinkPicker;
use Codedor\LinkPicker\Link;
use Codedor\LinkPicker\LinkCollection;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Routing\Route;
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
            ->hasViews('filament-link-picker')
            ->hasTranslations();
    }

    public function registeringPackage()
    {
        $this->app->singleton(LinkCollection::class, function () {
            return new LinkCollection;
        });
    }

    public function bootingPackage()
    {
        Route::macro('linkPicker', function (callable $callback = null) {
            /** @var \Illuminate\Routing\Route $this */
            $link = new Link($this->getName());

            FacadesLinkCollection::add(
                $callback ? call_user_func($callback, $link) : $link
            );

            return $this;
        });
    }
}
