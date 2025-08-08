<div class="w-full overflow-x-auto py-4">
    @php
        $pipeline = $opportunity->pipeline;
        $etapes = $pipeline ? $pipeline->etapePipelines->sortBy('ordre') : collect();
        $currentEtapeId = $opportunity->etape_pipeline_id;
    @endphp

    @if($pipeline && $etapes->isNotEmpty())
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pipeline: {{ $pipeline->nom }}</h3>
            <span class="text-sm text-gray-600 dark:text-gray-400" x-data="{ currentStepTitle: '' }" x-text="currentStepTitle"></span>
        </div>

        <div class="flex items-center justify-between relative" style="min-width: {{ count($etapes) * 150 }}px;">
            {{-- Timeline Line --}}
            <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-300 dark:bg-gray-600 transform -translate-y-1/2"></div>

            @foreach($etapes as $etape)
                @php
                    $isActive = $etape->id === $currentEtapeId;
                    $isCompleted = ($opportunity->etapePipeline && $etape->ordre < $opportunity->etapePipeline->ordre) ? true : false;
                @endphp
                <div
                    class="flex flex-col items-center relative z-10 px-4 py-2"
                    x-data="{ tooltip: '{{ $etape->nom }}' }"
                    @mouseover="$dispatch('update-step-title', tooltip)"
                    @mouseleave="$dispatch('update-step-title', '')"
                >
                    {{-- Circle --}}
                    <div class="w-6 h-6 rounded-full border-2
                        {{ $isActive ? 'bg-blue-500 border-blue-500' : ($isCompleted ? 'bg-green-500 border-green-500' : 'bg-gray-400 border-gray-400') }}
                        flex items-center justify-center text-white text-xs font-bold"
                    >
                        {{ $loop->index + 1 }}
                    </div>
                    {{-- Step Name --}}
                    <div class="mt-2 text-sm text-center font-medium
                        {{ $isActive ? 'text-blue-600 dark:text-blue-400' : ($isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400') }}"
                    >
                        {{ $etape->nom }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600 dark:text-gray-400">Aucun pipeline ou étape défini pour cette opportunité.</p>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timeline', () => ({
            currentStepTitle: '',
            init() {
                this.$watch('$store.timeline.currentStepTitle', (value) => {
                    this.currentStepTitle = value;
                });
            }
        }));

        Alpine.store('timeline', {
            currentStepTitle: '',
            updateStepTitle(title) {
                this.currentStepTitle = title;
            }
        });

        document.addEventListener('update-step-title', (event) => {
            Alpine.store('timeline').updateStepTitle(event.detail);
        });
    });
</script>
