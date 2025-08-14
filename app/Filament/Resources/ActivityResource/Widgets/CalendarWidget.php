<?php

namespace App\Filament\Resources\ActivityResource\Widgets;

use App\Models\Activity;
use App\Models\User;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Enums\ActivityStatut;
use Saade\FilamentFullCalendar\Actions;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = 'Calendrier des activités';

    public Model | string | null $model = Activity::class;

    public function fetchEvents(array $fetchInfo): array
    {
        $activities = Activity::query()
            ->where(function ($query) use ($fetchInfo) {
                // Cas : événements (date_debut / date_fin)
                $query->where(function ($q) use ($fetchInfo) {
                    $q->whereNotNull('date_debut')
                        ->whereNotNull('date_fin')
                        ->where('date_debut', '>=', $fetchInfo['start'])
                        ->where('date_fin', '<=', $fetchInfo['end']);
                })
                    // OU Cas : tâches / appels (due_date)
                    ->orWhere(function ($q) use ($fetchInfo) {
                        $q->whereNotNull('due_date')
                            ->where('due_date', '>=', $fetchInfo['start'])
                            ->where('due_date', '<=', $fetchInfo['end']);
                    });
            })
            ->with(['label', 'contact', 'opportunity'])
            ->get();

        return $activities->map(function (Activity $activity) {

            $statusLabel = $activity->statut->getLabel();
            $title = $activity->label?->value .  ' [' . $activity->statut->getLabel() . ' ]';

            return EventData::make()
                ->id($activity->id)
                ->title($title)
                ->start($activity->date_debut ?? $activity->due_date)
                ->end($activity->date_fin ?? $activity->due_date)
                ->allDay((bool) $activity->is_all_day)
                ->extendedProps([
                    'type'       => $activity->type,
                    'statut'     => $activity->statut,
                    'prioritaire' => $activity->prioritaire,
                    'contact'    => $activity->contact?->name,
                    'opportunity' => $activity->opportunity?->name,
                ]);
        })->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('titre')
                ->label('Titre')
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('Description'),

            Forms\Components\Select::make('type')
                ->label('Type')
                ->default('event')
                ->options([
                    'event' => 'Événement',
                    'call'  => 'Appel',
                    'task'  => 'Tâche',
                ])
                ->required()
                ->live(),

            Forms\Components\Select::make('label_id')
                ->label('Label')
                ->options(function (Forms\Get $get) {
                    $type = $get('type');
                    if ($type === 'task') {
                        return \App\Models\Label::taskLabels()->pluck('value', 'id');
                    } elseif ($type === 'event') {
                        return \App\Models\Label::eventLabels()->pluck('value', 'id');
                    } elseif ($type === 'call') {
                        return \App\Models\Label::callLabels()->pluck('value', 'id');
                    }
                    return \App\Models\Label::all()->pluck('value', 'id');
                })
                ->searchable(),

            Forms\Components\Select::make('user_id')
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

            Forms\Components\DateTimePicker::make('date_debut')
                ->label('Date début')
                ->withoutTime(true)
                ->hidden(fn (Forms\Get $get) => in_array($get('type'), ['task', 'call'])),

            Forms\Components\DateTimePicker::make('date_fin')
                // ->withoutTime(fn ($get) => $get('is_all_day'))
                ->label('Date fin')
                ->withoutTime(true)
                ->hidden(fn (Forms\Get $get) => in_array($get('type'), ['task', 'call'])),

            Forms\Components\DateTimePicker::make('due_date')
                ->label('Date d\'échéance')
                ->hidden(fn (Forms\Get $get) => $get('type') === 'event'),

            // Forms\Components\Checkbox::make('is_all_day')
            //     ->live()
            //     ->hidden(fn (Forms\Get $get) => $get('type') === 'task' || $get('type') == 'call') // Correction: 'call' au lieu de 'appel'
            //     ->label('Toute la journée'),

            Forms\Components\Toggle::make('prioritaire')
                ->hidden(fn (Forms\Get $get) => $get('type') === 'event')
                ->label('Priorité'),

            Forms\Components\Select::make('statut')
                ->label('Statut')
                ->default(ActivityStatut::TODO)
                ->options(ActivityStatut::class),
        ];
    }

    /**
     * Actions de la modal
     */
    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->mountUsing(
                    function (Activity $record, Forms\Form $form, array $arguments) {
                        $form->fill([
                            'type' => 'event',
                            'date_debut' => $arguments['start'] ?? null,
                            'date_fin' => $arguments['end'] ?? null,
                        ]);
                    }
                )
            ,
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Actions de l'en-tête du calendrier
     */
    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make()
             ->mountUsing(
                 function (Forms\Form $form, array $arguments) {
                     $form->fill([
                        'type' => 'event',
                        'date_debut' => $arguments['start'] ?? null,
                        'date_fin' => $arguments['end'] ?? null
                     ]);
                 }
             )
        ];
    }



    // Correction 3 : Ajouter cette méthode pour mieux gérer la création d'événements
    public function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver() // Optionnel : pour un meilleur UX
                ->mutateFormDataUsing(function (array $data, array $arguments) {
                    // Pré-remplir les dates selon le type d'activité
                    if (isset($arguments['start'])) {
                        $startDate = $arguments['start'];

                        if (in_array($data['type'] ?? 'event', ['task', 'call'])) {
                            $data['due_date'] = $startDate;
                        } else {
                            $data['date_debut'] = $startDate;
                            $data['date_fin'] = $arguments['end'] ?? $startDate;
                        }
                    }

                    return $data;
                }),
        ];
    }
}
