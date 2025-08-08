<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pipeline;
use App\Models\Opportunity;
use Illuminate\Support\Collection;

class OpportunityKanbanBoard extends Component
{
    public ?int $selectedPipelineId = null;
    public Collection $pipelines;

    protected $listeners = ['pipelineSelected'];

    public function mount(): void
    {
        $this->pipelines = Pipeline::with('etapePipelines')->get();
        $this->selectedPipelineId = $this->pipelines->first()->id ?? null;
    }

    public function pipelineSelected(int $pipelineId): void
    {
        $this->selectedPipelineId = $pipelineId;
    }

    public function getOpportunitiesProperty(): Collection
    {
        if ($this->selectedPipelineId) {
            return Opportunity::where('pipeline_id', $this->selectedPipelineId)
                ->whereNotNull('etape_pipeline_id')
                ->with('contact')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('etape_pipeline_id');
        }

        return collect();
    }

    public function updateOpportunityStage($opportunityId, $newStageId, $newOrder): void
    {
        $opportunity = Opportunity::find($opportunityId);
        if ($opportunity) {
            $opportunity->etape_pipeline_id = $newStageId;
            $opportunity->save();

            // Reorder opportunities in the new stage
            $this->reorderOpportunitiesInStage($newStageId, $newOrder);
        }
    }

    protected function reorderOpportunitiesInStage(int $stageId, array $newOrder): void
    {
        foreach ($newOrder as $index => $opportunityId) {
            Opportunity::where('id', $opportunityId)->update(['sort_order' => $index + 1]);
        }
    }

    public function render()
    {
        $currentPipeline = $this->pipelines->firstWhere('id', $this->selectedPipelineId);

        return view('livewire.opportunity-kanban-board', [
            'currentPipeline' => $currentPipeline,
            'opportunities' => $this->opportunities,
        ]);
    }
}
