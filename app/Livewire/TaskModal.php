<?php

namespace App\Livewire;

use App\Enums\ActivityStatut;
use App\Models\Activity;
use App\Models\Label;
use App\Models\User;
use App\Models\Opportunity;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;


class TaskModal extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $record;
    public $due_date;
    public $label_id;
    public $user_id;
    public $statut;
    public $prioritaire = 0;


    public function mount(Opportunity $opportunity)
    {
        $this->record = $opportunity;
        $this->statut = ActivityStatut::TODO->value;
        $this->user_id = auth()->id();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Checkbox::make('prioritaire')
                    ->extraAttributes(['class' => 'h-6 w-6'])
                    ->label('Priorité'),
                \Filament\Forms\Components\Grid::make(2)
                    ->schema([
                        Select::make('user_id')
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
                            ->preload()
                            ->default(auth()->id()),
                        \Filament\Forms\Components\Select::make('label_id')
                            ->label('Label')
                            ->options(Label::taskLabels()->pluck('value', 'id'))
                            ->required()
                            ->searchable(),
                    ]),
                \Filament\Forms\Components\Grid::make(2)
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->label('Date'),
                        Select::make('statut')
                            ->label('Statut')
                            ->options(ActivityStatut::class),

                    ])
            ]);
    }

    public function createTask(): void
    {
        $task = [
            'type' => 'task',
            'user_id' => $this->user_id,
            'prioritaire' => $this->prioritaire,
            'label_id' => $this->label_id,
            'statut' => $this->statut,
            'due_date' => $this->due_date,
            'opportunity_id' => $this->record->id
        ];
        Activity::create($task);
        $this->dispatch('activityCreated');
        $this->dispatch('close-modal', id: 'activity-modal');

        Notification::make()
            ->title('La tâche créée avec succès!')
            ->success()
            ->send();
    }


    public function render()
    {
        return view('livewire.task-modal');
    }
}
