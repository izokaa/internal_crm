<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Enums\ActivityStatut;
use App\Filament\Resources\OpportunityResource;
use App\Models\Activity;
use App\Models\Label;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action as HeaderAction;
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
use App\Models\PieceJointe;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewOpportunityDetails extends ViewRecord
{
    protected static string $resource = OpportunityResource::class;

    protected static string $view = 'filament.resources.opportunity-resource.pages.view-opportunity-details';

    public function infolist(Infolist $infolist): Infolist
    {
        return OpportunityResource::infolist($infolist)
            ->schema([
                Section::make('Documents liés')
                    ->schema([
                        RepeatableEntry::make('piecesJointes')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('nom_fichier')
                                    ->label('Nom du fichier')
                                    ->icon('heroicon-o-document')
                                    ->columnSpan(1),
                                TextEntry::make('created_at')
                                    ->label('Date d\'ajout')
                                    ->dateTime()
                                    ->columnSpan(1),
                                Actions::make([
                                    InfolistAction::make('download')
                                        ->label('Télécharger')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->color('success')
                                        ->action(function (PieceJointe $record) {
                                            $filePath = Storage::disk('public')->path($record->chemin_fichier);

                                            if (!Storage::disk('public')->exists($record->chemin_fichier)) {
                                                Notification::make()
                                                    ->title('Erreur')
                                                    ->body('Le fichier est introuvable.')
                                                    ->danger()
                                                    ->send();
                                                return;
                                            }

                                            // Récupérer l'extension du fichier original
                                            $originalExtension = pathinfo($record->chemin_fichier, PATHINFO_EXTENSION);

                                            // Construire le nom du fichier avec la bonne extension
                                            $downloadName = $record->nom_fichier;

                                            // Vérifier si le nom du fichier a déjà une extension
                                            if (!pathinfo($downloadName, PATHINFO_EXTENSION) && $originalExtension) {
                                                $downloadName = $downloadName . '.' . $originalExtension;
                                            }

                                            // Déterminer le type MIME correct
                                            $mimeType = Storage::disk('public')->mimeType($record->chemin_fichier);

                                            return response()->download($filePath, $downloadName, [
                                                'Content-Type' => $mimeType,
                                            ]);
                                        }),
                                    InfolistAction::make('view')
                                        ->label('Voir')
                                        ->icon('heroicon-o-eye')
                                        ->color('primary')
                                        ->url(fn (PieceJointe $record): string => Storage::disk('public')->url($record->chemin_fichier))
                                        ->openUrlInNewTab(),
                                ])->columnSpan(2),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible(),
            ]);
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('createTask')
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

                    $this->dispatch('activityCreated');
                }),
            HeaderAction::make('createEvent')
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

                    $this->dispatch('activityCreated');
                }),

            HeaderAction::make('createCall')
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

                    $this->dispatch('activityCreated');
                })),
        ];
    }
}
