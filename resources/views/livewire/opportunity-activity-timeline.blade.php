<div class="relative border-l-2 border-blue-500 dark:border-blue-700">
    @forelse($activities as $index => $a)
        <div class="mb-10 ml-6" wire:key="activity-{{ $index }}">
            <span
                class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-8 ring-white dark:ring-gray-900
                @php
$colorClass = match ($a['type']) {
                        'task' => 'bg-blue-500 dark:bg-blue-700',
                        'event' => 'bg-purple-500 dark:bg-purple-700',
                        'call' => 'bg-green-500 dark:bg-green-700',
                        default => 'bg-gray-500 dark:bg-gray-700',
                    }; @endphp
                {{ $colorClass }}">

                @php
                    $icon = match ($a['type']) {
                        'task' => 'clipboard-document-check',
                        'event' => 'calendar',
                        'call' => 'phone',
                        default => 'information-circle',
                    };
                @endphp
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-4 w-4 text-white" />
            </span>

            <div class="flex items-center mb-1 gap-2 ">
                @if ($a['causer_name'])
                    <div
                        class="flex items-center justify-center w-6 h-6 rounded-full bg-black dark:bg-gray-700 text-white dark:text-gray-200 text-sm font-semibold mr-2">
                        {{ $a['causer_initial'] }}
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $a['causer_name'] }}
                    </h3>
                @endif
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ \Carbon\Carbon::parse($a['created_at'])->diffForHumans() }}
                </span>
            </div>

            <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $a['label_value'] ?? '' }}
                </h4>

                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span style="background-color: {{ $a['badge'] }}"
                        class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ $a['statut'] }}
                    </span>

                    @if ($a['label_name'])
                        <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                            {{ $a['label_name'] }}
                        </span>
                    @endif

                    @if ($a['prioritaire'])
                        <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            Prioritaire
                        </span>
                    @endif
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    @if ($a['type'] == 'task' && $a['due_date'])
                        <p>Date d'échéance: {{ \Carbon\Carbon::parse($a['due_date'])->format('d M Y H:i') }}</p>
                    @elseif($a['type'] == 'event')
                        @if ($a['date_debut'] && $a['date_fin'])
                            <p>Début: {{ \Carbon\Carbon::parse($a['date_debut'])->format('d M Y H:i') }}</p>
                            <p>Fin: {{ \Carbon\Carbon::parse($a['date_fin'])->format('d M Y H:i') }}</p>
                        @elseif($a['date_debut'])
                            <p>Début: {{ \Carbon\Carbon::parse($a['date_debut'])->format('d M Y H:i') }}</p>
                        @endif
                    @elseif($a['type'] == 'call' && $a['due_date'])
                        <p>Date de l'appel: {{ \Carbon\Carbon::parse($a['due_date'])->format('d M Y H:i') }}</p>
                    @endif

                    @if ($a['contact'])
                        <p>Contact: {{ $a['contact'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500 dark:text-gray-400">Aucun historique d'activité trouvé pour cette opportunité.</p>
    @endforelse
</div>
