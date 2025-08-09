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
    public $selectedStatus;
    public $editingStatus = false;

    public $statuses = [
        'Ouverte' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Gagnée' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Perdue' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'En retard' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'Annulée' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        'Fermée' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    ];

    public function mount()
    {
        $this->dateEcheance = $this->opportunity->date_echeance ? $this->opportunity->date_echeance->format('Y-m-d') : null;
        $this->selectedStatus = $this->opportunity->status;
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

    public function updateStatus($newStatus)
    {
        $this->opportunity->status = $newStatus;
        $this->opportunity->save();
        $this->selectedStatus = $newStatus; // Update selected status to reflect change
        $this->editingStatus = false; // Exit editing mode
        $this->dispatch('statusUpdated');
    }

    public function toggleEditingStatus()
    {
        $this->editingStatus = !$this->editingStatus;
    }

    public function render()
    {
        return view('livewire.opportunity-pipeline-timeline');
    }
}
