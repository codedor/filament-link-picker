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
        state: $wire.entangle('{{ $getStatePath() }}').defer,
        init () {
            window.addEventListener('filament-link-picker.submit', (e) => {
                if (e.detail.statePath !== '{{ $getStatePath() }}') return

                this.state = e.detail.state

                $dispatch('close-modal', {
                    id: 'filament-link-picker::picker-modal-{{ $getStatePath() }}'
                })
            })
        },
    }">
        <button
            type="button"
            class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
            x-on:click.prevent="$dispatch('open-modal', {
                id: 'filament-link-picker::picker-modal-{{ $getStatePath() }}'
            })"
        >
            {{ __('filament-link-picker.select link') }}
        </button>

        <x-filament::modal id="filament-link-picker::picker-modal-{{ $getStatePath() }}">
            <livewire:filament-link-picker
                :state-path="$getStatePath()"
            />
        </x-filament::modal>
    </div>
</x-dynamic-component>
