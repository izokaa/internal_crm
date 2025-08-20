<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::section
        collapsible
        collapsed={{false}}
        heading="Statistiques opérationnelles"
        class="mt-6"
    >
        <div class="grid grid-cols-2 gap-4 md:grid-cols-2 lg:grid-cols-2" wire:key="widgets-{{ $startDate }}-{{ $endDate }}">
            @foreach ($this->getWidgets() as $widgetClass)
            @livewire($widgetClass, [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
            ], key($widgetClass . '-' . $this->startDate . '-' . $this->endDate))
            @endforeach

        </div>
    </x-filament::section>
    <x-filament::section
        collapsible
        collapsed={{false}}
        heading="Statistiques financières"
        class="mt-6"
    >
        <div class="grid grid-cols-2 gap-4 md:grid-cols-2 lg:grid-cols-2" wire:key="widgets-{{ $startDate }}-{{ $endDate }}">
            @foreach ($this->getFinanceWidgets() as $widgetClass)
            @livewire($widgetClass, [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
            ], key($widgetClass . '-' . $this->startDate . '-' . $this->endDate))
            @endforeach

        </div>
    </x-filament::section>
</x-filament-panels::page>
