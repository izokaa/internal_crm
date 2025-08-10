<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Opportunity;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Collection;
use Livewire\Attributes\On; // Import the On attribute

class OpportunityActivityTimeline extends Component
{
    public Opportunity $opportunity;

    public function mount(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
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
        return Activity::where(function ($query) {
            $query->where('subject_type', Opportunity::class)
                  ->where('subject_id', $this->opportunity->id);
        })->orWhere(function ($query) {
            $query->where('subject_type', \App\Models\Activity::class)
                  ->whereIn('subject_id', $this->opportunity->activities->pluck('id'));
        })
        ->with('causer') // Eager load the causer (user) relationship
        ->latest() // Order by latest activity
        ->get();
    }

    public function render()
    {
        return view('livewire.opportunity-activity-timeline', [
            'activities' => $this->activities,
        ]);
    }
}
