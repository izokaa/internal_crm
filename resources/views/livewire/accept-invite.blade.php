<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="create">
        {{ $this->form  }}
        <button type="submit" style="background-color: orange; border-radius: 10px; padding: .5rem;">soumettre</button>
    </x-filament-panels::form>
</x-filament-panels::page.simple>
