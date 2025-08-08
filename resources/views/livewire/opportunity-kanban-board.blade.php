<div class="p-4 filament-kanban-board">
    <div class="mb-4">
        <label for="pipeline-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sélectionner un Pipeline:</label>
        <select id="pipeline-select" wire:model.live="selectedPipelineId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            @foreach($pipelines as $pipeline)
                <option value="{{ $pipeline->id }}">{{ $pipeline->nom }}</option>
            @endforeach
        </select>
    </div>

    @if($currentPipeline)
        <div class="flex space-x-4 overflow-x-auto pb-4">
            @foreach($currentPipeline->etapePipelines->sortBy('ordre') as $etape)
                <div
                    class="flex-shrink-0 w-72 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-md flex flex-col kanban-column"
                    wire:key="stage-{{ $etape->id }}"
                    @drop.prevent="
                        const opportunityId = event.dataTransfer.getData('opportunityId');
                        const oldStageId = event.dataTransfer.getData('oldStageId');
                        const targetColumn = event.target.closest('.kanban-column');
                        const newOrder = Array.from(targetColumn.querySelectorAll('.kanban-card')).map(card => card.dataset.opportunityId);
                        
                        if (targetColumn.dataset.stageId != oldStageId) {
                            $wire.updateOpportunityStage(opportunityId, {{ $etape->id }}, newOrder);
                        }
                    "
                    @dragover.prevent
                    @dragenter.prevent
                    data-stage-id="{{ $etape->id }}"
                >
                    <div class="p-3 font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600">
                        {{ $etape->nom }}
                    </div>
                    <div class="p-3 space-y-3 flex-grow overflow-y-auto kanban-cards-container">
                        @forelse($opportunities->get($etape->id, collect())->sortBy('sort_order') as $opportunity)
                            <div
                                class="bg-white dark:bg-gray-700 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-600 cursor-grab kanban-card"
                                draggable="true"
                                wire:key="opportunity-{{ $opportunity->id }}"
                                data-opportunity-id="{{ $opportunity->id }}"
                                data-old-stage-id="{{ $etape->id }}"
                                @dragstart="
                                    event.dataTransfer.setData('opportunityId', event.target.dataset.opportunityId);
                                    event.dataTransfer.setData('oldStageId', event.target.dataset.oldStageId);
                                "
                            >
                                <p class="font-bold text-gray-900 dark:text-gray-100">{{ $opportunity->titre }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $opportunity->contact->nom ?? 'N/A' }} {{ $opportunity->contact->prenom ?? '' }}</p>
                                <p class="text-sm font-medium text-green-600 dark:text-green-400">{{ number_format($opportunity->montant_estime, 2) }} {{ $opportunity->devise }}</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $opportunity->date_echeance->format('d/m/Y') }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $opportunity->probabilite }}%
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                Aucune opportunité
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600 dark:text-gray-400">Veuillez créer ou sélectionner un pipeline pour afficher le tableau Kanban.</p>
    @endif

    <style>
        .kanban-column {
            min-height: 400px; /* Hauteur minimale pour les colonnes */
            max-height: 80vh; /* Hauteur maximale pour les colonnes */
        }
        .kanban-cards-container {
            min-height: 150px;
        }
    </style>
</div>
