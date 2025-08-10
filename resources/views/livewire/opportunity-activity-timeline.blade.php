<div class="relative border-l-2 border-blue-500 dark:border-blue-700">
    @php
        $attributeLabels = [
            'titre' => 'Titre',
            'description' => 'Description',
            'statut' => 'Statut',
            'due_date' => 'Date d\'échéance',
            'prioritaire' => 'Prioritaire',
            'label' => 'Label',
            'date_debut' => 'Date de début',
            'date_fin' => 'Date de fin',
            'is_all_day' => 'Toute la journée',
            'contact' => 'Contact',
        ];
    @endphp
    @forelse($activities as $activity)
        <div class="mb-10 ml-6">
            <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
    @php
        $icon = match ($activity->type) {
            'task' => 'heroicon-o-clipboard-document-check',
            'event' => 'heroicon-o-calendar',
            'call' => 'heroicon-o-phone',
            default => 'heroicon-o-information-circle', // Default icon if type is not matched
        };
    @endphp
    <x-heroicon-o-{{ $icon }} class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" />
</span>
            <div class="flex items-center mb-1">
                @if($activity->causer)
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold mr-2">
                        {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $activity->causer->name }}
                    </h3>
                @endif
            </div>
            <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                {{ $activity->created_at->diffForHumans() }}
            </time>
            @if($activity->displayProperties->isNotEmpty())
                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                    <p class="font-semibold mb-1">Détails :</p>
                    @foreach($activity->displayProperties as $key => $value)
                        @php
                            $label = $attributeLabels[$key] ?? ucfirst($key);
                        @endphp
                        @if(is_array($value) && isset($value['old']) && isset($value['new']))
                            <p>
                                <strong>{{ $label }}:</strong>
                                <span class="text-red-500 line-through">{{ $value['old'] }}</span>
                                <span class="text-green-500">{{ $value['new'] }}</span>
                            </p>
                        @else
                            <p><strong>{{ $label }}:</strong> {{ $value }}</p>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <p class="text-gray-500 dark:text-gray-400">Aucun historique d\'activité trouvé pour cette opportunité.</p>
    @endforelse
</div>
