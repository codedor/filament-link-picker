<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        init () {
            window.addEventListener('filament-link-picker.submit', (e) => {
                if (e.detail.statePath !== '{{ $getStatePath() }}') return

                if (e.detail.updateState) {
                    this.state = e.detail.state
                }

                $dispatch('close-modal', {
                    id: 'filament-link-picker::picker-modal-{{ $getStatePath() }}'
                })
            })
        },
        openPicker () {
            $dispatch('open-modal', {
                id: 'filament-link-picker::picker-modal-{{ $getStatePath() }}'
            })
        },
    }">
        <div class="flex flex-col items-start gap-3">
            @if (filled($getState()))
                <div class="flex gap-3 items-center">
                    @php $route = lroute($getState()) @endphp
                    <a
                        href="{{ $route }}"
                        class="bg-gray-100 hover:bg-gray-200 transition rounded text-sm px-2 py-1"
                        target="_blank"
                    >
                        {{ $route }}
                    </a>

                    @if (! $isDisabled())
                        <button
                            type="button"
                            class="text-gray-600 hover:text-gray-700"
                            x-on:click.prevent="openPicker()"
                        >
                            <x-heroicon-o-pencil class="w-4 h-4" />
                        </button>

                        <button
                            type="button"
                            class="text-danger-600 hover:text-danger-700"
                            x-on:click="state = null"
                        >
                            <x-heroicon-o-trash class="w-4 h-4" />
                        </button>
                    @endif
                </div>
            @else
                @if (! $isDisabled())
                    <x-filament::button x-on:click.prevent="openPicker()">
                        {{ __('filament-link-picker.select link') }}
                    </x-filament::button>
                @else
                    <p>{{ __('filament-link-picker.no link selected') }}</p>
                @endif
            @endif
        </div>

        <x-filament::modal
            id="filament-link-picker::picker-modal-{{ $getStatePath() }}"
            width="3xl"
        >
            <x-slot name="header">
                <x-filament::modal.heading>
                    {{ __('filament-link-picker.select link') }}
                </x-filament::modal.heading>
            </x-slot>

            <livewire:filament-link-picker
                :state-path="$getStatePath()"
                :state="$getState()"
            />
        </x-filament::modal>
    </div>
</x-dynamic-component>
