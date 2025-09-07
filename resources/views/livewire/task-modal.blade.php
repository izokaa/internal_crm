<x-filament::modal width="3xl" :close-button="true" id="activity-modal" alignment="center">
    <x-slot name="heading">
        Programmer une tâche
    </x-slot>
    <x-slot name="trigger">
        <button
            class="whitespace-nowrap flex items-center gap-2 py-2 px-4 rounded-md font-medium text-sm focus:outline-none s-center space-x-2 transition-colors duration-200"
            style="background-color: #3B82F6; color: #FFFFFF; margin-right: 1rem;">
            <x-heroicon-o-clipboard-document-check class="h-5 w-5" />
            <span>Tâche</span>
        </button>
    </x-slot>

    {{ $this->form }}
    <x-slot name="footer">
        <x-filament::button class="mx-auto" wire:click="createTask">Créer </x-filament::button>
    </x-slot>
</x-filament::modal>
