<?php

namespace App\Filament\Resources\ActivityResource\Widgets;

use App\Models\Activity;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions;

class CalendarWidget extends FullCalendarWidget
{
    // protected static string $view = 'filament.resources.activity-resource.widgets.calendar-widget';

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
            $color = match ($activity->type) {
                'event' => '#7C3AED', // violet
                'call'  => '#F97316', // orange
                'task'  => '#2563EB', // bleu
                default => '#4B5563', // gris
            };

            return EventData::make()
                ->id($activity->id)
                ->title($activity->label?->value ?? $activity->titre)
                ->start($activity->date_debut ?? $activity->due_date)
                ->end($activity->date_fin ?? $activity->due_date)
                ->allDay((bool) $activity->is_all_day)
                ->backgroundColor($color)
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
                ->options([
                    'event' => 'Événement',
                    'call'  => 'Appel',
                    'task'  => 'Tâche',
                ])
                ->required(),

            Forms\Components\Select::make('label_id')
                ->label('Label')
                ->relationship('label', 'name')
                ->searchable(),

            Forms\Components\DateTimePicker::make('date_debut')
                ->label('Date début'),

            Forms\Components\DateTimePicker::make('date_fin')
                ->label('Date fin'),

            Forms\Components\DateTimePicker::make('due_date')
                ->label('Date d’échéance'),

            Forms\Components\Toggle::make('is_all_day')
                ->label('Toute la journée'),

            Forms\Components\Toggle::make('prioritaire')
                ->label('Prioritaire'),

            Forms\Components\Select::make('statut')
                ->label('Statut')
                ->options([
                    'To Do' => 'À faire',
                    'In Progress' => 'En cours',
                    'Done' => 'Terminé',
                    'Canceled' => 'Annulé',
                ]),
        ];
    }

    /**
     * Actions de la modal
     */
    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Actions de l’en-tête du calendrier
     */
    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


}
