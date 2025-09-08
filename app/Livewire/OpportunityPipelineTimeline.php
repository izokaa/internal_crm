<?php

namespace App\Livewire;

use App\Models\EtapePipeline;
use Livewire\Component;
use App\Models\Opportunity;
use App\Enums\OpportunityStatut;
use Illuminate\Support\Facades\Log;

class OpportunityPipelineTimeline extends Component
{
    public Opportunity $opportunity;
    public $dateEcheance;
    public $selectedStatus;
    public $editingStatus = false;
    public $statuses;

    public function mount()
    {
        $this->dateEcheance = $this->opportunity->date_echeance ? $this->opportunity->date_echeance->format('Y-m-d') : null;
        $this->selectedStatus = $this->opportunity->status;
        $this->statuses = OpportunityStatut::cases(); // Obtenir tous les cas de l'enum
    }

    public function updateEtape($etapeId)
    {
        // Vérifier que l'étape existe et appartient au bon pipeline
        $etape = EtapePipeline::find($etapeId);

        if ($etape &&
            $this->opportunity->pipeline &&
            $etape->pipeline_id === $this->opportunity->pipeline->id &&
            $this->opportunity->etape_pipeline_id !== $etape->id) {

            $this->opportunity->etape_pipeline_id = $etape->id;
            $this->opportunity->save();

            // Actualiser l'opportunité pour obtenir les dernières données
            $this->opportunity->refresh();

            // Émettre un événement pour notifier d'autres composants
            $this->dispatch('etapeUpdated', $etape->id);
        }
    }

    public function updatedDateEcheance($value)
    {
        $this->validate([
            'dateEcheance' => 'nullable|date',
        ]);

        $this->opportunity->date_echeance = $value ? $value : null;
        $this->opportunity->save();

        // Actualiser l'opportunité
        $this->opportunity->refresh();

        $this->dispatch('dateEcheanceUpdated', $value);
    }

    public function updateStatus($newStatusValue)
    {
        // Convertir la valeur string en instance d'enum si nécessaire
        $newStatus = is_string($newStatusValue)
            ? OpportunityStatut::from($newStatusValue)
            : $newStatusValue;

        $this->opportunity->status = $newStatus;
        $this->opportunity->save();

        $this->selectedStatus = $newStatus;
        $this->editingStatus = false;

        // Actualiser l'opportunité
        $this->opportunity->refresh();

        $this->dispatch('statusUpdated', $newStatus->value);
    }

    public function toggleEditingStatus()
    {
        $this->editingStatus = !$this->editingStatus;
    }

    public function getStatusesProperty()
    {
        return OpportunityStatut::cases();
    }

    public function render()
    {
        return view('livewire.opportunity-pipeline-timeline');
    }
}
