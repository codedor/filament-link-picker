<?php

use Codedor\LinkPicker\Forms\Components\LinkPickerInput;
use Codedor\LinkPicker\Link;
use Codedor\LinkPicker\Tests\Fixtures\Forms\Livewire;
use Codedor\LinkPicker\Tests\Fixtures\Models\TestModel;
use Filament\Forms\ComponentContainer;

beforeEach(function () {
    $this->field = LinkPickerInput::make('link')
        ->container(ComponentContainer::make(Livewire::make()))
        ->model(TestModel::class);
});

it('can set and get the options', function () {
    expect($this->field)->getLinkPickerOptions()->toHaveCount(0);

    registerRoute();

    expect($this->field)->getLinkPickerOptions()->toHaveCount(1);
});

it('can get the current state', function () {
    registerRoute();

    $this->field->getLivewire()->form->fill([
        'link' => 'route.name',
    ]);

    expect($this->field)
        ->getState()->toBeInstanceOf(Link::class)
        ->getState()->getRoute()->toBe('route.name');
});
