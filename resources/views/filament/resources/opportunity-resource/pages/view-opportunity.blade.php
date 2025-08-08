<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{-- Timeline Section --}}
        <x-filament::section>
            <x-slot name="heading">
                Pipeline et Étape Actuelle
            </x-slot>
            @livewire('opportunity-pipeline-timeline', ['opportunity' => $record])
        </x-filament::section>

        {{ $this->form }}
    </x-filament-panels::form>
</x-filament-panels::page>
