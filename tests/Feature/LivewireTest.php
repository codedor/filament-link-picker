<?php

use Filament\Events\ServingFilament;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->hasDispatchedSuccessfully = is_array(
        Event::dispatch(ServingFilament::class)
    );
});

it('can mount on filament', function () {
    expect($this->hasDispatchedSuccessfully)->toBeTrue();
});
