<?php

namespace App\Livewire;

use App\Models\Activity as AppActivity;
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
        Log::info($this->activities[count($this->activities) - 1]);
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
            $query->where('subject_type', Opportunity::class)
                ->where('subject_id', $this->opportunity->id);
        })
            ->orWhere(function ($query) {
                $query->where('subject_type', \App\Models\Activity::class)
                    ->whereIn('subject_id', $this->opportunity->activities->pluck('id'));
            })
            ->with('causer')
            ->latest()
            ->get();

        $this->activities = $activities->map(function ($activityLog) {
            $activity = AppActivity::with(['label', 'contact'])->find($activityLog->subject_id);

            return [
                'type'       => $activity?->type,
                'created_at' => $activity?->created_at?->toDateTimeString(),
                'label_name' => $activity?->label?->name,
                'label_value' => $activity?->label?->value,
                'statut'     => $activity?->statut?->value,
                'badge'      => $activity?->statut?->getBadge(),
                'due_date'   => $activity?->due_date?->toDateTimeString(),
                'date_debut' => $activity?->date_debut?->toDateTimeString(),
                'date_fin'   => $activity?->date_fin?->toDateTimeString(),
                'prioritaire' => $activity?->prioritaire,
                'contact'    => $activity?->contact?->name,
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
