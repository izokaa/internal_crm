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
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
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
                        'user_id' => $data['user_id'], // ← Utilisez la valeur du formulaire
                        'description' => $data['description'],
                        'due_date' => $data['due_date'],
                        'label_id' => $data['label_id']
                    ]);

                    Notification::make()
                        ->title('Tâche créée avec succès!')
                        ->success()
                        ->send();
                }),
            Action::make('createEvent')
                ->label('Programmer un évènement')
                ->form([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required(),
                    Grid::make(2)
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
                        // NOTE: Propriataire
                        'user_id' => $data['user_id'],
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
                            Select::make('label_id')
                                ->label('Label')
                                ->options(Label::callLabels()->pluck('value', 'id'))
                                ->required()
                                ->searchable(),
                        ]),
                    DatePicker::make('due_date')
                        ->required()
                        ->label('Date'),
                    MarkdownEditor::make('description')
                        ->label('Note'),
                ])
                ->action((function (array $data) {
                    Activity::create([
                        'opportunity_id' => $this->record->id,
                        'titre' => 'empty cause it\'s event',
                        'type' => 'call',
                        'statut' => ActivityStatut::TODO,
                        'prioritaire' => $data['prioritaire'],
                        // HACK: responsable
                        'user_id' => $data['user_id'],
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
}

