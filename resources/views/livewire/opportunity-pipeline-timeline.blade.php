<div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
    @php
        $pipeline = $opportunity->pipeline;
        $etapes = $pipeline ? $pipeline->etapePipelines->sortBy('ordre') : collect();
        $currentEtape = $opportunity->etapePipeline;

        // Calculate progress percentage
        $totalEtapes = $etapes->count();
        $currentEtapeIndex = $currentEtape ? $etapes->search(fn($e) => $e->id === $currentEtape->id) : -1;
        $progressPercentage = 0;
        if ($totalEtapes > 1 && $currentEtapeIndex !== false && $currentEtapeIndex >= 0) {
            $progressPercentage = ($currentEtapeIndex / ($totalEtapes - 1)) * 100;
        }

        // Map status enums to Tailwind CSS classes for cleaner rendering
        $statusClasses = function ($status) {
            return match ($status) {
                \App\Enums\OpportunityStatut::OPEN => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                \App\Enums\OpportunityStatut::WON => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                \App\Enums\OpportunityStatut::LOST => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                \App\Enums\OpportunityStatut::LATE => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                \App\Enums\OpportunityStatut::CANCELED => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                \App\Enums\OpportunityStatut::CLOSED => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            };
        };
    @endphp

    @if($pipeline && $etapes->isNotEmpty())
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6 md:gap-4 mb-8">
            <!-- Left Corner -->
            <div class="flex flex-col gap-2 items-center md:items-start">
                <h4 class="text-xl font-semibold text-gray-900 dark:text-white capitalize">
                    {{ $opportunity->contact->nom }} {{ $opportunity->contact->prenom }}
                </h4>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $opportunity->prefix }} - {{ $opportunity->id }}</span>
                <div class="mt-2" x-data="{ open: false }" @click.away="open = false">
                    <div @click="open = !open" @class([
                        'inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium cursor-pointer',
                        $statusClasses($selectedStatus),
                    ])>
                        {{ $selectedStatus->getLabel() }}
                        <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <div x-show="open" x-transition class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none">
                        <div class="py-1" role="menu" aria-orientation="vertical">
                            @foreach ($statuses as $statusValue)
                                <a href="#" wire:click.prevent="updateStatus('{{ $statusValue->value }}')" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">
                                    <span @class([
                                        'inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium',
                                        $statusClasses($statusValue),
                                    ])>
                                        {{ $statusValue->getLabel() }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center Info -->
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $pipeline->nom }}</h3>
                @if($currentEtape)
                    <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Étape actuelle :</span>
                        <span class="font-semibold text-blue-700 dark:text-blue-400">{{ $currentEtape->nom }}</span>
                    </p>
                @endif
            </div>

            <!-- Right Corner -->
            <div class="flex flex-col gap-2 items-center md:items-end">
                <h3 class="font-bold text-xl text-gray-900 dark:text-white"> {{ $opportunity->montant_estime }} {{ $opportunity->devise }}</h3>
                <div class="mt-2 flex items-center gap-2">
                    <label for="date_echeance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Date d'échéance:</label>
                    <input type="date" id="date_echeance" wire:model.live="dateEcheance" class="block w-full text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <span class="text-sm text-gray-400 dark:text-gray-500">Source: {{ $opportunity->source->nom }}</span>
            </div>
        </div>

        <!-- Timeline Visualizer -->
        <div class="relative pt-10 md:pt-0" style="--progress-percentage: {{ $progressPercentage }}%;">
            <!-- Base Line -->
            <div class="absolute top-10 left-5 md:top-5 md:left-0 w-1 md:w-full h-full md:h-1 bg-gray-300 dark:bg-gray-600 -translate-x-1/2 md:translate-x-0 md:-translate-y-1/2"></div>

            <!-- Progress Line -->
            <div class="absolute top-10 left-5 md:top-5 md:left-0 w-1 md:w-[var(--progress-percentage)] h-[var(--progress-percentage)] md:h-1 bg-green-600 dark:bg-green-500 -translate-x-1/2 md:translate-x-0 md:-translate-y-1/2 transition-all duration-500 ease-in-out"></div>

            <!-- Etapes (Steps) -->
            <div class="flex flex-col md:flex-row md:justify-between">
                @foreach($etapes as $etape)
                    @php
                        $etapeIndex = $loop->index;
                        $isCompleted = $currentEtape && $currentEtapeIndex !== false && $etapeIndex < $currentEtapeIndex;
                        $isActive = $currentEtape && $etape->id === $currentEtape->id;
                        $isClickable = !$isActive;

                        $etapeClasses = \Illuminate\Support\Arr::toCssClasses([
                            'group flex flex-row items-center md:flex-col md:items-center relative',
                            'cursor-pointer' => $isClickable,
                        ]);

                        $circleClasses = \Illuminate\Support\Arr::toCssClasses([
                            'w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold text-white border-4 transition-all duration-300',
                            'bg-gray-400 border-gray-300 dark:bg-gray-500 dark:border-gray-600' => !$isActive && !$isCompleted,
                            'group-hover:scale-110 group-hover:brightness-110' => $isClickable,
                            'bg-green-600 border-green-600 dark:bg-green-500 dark:border-green-500' => $isCompleted,
                            'bg-blue-600 border-blue-600 dark:bg-blue-500 dark:border-blue-500 scale-110' => $isActive,
                        ]);

                        $nameClasses = \Illuminate\Support\Arr::toCssClasses([
                            'ml-6 md:ml-0 md:mt-3 text-sm font-medium text-center w-28',
                            'text-gray-600 dark:text-gray-400' => !$isActive,
                            'text-blue-700 dark:text-blue-300 font-bold' => $isActive,
                        ]);
                    @endphp
                    <div @if($isClickable) wire:click="updateEtape({{ $etape->id }})" @endif class="{{ $etapeClasses }}">
                        <div class="{{ $circleClasses }}">{{ $loop->index + 1 }}</div>
                        <div class="{{ $nameClasses }}">{{ $etape->nom }}</div>
                    </div>
                @endforeach
            </div>
        </div>

    @else
        <p class="text-center text-gray-500 dark:text-gray-400 italic py-8">
            Aucun pipeline ou étape défini pour cette opportunité.
        </p>
    @endif
</div>
