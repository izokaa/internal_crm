<x-filament-panels::page>
    {{-- We will render the interactive timeline component here --}}
    @livewire('opportunity-pipeline-timeline', ['opportunity' => $record])

    {{-- And then render the rest of the static infolist fields --}}
    {{ $this->infolist }}
</x-filament-panels::page>
