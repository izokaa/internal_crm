<div>
    @if($this->addingComment)
        <form wire:submit.prevent="create" wire:loading.attr="disabled" class="space-y-4">
            {{ $this->form }}
            <x-filament::button type="submit">
                Soumettre
            </x-filament::button>
            <x-filament::button type="button" color="gray" wire:click="showForm(false)">
                Annuler
            </x-filament::button>
        </form>
    @else
        <x-filament::input.wrapper
                :inline-prefix="true"
                prefix-icon="heroicon-o-chat-bubble-bottom-center-text">
            <x-filament::input
                    :placeholder="$this->replyTo?->getKey() ? 'Ajouter une réponse' : 'Ajouter un nouveau commentaire'"
                    type="text"
                    wire:click.prevent.stop="showForm(true)"
                    :readonly="true"
            />
        </x-filament::input.wrapper>
    @endif
    <x-filament-actions::modals />
</div>
