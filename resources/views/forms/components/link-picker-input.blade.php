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
    <div
        wire:key="filament-link-picker::picker-modal-{{ $getStatePath() }}"
        x-data="{
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
        }"
    >
        <div class="flex flex-col items-start gap-3">
            @if (filled($getState()))
                <div class="flex gap-3 items-center">
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

                    <ul class="bg-gray-100 hover:bg-gray-200 transition rounded text-sm px-2 py-1">
                        @php($selection = $getSelectedDescription())

                        <li>
                            <strong>
                                {{ __('filament-link-picker::input.selected link') }}:
                            </strong>

                            {{ $selection['label'] }}
                        </li>

                        @if (filled($selection['parameters']))
                            <li>
                                <strong>
                                    {{ __('filament-link-picker::input.selected parameters') }}:
                                </strong>

                                <ul>
                                    @foreach ($selection['parameters'] as $key => $value)
                                        <li>
                                            {{ ucfirst($key) }}: {{ $value }}
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        @if ($selection['newTab'] ?? false)
                            <li>
                                <strong>
                                    {{ __('filament-link-picker::input.selected open in new tab') }}
                                </strong>
                            </li>
                        @endif
                    </ul>
                </div>
            @else
                @if (! $isDisabled())
                    <x-filament::button x-on:click.prevent="openPicker()">
                        {{ __('filament-link-picker::input.select link') }}
                    </x-filament::button>
                @else
                    <p>{{ __('filament-link-picker::input.no link selected') }}</p>
                @endif
            @endif
        </div>

        <div style="height: 0; overflow: hidden">
        <x-filament::modal
            id="filament-link-picker::picker-modal-{{ $getStatePath() }}"
            width="3xl"
        >
            <x-slot name="header">
                <x-filament::modal.heading>
                    {{ __('filament-link-picker::input.select link') }}
                </x-filament::modal.heading>
            </x-slot>

            <livewire:filament-link-picker
                wire:key="filament-link-picker::picker-modal-{{ $getStatePath() }}"
                :state-path="$getStatePath()"
                :state="$getState()"
            />
        </x-filament::modal>
        </div>
    </div>
</x-dynamic-component>
