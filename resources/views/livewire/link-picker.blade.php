<div>
    <select wire:model="state">
        <option value="">Select a link</option>
        @foreach ($routes as $name => $group)
            <optgroup label="{{ $name }}">
                @foreach ($group as $link)
                    <option value="{{ $link->route }}">
                        {{ $link->label }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>

    @if ($state)
        <div wire:key="{{ $state }}">
            {{ $this->form }}
        </div>

        <button
            type="button"
            class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
            wire:click.prevent="submit"
        >
            {{ __('filament-link-picker.confirm link') }}
        </button>
    @endif
</div>
