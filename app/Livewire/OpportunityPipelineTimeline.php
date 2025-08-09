<?php

namespace App\Livewire;

use App\Models\EtapePipeline;
use Livewire\Component;
use App\Models\Opportunity;

class OpportunityPipelineTimeline extends Component
{
    public Opportunity $opportunity;

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

    public function render()
    {
        return view('livewire.opportunity-pipeline-timeline');
    }
}
