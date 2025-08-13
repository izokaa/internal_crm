<x-filament-panels::page>
    {{ $this->form }}

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3" wire:key="widgets-{{ $startDate }}-{{ $endDate }}">
        @foreach ($this->getWidgets() as $widgetClass)
            @livewire($widgetClass, [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate
            ], key($widgetClass . '-' . $this->startDate . '-' . $this->endDate))
        @endforeach
    </div>
</x-filament-panels::page>
