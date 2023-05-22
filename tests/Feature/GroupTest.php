<?php

use Codedor\LinkPicker\Facades\LinkCollection;
use Codedor\LinkPicker\Forms\Components\LinkPickerInput;
use Codedor\LinkPicker\Link;
use Codedor\LinkPicker\Tests\Fixtures\Forms\Livewire;
use Codedor\LinkPicker\Tests\Fixtures\Models\TestModel;
use Filament\Forms\ComponentContainer;

beforeEach(function () {
    $this->field = LinkPickerInput::make('link')
        ->container(ComponentContainer::make(Livewire::make()))
        ->model(TestModel::class);

    LinkCollection::add(Link::make('nl.home'));
    LinkCollection::add(Link::make('en.home'));
    LinkCollection::add(Link::make('nl.news.index'));
    LinkCollection::add(Link::make('en.news.index'));
});

// it('can group routes', function () {
//     // assert routes are grouped (count in link picker is 2 instead of 4 when grouped)
//     expect(LinkCollection::unique('route')->groupBy('group')->flatten())
//         // ->toHaveCount(2)
//         ->sequence(
//             function ($link) {
//                 $link->getRoute()->toBe('nl.home');
//             },
//             function ($link) {
//                 $link->getRoute()->toBe('news.index');
//             },
//         );
// });
