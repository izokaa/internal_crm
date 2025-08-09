<x-filament-panels::page>
    <div class="mb-4">
        <livewire:opportunity-pipeline-timeline :opportunity="$record" />
    </div>

    <div x-data="{ activeFilter: 'informations-generales' }" class="mt-8">
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
            <nav class="-mb-px flex space-x-8" aria-label="Filters">
                <button
                    @click="activeFilter = 'activite'"
                    :class="activeFilter === 'activite' ? 'border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                >
                    Activité
                </button>
                <button
                    @click="activeFilter = 'informations-generales'"
                    :class="activeFilter === 'informations-generales' ? 'border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                >
                    Informations générales
                </button>
                <button
                    @click="activeFilter = 'documents-lies'"
                    :class="activeFilter === 'documents-lies' ? 'border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                >
                    Documents liés
                </button>
            </nav>
        </div>

        <!-- Dynamic Content Area -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <div x-show="activeFilter === 'activite'">
                <h3 class="text-lg font-semibold mb-4">Activité</h3>
                <p>Contenu de l'onglet Activité (actions, historique, commentaires).</p>
            </div>
            <div x-show="activeFilter === 'informations-generales'">
                <h3 class="text-lg font-semibold mb-4">Informations générales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Titre:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->titre }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Identifiant:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->prefix }}-{{ $record->id }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Description:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->description }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Note:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->note }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date d'échéance:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->date_echeance?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Probabilité:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->probabilite }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Statut:</p>
                        @php
                            $statusBgColor = match ($record->status) {
                                'Ouverte' => '#DBEAFE', // blue-100
                                'Gagnée' => '#D1FAE5', // green-100
                                'Perdue' => '#FEE2E2', // red-100
                                'En retard' => '#FEF3C7', // yellow-100
                                'Annulée' => '#E5E7EB', // gray-100
                                'Fermée' => '#EDE9FE', // purple-100
                                default => '#E5E7EB', // gray-100
                            };
                            $statusTextColor = match ($record->status) {
                                'Ouverte' => '#1E40AF', // blue-800
                                'Gagnée' => '#065F46', // green-800
                                'Perdue' => '#991B1B', // red-800
                                'En retard' => '#92400E', // yellow-800
                                'Annulée' => '#4B5563', // gray-800
                                'Fermée' => '#5B21B6', // purple-800
                                default => '#4B5563', // gray-800
                            };
                        @endphp
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            style="background-color: {{ $statusBgColor }}; color: {{ $statusTextColor }};"
                        >
                            {{ $record->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Montant estimé:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ number_format($record->montant_estime, 2) }} {{ $record->devise }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Contact:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->contact->nom }} {{ $record->contact->prenom }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Source:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->source->nom }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pipeline:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->pipeline->nom }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Étape Pipeline:</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $record->etapePipeline->nom }}</p>
                    </div>
                </div>
            </div>
            <div x-show="activeFilter === 'documents-lies'">
                <h3 class="text-lg font-semibold mb-4">Documents liés</h3>
                <p>Contenu de l'onglet Documents liés.</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>