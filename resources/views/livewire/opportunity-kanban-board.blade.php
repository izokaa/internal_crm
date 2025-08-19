<div class="p-4 filament-kanban-board">
    <div class="mb-4 flex items-center gap-2">
        <div>
            <label for="pipeline-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sélectionner un Pipeline:</label>
            <select id="pipeline-select" wire:model.live="selectedPipelineId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                @foreach($pipelines as $pipeline)
                <option value="{{ $pipeline->id }}">{{ $pipeline->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center justify-between gap-2">
            <div>
                <label for="debut" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date début</label>
                <input type="date" wire:model.live="dateDebut"  id="debut" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            <div>
                <label for="fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date fin</label>
                <input type="date" wire:model.live="dateFin" id="fin" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>

    </div>

    @if($currentPipeline)
        <div class="flex gap-2 overflow-x-auto pb-4" x-data="kanban()">
            @foreach($currentPipeline->etapePipelines->sortBy('ordre') as $etape)
                <div
                    class="flex-shrink-0 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col kanban-column"
                    wire:key="stage-{{ $etape->id }}"
                    x-on:drop.prevent="handleDrop($event, {{ $etape->id }})"
                    x-on:dragover.prevent="handleDragOver($event)"
                    data-stage-id="{{ $etape->id }}"
                >
                    <div class="relative flex items-center p-3 border-b border-gray-300 dark:border-gray-600">
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $etape->nom }} {{ $opportunities->get($etape->id, collect())->count() > 0 ? '(' . $opportunities->get($etape->id)->count() . ')' : '' }}
                        </span>
                        @if(!$loop->last)
                            <div class="kanban-step-arrow"></div>
                        @endif
                    </div>
                    <div class="p-3 space-y-3 flex-grow overflow-y-auto kanban-cards-container" data-stage-id="{{ $etape->id }}">
                        @forelse($opportunities->get($etape->id, collect())->sortBy('sort_order') as $opportunity)
                            @php
                                $statusColor = $opportunity->status->getTailwindBadge();
                            @endphp
                            <a href="{{ route('filament.admin.resources.opportunities.view', $opportunity->id) }}"
                               class="block bg-orange-100 dark:bg-orange-700 hover:shadow-md kanban-card p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 cursor-grab"
                               draggable="true"
                               wire:key="opportunity-{{ $opportunity->id }}"
                               x-on:dragstart="handleDragStart($event, {{ $opportunity->id }}, {{ $etape->id }})"
                               data-opportunity-id="{{ $opportunity->id }}"
                            >
                                <div class="flex justify-between items-start">
                                    <span class="font-semibold text-md text-gray-700 dark:text-gray-300">{{ $opportunity->titre }}</span>
                                    <span class="text-sm font-mono bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded">{{ $opportunity->prefix }}-{{ $opportunity->id }}</span>
                                </div>
                                <div class="mt-3 space-y-2">
                                    <p class="text-sm"><span class="font-semibold text-gray-500 dark:text-gray-400">Contact:</span> {{ $opportunity->contact->nom ?? 'N/A' }} {{ $opportunity->contact->prenom ?? '' }}</p>
                                    <p class="text-sm"><span class="font-semibold text-gray-500 dark:text-gray-400">Montant:</span> <span class="font-medium text-green-600 dark:text-green-400">{{ number_format($opportunity->montant_estime, 2) }} {{ $opportunity->devise }}</span></p>
                                    <p class="text-sm"><span class="font-semibold text-gray-500 dark:text-gray-400">Échéance:</span> {{ $opportunity->date_echeance->format('d/m/Y') }}</p>
                                    <p class="text-sm"><span class="font-semibold text-gray-500 dark:text-gray-400">Probabilité:</span> <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ $opportunity->probabilite }}%</span></p>
                                    <p class="text-sm flex items-center gap-2">
                                        <span class="font-semibold text-gray-500 dark:text-gray-400 mr-2">Statut:</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                              style="background-color: {{ $opportunity->status->getBadge() }}; padding-inline: .5rem; color: {{ $opportunity->status->getTextStatusColor() }}">
                                              {{ $opportunity->status->getLabel() }}
                                        </span>
                                    </p>
                                </div>
                            </a>
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

    <script>
        function kanban() {
            return {
                handleDragStart(event, opportunityId, oldStageId) {
                    event.dataTransfer.setData('opportunityId', opportunityId);
                    event.dataTransfer.setData('oldStageId', oldStageId);
                    event.target.classList.add('opacity-50');
                },
                handleDragOver(event) {
                    if (event.target.closest('.kanban-cards-container')) {
                        event.preventDefault();
                    }
                },
                handleDrop(event, newStageId) {
                    const column = event.target.closest('.kanban-column');
                    if (column) {
                        column.classList.remove('border-blue-500');
                    }

                    const opportunityId = event.dataTransfer.getData('opportunityId');
                    const oldStageId = event.dataTransfer.getData('oldStageId');
                    const cardsContainer = event.target.closest('.kanban-cards-container');

                    if (cardsContainer) {
                        const cardElements = Array.from(cardsContainer.querySelectorAll('.kanban-card'));
                        const newOrder = cardElements.map(card => card.dataset.opportunityId);

                        if (newStageId != oldStageId) {
                            @this.call('updateOpportunityStage', opportunityId, newStageId, newOrder);
                        } else {
                            @this.call('updateOpportunityOrder', opportunityId, newOrder);
                        }
                    }

                    // Remove opacity class from all cards
                    document.querySelectorAll('.kanban-card').forEach(card => card.classList.remove('opacity-50'));
                }
            }
        }
    </script>

    <style>
        .kanban-column {
            min-height: 400px;
            max-height: 80vh;
        }
        .kanban-cards-container {
            min-height: 150px;
        }
        .kanban-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .kanban-step-arrow {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translate(0, -50%);
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-left: 15px solid #f97316;
            z-index: 10;
        }
    </style>
</div>
