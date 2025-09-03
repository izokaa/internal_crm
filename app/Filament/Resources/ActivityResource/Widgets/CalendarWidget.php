<?php

namespace App\Filament\Resources\ActivityResource\Widgets;

use App\Models\Activity;
use App\Models\Label;
use App\Models\User;
use Filament\Forms;
use Illuminate\Support\Facades\Event;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Enums\ActivityStatut;
use Saade\FilamentFullCalendar\Actions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Activity::class;

    // Add listener to handle drag and drop event updates
    protected $listeners = [
        'eventDropped' => 'handleEventDropped',
    ];

    public function eventDidMount(): string
    {
        return <<<JS
                function({ event, el }) {
                    const status = (event.extendedProps?.statut ?? '').toLowerCase();
                    const statusLabel = event.extendedProps?.statutLabel ?? '';
                    const statusBadge = event.extendedProps?.statutBadge ?? '';

                    // event time
                    const eventTime = el.getElementsByClassName('fc-event-time')[0];
                    eventTime.style.display = 'none';
                    // event title
                    const eventTitle = el.getElementsByClassName('fc-event-title')[0];
                    // don't display the event time
                    const eventStatus = document.createElement('span');
                    eventStatus.innerHTML = statusLabel;
                    eventStatus.style.backgroundColor = statusBadge;
                    eventStatus.style.padding = '5px';
                    eventStatus.style.borderRadius = '.5rem';
                    el.appendChild(eventStatus);

                    el.style.border = 'none';

                    el.style.display = 'flex' ;
                    el.style.alignItems = 'center' ;
                    el.style.gap = '1rem' ;

                }
            JS;
    }

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
            $title = $activity->label?->value;

            return EventData::make()
                ->id($activity->id)
                ->title($title)
                ->start($activity->date_debut ?? $activity->due_date)
                ->end(Carbon::parse($activity->date_fin)->addDays(1) ?? $activity->due_date)
                ->allDay((bool) $activity->is_all_day)
                ->extendedProps([
                    'type'       => $activity->type,
                    'date_debut'     => $activity->statut,
                    'statut'     => $activity->statut->value,
                    'statutLabel' => $activity->statut->getLabel(),
                    'statutBadge' => $activity->statut->getBadge(),
                    'prioritaire' => $activity->prioritaire,
                    'contact'    => $activity->contact?->name,
                    'opportunity' => $activity->opportunity?->titre,
                ]);
        })->toArray();
    }

    public function getFormSchema(): array
    {
        return [

            Forms\Components\Select::make('type')
                ->label('Type')
                ->options([
                    'event' => 'Événement',
                    'call'  => 'Appel',
                    'task'  => 'Tâche',
                ])
                ->required()
                ->reactive(), // <-- important en v3

            Forms\Components\Select::make('label_id')
                ->label('Label')
                ->default(fn ($record) => $record?->label?->value) // protège si null
                ->required()
                ->options(function (Forms\Get $get) {
                    $type = $get('type');
                    if ($type === 'task') {
                        return Label::taskLabels()->pluck('value', 'id');
                    } elseif ($type === 'event') {
                        return Label::eventLabels()->pluck('value', 'id');
                    } elseif ($type === 'call') {
                        return Label::callLabels()->pluck('value', 'id');
                    }
                    return [];
                })
                ->searchable()
                ->reactive(), // <-- lui aussi doit réagir
            Forms\Components\Select::make('user_id')
                ->relationship('user')
                ->default(fn (Activity $record) => $record->user->nom . $record->user->prenom)
                ->required()
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
                ->default(fn ($record) => $record->statut ?? ActivityStatut::TODO->value)
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
                        Log::info($arguments);
                        $form->fill([
                            'titre' => $record->titre,
                            'statut' => $record->statut,
                            'label' => $record->label->value,
                            'description' => $record->description,
                            'type' => $record->type,
                            'due_date' => @$arguments['event']['start'] ?? $record->due_date,
                            'label_id' => $record->label_id,
                            'date_debut' => $arguments['event']['start'] ?? $record->date_debut,
                            'date_fin' => @$arguments['event']['end'] ? Carbon::parse($arguments['event']['end'])->subDays(1) : $record->date_fin,
                        ]);
                    }
                ),
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
                            'date_debut' => $arguments['start'] ?? null,
                            'date_fin' => $arguments['end'] ?? null,
                            'due_date' => $arguments['start'] ?? null,
                            'statut' => ActivityStatut::TODO->value
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


    public function handleEventDropped(array $data): void
    {
        // Assuming $data contains keys: id, start, end.
        $activity = \App\Models\Activity::find($data['id']);
        if ($activity) {
            $activity->update([
                'date_debut' => $data['start'],
                'date_fin'   => $data['end'],
            ]);
        }
        // Optionally refresh widget/calendar view.
        $this->dispatch('$refresh');
    }
}
