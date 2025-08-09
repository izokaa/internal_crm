<div class="timeline-wrapper">
    @php
        $pipeline = $opportunity->pipeline;
        $etapes = $pipeline ? $pipeline->etapePipelines->sortBy('ordre') : collect();
        $currentEtape = $opportunity->etapePipeline;

        // Calculate progress percentage
        $totalEtapes = $etapes->count();
        $currentEtapeIndex = $currentEtape ? $etapes->search(fn($e) => $e->id === $currentEtape->id) : -1;
        $progressPercentage = 0;
        if ($totalEtapes > 1 && $currentEtapeIndex != -1) {
            // Progress is based on the segments between etapes
            $progressPercentage = ($currentEtapeIndex / ($totalEtapes - 1)) * 100;
        }

    @endphp

    @if($pipeline && $etapes->isNotEmpty())
        <div class="timeline-header-container">
            <div class="left-corner">
                <h4 class="text-xl capitalize font-semibold"> {{ $opportunity->contact->nom }} {{ $opportunity->contact->prenom }}</h4>
                <span class="">{{ $opportunity->prefix }} - {{ $opportunity->id }}</span>
                @php
                    $statusBgColor = match ($selectedStatus) {
                        'Ouverte' => '#DBEAFE', // blue-100
                        'Gagnée' => '#D1FAE5', // green-100
                        'Perdue' => '#FEE2E2', // red-100
                        'En retard' => '#FEF3C7', // yellow-100
                        'Annulée' => '#E5E7EB', // gray-100
                        'Fermée' => '#EDE9FE', // purple-100
                        default => '#E5E7EB', // gray-100
                    };
                    $statusTextColor = match ($selectedStatus) {
                        'Ouverte' => '#1E40AF', // blue-800
                        'Gagnée' => '#065F46', // green-800
                        'Perdue' => '#991B1B', // red-800
                        'En retard' => '#92400E', // yellow-800
                        'Annulée' => '#4B5563', // gray-800
                        'Fermée' => '#5B21B6', // purple-800
                        default => '#4B5563', // gray-800
                    };
                @endphp
                <div class="mt-2" x-data="{ open: false }" @click.away="open = false">
                    <div
                        wire:click="toggleEditingStatus"
                        @click="open = !open"
                        class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium cursor-pointer"
                        style="background-color: {{ $statusBgColor }}; color: {{ $statusTextColor }};"
                    >
                        {{ $selectedStatus }}
                    </div>

                    <div x-show="open" class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none">
                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            @foreach ($statuses as $statusValue => $statusClasses)
                                @php
                                    $optionBgColor = match ($statusValue) {
                                        'Ouverte' => '#DBEAFE', // blue-100
                                        'Gagnée' => '#D1FAE5', // green-100
                                        'Perdue' => '#FEE2E2', // red-100
                                        'En retard' => '#FEF3C7', // yellow-100
                                        'Annulée' => '#E5E7EB', // gray-100
                                        'Fermée' => '#EDE9FE', // purple-100
                                        default => '#E5E7EB', // gray-100
                                    };
                                    $optionTextColor = match ($statusValue) {
                                        'Ouverte' => '#1E40AF', // blue-800
                                        'Gagnée' => '#065F46', // green-800
                                        'Perdue' => '#991B1B', // red-800
                                        'En retard' => '#92400E', // yellow-800
                                        'Annulée' => '#4B5563', // gray-800
                                        'Fermée' => '#5B21B6', // purple-800
                                        default => '#4B5563', // gray-800
                                    };
                                @endphp
                                <a href="#"
                                   wire:click="updateStatus('{{ $statusValue }}')"
                                   @click="open = false"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                   role="menuitem"
                                >
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium"
                                        style="background-color: {{ $optionBgColor }}; color: {{ $optionTextColor }};"
                                    >
                                        {{ $statusValue }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="timeline-header">
                <h3 class="pipeline-name">{{ $pipeline->nom }}</h3>
                @if($currentEtape)
                <p class="current-etape">
                    <span class="current-etape-label">Étape actuelle :</span>
                    <span class="current-etape-name">{{ $currentEtape->nom }}</span>
                </p>
                @endif
            </div>
            <div class="right-corner">
                <h3 class="font-bold text-xl"> {{ $opportunity->montant_estime }} {{ $opportunity->devise }}</h3>
                                <div class="mt-2 flex items-center gap-2">
                    <label for="date_echeance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Date d'échéance:</label>
                    <input
                        type="date"
                        id="date_echeance"
                        wire:model.live="dateEcheance"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    >
                </div>
                <span class="text-gray-300">source: {{ $opportunity->source->nom }}</span>
            </div>
        </div>

        <div class="timeline-container" style="--progress-percentage: {{ $progressPercentage }}%;">
            <div class="timeline-line"></div>
            <div class="timeline-progress-line"></div>
            @foreach($etapes as $etape)
                @php
                    $isCompleted = $currentEtape && $etapes->search(fn($e) => $e->id === $etape->id) < $currentEtapeIndex;
                    $isActive = $currentEtape && $etape->id === $currentEtape->id;
                @endphp
                <div class="timeline-etape {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }} {{ !$isActive ? 'clickable' : '' }}" wire:click="updateEtape({{ $etape->id }})">
                    <div class="etape-circle">{{ $loop->index + 1 }}</div>
                    <div class="etape-name">{{ $etape->nom }}</div>
                </div>
            @endforeach
        </div>
    @else
        <p class="no-pipeline-message">Aucun pipeline ou étape défini pour cette opportunité.</p>
    @endif
</div>
