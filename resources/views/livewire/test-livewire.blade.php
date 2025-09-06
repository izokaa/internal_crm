    <div>
        <x-filament::modal icon="heroicon-o-phone" icon-color="success" :close-button="true" id="call-modal">
            <x-slot name="heading">
                Programmer un appel
            </x-slot>
            <x-slot name="trigger">
                <x-filament::button style="background-color: green">
                    Appel
                </x-filament::button>
            </x-slot>

            {{ $this->form }}
            <button class="bg-blue-500 w-32 rounded-sm mx-auto" wire:click="createActivity">Créer </button>
        </x-filament::modal>
    </div>
