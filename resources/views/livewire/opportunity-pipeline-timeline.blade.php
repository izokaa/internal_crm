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
                                <input type="date" wire:model.live="dateEcheance">
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
