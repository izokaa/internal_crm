<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Opportunity;

class OpportunityPipelineTimeline extends Component
{
    public Opportunity $opportunity;

    public function render()
    {
        return view('livewire.opportunity-pipeline-timeline');
    }
}
