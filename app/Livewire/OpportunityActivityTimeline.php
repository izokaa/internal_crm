<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Opportunity;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Collection;
use Livewire\Attributes\On; // Import the On attribute
use App\Models\Label;
use App\Models\Contact;
use Carbon\Carbon;

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

        return $activities->map(function ($activity) {
            $displayProperties = collect();
            $attributesToDisplay = [];

            // Define attributes to display based on activity type
            switch ($activity->type) {
                case 'task':
                    $attributesToDisplay = ['titre', 'description', 'statut', 'due_date', 'prioritaire', 'label_id'];
                    break;
                case 'event':
                    $attributesToDisplay = ['titre', 'description', 'statut', 'date_debut', 'date_fin', 'is_all_day', 'label_id'];
                    break;
                case 'call':
                    $attributesToDisplay = ['description', 'statut', 'due_date', 'prioritaire', 'contact_id', 'label_id'];
                    break;
                default:
                    // For other types or general logs, display all logged attributes
                    $attributesToDisplay = array_keys($activity->properties['attributes'] ?? []);
                    break;
            }

            // Process 'attributes' (new values)
            if ($activity->properties->has('attributes')) {
                foreach ($attributesToDisplay as $attribute) {
                    if (isset($activity->properties['attributes'][$attribute])) {
                        $newValue = $activity->properties['attributes'][$attribute];

                        $displayKey = $attribute; // Default display key
                        $formattedValue = $newValue; // Default formatted value

                        // Special handling for label_id and contact_id
                        if ($attribute === 'label_id') {
                            $label = \App\Models\Label::find($newValue);
                            $displayKey = 'label';
                            $formattedValue = $label ? $label->value : 'N/A';
                        } elseif ($attribute === 'contact_id') {
                            $contact = \App\Models\Contact::find($newValue);
                            $displayKey = 'contact';
                            $formattedValue = $contact ? $contact->nom . ' ' . $contact->prenom : 'N/A';
                        } else {
                            $formattedValue = $this->formatPropertyValue($attribute, $newValue, $activity->type);
                        }

                        if ($activity->event === 'updated' && $activity->properties->has('old') && isset($activity->properties['old'][$attribute])) {
                            $oldValue = $activity->properties['old'][$attribute];
                            $formattedOldValue = $this->formatPropertyValue($attribute, $oldValue, $activity->type);

                            // Re-resolve old label/contact if it was an ID
                            if ($attribute === 'label_id') {
                                $oldLabel = \App\Models\Label::find($oldValue);
                                $formattedOldValue = $oldLabel ? $oldLabel->value : 'N/A';
                            } elseif ($attribute === 'contact_id') {
                                $oldContact = \App\Models\Contact::find($oldValue);
                                $formattedOldValue = $oldContact ? $oldContact->nom . ' ' . $oldContact->prenom : 'N/A';
                            }

                            if ($formattedOldValue !== $formattedValue) { // Only show if value actually changed
                                $displayProperties->put($displayKey, ['old' => $formattedOldValue, 'new' => $formattedValue]);
                            }
                        } else {
                            $displayProperties->put($displayKey, $formattedValue);
                        }
                    }
                }
            }

            // For 'created' events, ensure all relevant attributes are displayed
            if ($activity->event === 'created' && $activity->properties->has('attributes')) {
                foreach ($attributesToDisplay as $attribute) {
                    if (isset($activity->properties['attributes'][$attribute]) && !$displayProperties->has($attribute)) {
                        $newValue = $activity->properties['attributes'][$attribute];
                        $displayKey = $attribute;
                        $formattedValue = $newValue;

                        // Special handling for label_id and contact_id
                        if ($attribute === 'label_id') {
                            $label = \App\Models\Label::find($newValue);
                            $displayKey = 'label';
                            $formattedValue = $label ? $label->value : 'N/A';
                        } elseif ($attribute === 'contact_id') {
                            $contact = \App\Models\Contact::find($newValue);
                            $displayKey = 'contact';
                            $formattedValue = $contact ? $contact->nom . ' ' . $contact->prenom : 'N/A';
                        } else {
                            $formattedValue = $this->formatPropertyValue($attribute, $newValue, $activity->type);
                        }
                        $displayProperties->put($displayKey, $formattedValue);
                    }
                }
            }

            $activity->displayProperties = $displayProperties;
            return $activity;
        });
    }

    private function formatPropertyValue($attribute, $value, $activityType = null)
    {
        if (is_null($value)) {
            return 'N/A';
        }

        // Handle boolean values
        if (in_array($attribute, ['prioritaire', 'is_all_day'])) {
            return $value ? 'Oui' : 'Non';
        }

        // Handle date attributes
        if (in_array($attribute, ['date_debut', 'date_fin', 'due_date'])) {
            try {
                return Carbon::parse($value)->format('d/m/Y H:i');
            } catch (\Exception $e) {
                return $value; // Return original if parsing fails
            }
        }

        // Handle label_id
        if ($attribute === 'label_id') {
            $label = \App\Models\Label::find($value);
            return $label ? $label->value : 'N/A';
        }

        // Handle contact_id
        if ($attribute === 'contact_id') {
            $contact = \App\Models\Contact::find($value);
            return $contact ? $contact->nom . ' ' . $contact->prenom : 'N/A';
        }

        return $value;
    }

    public function render()
    {
        return view('livewire.opportunity-activity-timeline', [
            'activities' => $this->activities,
        ]);
    }
}
