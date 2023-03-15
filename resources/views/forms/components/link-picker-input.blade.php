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

                    <button
                        type="button"
                        class="text-danger-600 hover:text-danger-700"
                        x-on:click="state = null"
                    >
                        <x-heroicon-o-trash class="w-4 h-4" />
                    </button>
                </div>
            @endif

            <button
                type="button"
                class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
                x-on:click.prevent="$dispatch('open-modal', {
                    id: 'filament-link-picker::picker-modal-{{ $getStatePath() }}'
                })"
            >
                {{ __('filament-link-picker.select link') }}
            </button>
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
