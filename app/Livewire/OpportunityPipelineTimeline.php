<?php

namespace App\Livewire;

use App\Models\EtapePipeline;
use Livewire\Component;
use App\Models\Opportunity;
use Illuminate\Support\Facades\Log;

class OpportunityPipelineTimeline extends Component
{
    public Opportunity $opportunity;
    public $dateEcheance;

    public function mount()
    {
        $this->dateEcheance = $this->opportunity->date_echeance ? $this->opportunity->date_echeance->format('Y-m-d') : null;
    }

    public function updateEtape($etapeId)
    {
        // Find the step to ensure it exists
        $etape = EtapePipeline::find($etapeId);

        if ($etape && $this->opportunity->etape_pipeline_id !== $etape->id) {
            $this->opportunity->etape_pipeline_id = $etape->id;
            $this->opportunity->save();

            // Refresh the component to reflect the change instantly
            $this->dispatch('$refresh');
        }
    }

    public function updatedDateEcheance($value)
    {
        $this->validate([
            'dateEcheance' => 'required|date',
        ]);

        $this->opportunity->date_echeance = $value;
        $this->opportunity->save();

        // Reload the opportunity model to get the latest data from the database
        $this->opportunity->refresh();

        $this->dispatch('dateEcheanceUpdated');
    }

    public function render()
    {
        return view('livewire.opportunity-pipeline-timeline');
    }
}
