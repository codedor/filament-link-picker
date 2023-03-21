<?php

use Codedor\LinkPicker\Forms\Components\LinkPickerInput;
use Codedor\LinkPicker\Tests\Fixtures\Forms\Livewire;
use Codedor\LinkPicker\Tests\Fixtures\Models\TestModel;
use Filament\Events\ServingFilament;
use Filament\Forms\ComponentContainer;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->hasDispatchedSuccessfully = is_array(
        Event::dispatch(ServingFilament::class)
    );
});

it('can mount on filament', function () {
    expect($this->hasDispatchedSuccessfully)->toBeTrue();
});
