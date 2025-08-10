<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Enums\ActivityStatut;
use App\Filament\Resources\OpportunityResource;
use App\Models\Activity;
use App\Models\Label;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewOpportunityDetails extends ViewRecord
{
    protected static string $resource = OpportunityResource::class;

    protected static string $view = 'filament.resources.opportunity-resource.pages.view-opportunity-details';

    public function infolist(Infolist $infolist): Infolist
    {
        return OpportunityResource::infolist($infolist);
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Nouvelle Tâche')
                ->form([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('title')
                                ->label('Titre')
                                ->required(),
                            Select::make('label_id')
                                ->label('Label')
                                ->options(Label::taskLabels()->pluck('value', 'id'))
                                ->required()
                                ->searchable(),
                        ]),
                    Checkbox::make('prioritaire')
                        ->extraAttributes(['class' => 'h-6 w-6'])
                        ->label('Priorité'),
                    Select::make('user_id')
                        ->relationship('user')
                        ->options(User::all()->map(function (User $user) {
                            if ($user->id == auth()->id()) {
                                $newUser = $user;
                                $newUser->name = $newUser->name . ' (moi)';
                                return $newUser;
                            } else {
                                return $user;
                            }
                        })->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->label('Responsable'),
                    DatePicker::make('due_date')
                        ->label('Date d\'échéance'),
                    MarkdownEditor::make('description')
                        ->label('Description'),
                ])
                ->action(function (array $data) {
                    Activity::create([
                        'opportunity_id' => $this->record->id,
                        'type' => 'task',
                        'statut' => ActivityStatut::TODO,
                        'titre' => $data['title'],
                        'prioritaire' => $data['prioritaire'],
                        'user_id' => auth()->id(),
                        'description' => $data['description'],
                        'due_date' => $data['due_date'],
                        'label_id' => $data['label_id']
                    ]);

                    Notification::make()
                        ->title('Tâche créée avec succès!')
                        ->success()
                        ->send();
                })
                ->modalSubmitActionLabel('Créer la tâche')
                ->modalCancelActionLabel('Annuler'),
            Action::make('createEvent')
                ->label('Programmer un évènement')
                ->form([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required(),
                    Grid::make(2)
                        ->schema([
                            Select::make('user_id')
                            ->label('Prioritaire')
                                ->options(User::all()->map(function (User $user) {
                                    if ($user->id == auth()->id()) {
                                        $newUser = $user;
                                        $newUser->name = $newUser->name . ' (moi)';
                                        return $newUser;
                                    } else {
                                        return $user;
                                    }
                                })->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload() ,
                            Select::make('label_id')
                                ->label('Label')
                                ->options(Label::eventLabels()->pluck('value', 'id'))
                                ->required()
                                ->searchable(),
                        ]),
                    Checkbox::make('is_all_day')
                        ->extraAttributes(['class' => 'h-6 w-6'])
                        ->label('Toute la journée')
                        ->live(),
                    Grid::make(2)
                        ->schema([
                            DateTimePicker::make('date_debut')
                                ->label('Date début')
                                ->withoutTime(fn ($get) => $get('is_all_day'))
                                ->required(),
                            DateTimePicker::make('date_fin')
                                ->label('Date fin')
                                ->withoutTime(fn ($get) => $get('is_all_day'))
                                ->required(),
                        ]),
                    MarkdownEditor::make('description')
                        ->label('Description'),
                ])
                ->action(function (array $data) {
                    $activityData = [
                        'opportunity_id' => $this->record->id,
                        'type' => 'event',
                        // NOTE: prioritaire
                        'user_id' => auth()->id(),
                        'titre' => $data['title'],
                        'description' => $data['description'],
                        'label_id' => $data['label_id'],
                        'is_all_day' => $data['is_all_day'],
                    ];

                    if ($data['is_all_day']) {
                        $activityData['date_debut'] = Carbon::parse($data['date_debut'])->startOfDay();
                        $activityData['date_fin'] = Carbon::parse($data['date_fin'])->endOfDay();
                    } else {
                        $activityData['date_debut'] = $data['date_debut'];
                        $activityData['date_fin'] = $data['date_fin'];
                    }

                    Activity::create($activityData);

                    Notification::make()
                        ->title('Évènement créé avec succès!')
                        ->success()
                        ->send();
                }),

            Action::make('createCall')
                ->label('Programmer un appel')
                ->form([
                    Checkbox::make('prioritaire')
                        ->extraAttributes(['class' => 'h-6 w-6'])
                        ->label('Priorité'),
                    Grid::make(2)
                        ->schema([
                            Select::make('user_id')
                            ->label('Responsable')
                                ->options(User::all()->map(function (User $user) {
                                    if ($user->id == auth()->id()) {
                                        $newUser = $user;
                                        $newUser->name = $newUser->name . ' (moi)';
                                        return $newUser;
                                    } else {
                                        return $user;
                                    }
                                })->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload() ,
                            Select::make('label_id')
                                ->label('Label')
                                ->options(Label::eventLabels()->pluck('value', 'id'))
                                ->required()
                                ->searchable(),
                        ]),
                    DatePicker::make('due_date')
                        ->label('Date'),
                    MarkdownEditor::make('description')
                        ->label('Note'),
                ])
                ->action((function (array $data) {
                    Activity::create([
                        'opportunity_id' => $this->record->id,
                        'type' => 'call',
                        'statut' => ActivityStatut::TODO,
                        'prioritaire' => $data['prioritaire'],
                        // NOTE: responsable
                        'user_id' => auth()->id(),
                        'description' => $data['description'],
                        'due_date' => $data['due_date'],
                        'label_id' => $data['label_id']
                    ]);

                    Notification::make()
                        ->title('Tâche créée avec succès!')
                        ->success()
                        ->send();
                })),
        ];
    }

    public $commentContent;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MarkdownEditor::make('commentContent')
                    ->label('Commentaire')
                    ->placeholder('Écrivez votre commentaire ici...')
                    // ->id('commentEditor')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function createComment()
    {
        $data = $this->form->getState();

        // Here you would save the comment to the database
        // For now, just a placeholder
        session()->flash('message', 'Commentaire envoyé : ' . $data['commentContent']);

        $this->form->fill(); // Clear the form after submission
    }

    public function clearComment()
    {
        $this->form->fill(); // Clear the form
    }

    public function getFormStatePath(): ?string
    {
        return null;
    }
}
