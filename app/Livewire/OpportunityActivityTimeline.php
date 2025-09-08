<?php

namespace App\Livewire;

use App\Enums\ActivityStatut;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Livewire\Component;
use App\Models\Opportunity;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Collection;
use Livewire\Attributes\On; // Import the On attribute
use App\Models\Label;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OpportunityActivityTimeline extends Component
{
    public Opportunity $opportunity;

    public $activities = [];

    protected $listeners = [
        'activityCreated' => 'refreshTheFucingPage',
    ];

    public function refreshTheFucingPage()
    {
        $this->refreshActivities();
    }

    public function mount(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
        $this->refreshActivities();
    }

    public function refreshActivities()
    {
        // recharge la relation et actualise $activities
        $this->opportunity->refresh();

        $activities = Activity::where(function ($query) {
            $query->where('subject_type', \App\Models\Activity::class)
                ->where('subject_id', $this->opportunity->id);
        })
            ->with('causer')
            ->latest()
            ->get();

        $this->activities = $activities->map(function ($activityLog) {
            $attributes = $activityLog->description == 'deleted' ? $activityLog->properties['old'] : $activityLog->properties['attributes'];
            $label = Label::find($attributes['label_id']);
            $responsable = User::find($attributes['user_id']);

            $description = match ($activityLog->description) {
                'created' => 'créé',
                'updated' => 'modifé',
                'deleted' => 'supprimé',
            };

            return [
                'description' => $description,
                'type'       => $attributes['type'],
                'created_at' => $attributes['created_at'],
                'label_value' => $label?->value,
                'statut'      => collect(ActivityStatut::cases())->where('value', $attributes['statut'])->first()?->getLabel() ?? '',
                'badge'      => collect(ActivityStatut::cases())->where('value', $attributes['statut'])->first()?->getBadge() ?? '',
                'due_date'   => $attributes['due_date'],
                'date_debut'   => $attributes['date_debut'],
                'date_fin'   => $attributes['date_fin'],
                'responsable_name' => $responsable?->name,
                'prioritaire' => $attributes['prioritaire'],
                'causer_name' => optional($activityLog->causer)->name,
                'causer_initial' => $activityLog->causer ? strtoupper(substr($activityLog->causer->name, 0, 1)) : null,
            ];
        });
    }

    public function render()
    {
        return view('livewire.opportunity-activity-timeline', [
            'activities' => $this->activities,
        ]);
    }
}
