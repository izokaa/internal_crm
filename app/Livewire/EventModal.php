<?php

namespace App\Livewire;

use App\Enums\ActivityStatut;
use App\Models\Activity;
use App\Models\Opportunity;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use App\Models\User;
use App\Models\Label;
use Filament\Notifications\Notification;

class EventModal extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;


    public $record;
    public $date_debut;
    public $date_fin;
    public $label_id;
    public $user_id;
    public $statut;

    public function mount(Opportunity $opportunity)
    {
        $this->record = $opportunity;
        $this->user_id = auth()->id();
        $this->statut = ActivityStatut::TODO->value;
    }

    public function form(Form $form): ?Form
    {
        return $form->schema([
            \Filament\Forms\Components\Grid::make(2)
                ->schema([
                    \Filament\Forms\Components\Select::make('user_id')
                        ->label('Responsable')
                        ->options(function () {
                            return User::all()->mapWithKeys(function (User $user) {
                                $name = $user->id == auth()->id()
                                    ? $user->name . ' (moi)'
                                    : $user->name;
                                return [$user->id => $name];
                            });
                        })
                        ->searchable()
                        ->preload(),
                    \Filament\Forms\Components\Select::make('label_id')
                        ->label('Label')
                        ->options(Label::eventLabels()->pluck('value', 'id'))
                        ->required()
                        ->searchable(),
                ]),
            \Filament\Forms\Components\Grid::make(2)
                ->schema([
                    \Filament\Forms\Components\DateTimePicker::make('date_debut')
                        ->label('Date début')
                        ->withoutTime(true)
                        ->required(),
                    \Filament\Forms\Components\DateTimePicker::make('date_fin')
                        ->label('Date fin')
                        ->withoutTime(true)
                        ->required(),
                ]),
            \Filament\Forms\Components\Select::make('statut')
                ->label('Statut')
                ->options(ActivityStatut::class),

        ]);
    }

    public function createEvent()
    {
        $event = [
            'type' => 'call',
            'user_id' => $this->user_id,
            'label_id' => $this->label_id,
            'statut' => $this->statut,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'opportunity_id' => $this->record->id
        ];
        Activity::create($event);
        $this->dispatch('activityCreated');
        $this->dispatch('close-modal', id: 'activity-modal');

        Notification::make()
            ->title('L\'évènement créée avec succès!')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.event-modal');
    }
}
