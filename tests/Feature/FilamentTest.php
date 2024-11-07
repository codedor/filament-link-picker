<?php

use Codedor\LinkPicker\Filament\LinkPickerInput;
use Codedor\LinkPicker\Tests\Fixtures\Forms\Livewire;
use Codedor\LinkPicker\Tests\Fixtures\Models\TestModel;
use Filament\Forms\ComponentContainer;

beforeEach(function () {
    $this->field = LinkPickerInput::make('link')
        ->container(ComponentContainer::make(Livewire::make()))
        ->model(TestModel::class);
});

it('can get the current state', function () {
    registerRoute();

    $this->field->state([
        'route' => 'route.name',
        'parameters' => [],
        'newTab' => false,
    ]);

    expect($this->field)
        ->getRouteDescription()->toBe([
            'group' => 'Route',
            'label' => 'Name',
            'parameters' => [],
            'newTab' => false,
            'custom' => false,
        ]);
});
