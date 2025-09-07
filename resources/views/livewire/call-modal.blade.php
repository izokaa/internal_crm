<x-filament::modal width="3xl" :close-button="true" id="activity-modal" icon="heroicon-o-phone" icon-color="success"
    alignment="center">
    <x-slot name="heading">
        Programmer un appel
    </x-slot>
    <x-slot name="trigger">

        <button
            class="whitespace-nowrap py-2 px-4 rounded-md font-medium text-sm focus:outline-none flex items-center gap-2 space-x-2 transition-colors duration-200"
            style="background-color: #22C55E; color: #FFFFFF; ">
            <span>Appel</span>
            <x-heroicon-o-phone class="h-5 w-5" />
        </button>
    </x-slot>

    {{ $this->form }}
    <x-slot name="footer">
        <x-filament::button class="mx-auto" wire:click="createCall">Créer </x-filament::button>
    </x-slot>
</x-filament::modal>
