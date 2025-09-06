<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Opportunity;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;


class TestLivewire extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $record;

    public function mount(Opportunity $opportunity)
    {
        $this->record = $opportunity;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
        ]);
    }

    public function createActivity(): void
    {
        Activity::create([
            'type' => 'call',
            'label_id' => 10,
            'due_date' => now()->addDays(3)->toDateString(),
            'user_id' => 1,
            'opportunity_id' => $this->record->id,
            'statut' => 'To Do'
        ]);
        $this->dispatch('activityCreated');
        $this->dispatch('close-modal', id: 'call-modal');

        Notification::make()
            ->title('L\'appel créée avec succès!')
            ->success()
            ->send();
    }


    public function render()
    {
        return view('livewire.test-livewire');
    }
}
