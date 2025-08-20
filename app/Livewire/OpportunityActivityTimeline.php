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

class OpportunityActivityTimeline extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;


    public $record;
    public Opportunity $opportunity;

    public function mount(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
        $this->record = $opportunity;
    }

    #[On('activityCreated')] // Listen for the 'activityCreated' event
    public function refreshActivities(): void
    {
        // This method will be called when the 'activityCreated' event is dispatched.
        // Livewire automatically re-renders the component when a public property changes
        // or a method is called that affects the view.
        // Since getActivitiesProperty is a computed property, it will be re-evaluated.
    }

    public function getActivitiesProperty(): Collection
    {
        $activities = Activity::where(function ($query) {
            $query->where('subject_type', Opportunity::class)
                  ->where('subject_id', $this->opportunity->id);
        })->orWhere(function ($query) {
            $query->where('subject_type', \App\Models\Activity::class)
                  ->whereIn('subject_id', $this->opportunity->activities->pluck('id'));
        })
        ->with('causer')
        ->latest()
        ->get();


        return $activities->map(function ($activityLog) {
            $activity = AppActivity::with(['label'])->find($activityLog->subject_id);
            $activityAction = collect([
                'activity' => $activity,
                'causer' => User::find($activityLog->causer_id),
            ]);

            return $activityAction;
        });
    }

    public function render()
    {
        return view('livewire.opportunity-activity-timeline', [
            'activities' => $this->activities,
        ]);
    }
}
