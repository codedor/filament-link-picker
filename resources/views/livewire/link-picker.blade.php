<div>
    <div class="w-full py-6" wire:loading>
        <x-filament-support::loading-indicator
            class="w-8 h-8 mx-auto text-primary-500"
        />
    </div>

    <div wire:loading.remove>
        {{-- Make sure we don't submit the main form when pressing enter --}}
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <label class="
                    filament-forms-field-wrapper-label inline-flex items-center
                    text-sm font-medium leading-4 text-gray-700
                ">
                    {{ __('filament-link-picker.chosen route') }}
                </label>

                <select
                    wire:model="route"
                    class="
                        text-gray-900 block w-full transition duration-75 rounded-lg shadow-sm outline-none
                        focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500
                        disabled:opacity-70 border-gray-300
                    "
                >
                    <option value="">
                        {{ __('filament-link-picker.choose a route') }}
                    </option>

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

                @if (filled($description))
                    <p class="text-sm opacity-50 ml-1 -mt-1">{{ $description }}</p>
                @endif
            </div>

            @if (filled($route))
                <div>
                    {{ $this->form }}
                </div>
            @endif

            <x-filament::modal.actions>
                <x-filament::button wire:click.prevent="submit">
                    {{ __('filament-link-picker.confirm link') }}
                </x-filament::button>

                <x-filament::button wire:click.prevent="cancel" color="secondary">
                    {{ __('filament-link-picker.cancel pick') }}
                </x-filament::button>
            </x-filament::modal.actions>
        </div>
    </div>
</div>
