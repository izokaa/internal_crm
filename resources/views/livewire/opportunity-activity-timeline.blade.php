<div class="relative border-l border-gray-200 dark:border-gray-700">
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
            <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $activity->description }}
                @if($activity->causer)
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ml-3">
                        {{ $activity->causer->name }}
                    </span>
                @endif
            </h3>
            <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                {{ $activity->created_at->diffForHumans() }}
            </time>
            @if($activity->properties->isNotEmpty())
                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                    @if($activity->event === 'updated' && $activity->properties->has('old') && $activity->properties->has('attributes'))
                        <p class="font-semibold mb-1">Changements :</p>
                        @foreach($activity->properties['attributes'] as $attribute => $newValue)
                            @if(isset($activity->properties['old'][$attribute]))
                                <p>
                                    <strong>{{ ucfirst($attribute) }}:</strong>
                                    <span class="text-red-500 line-through">{{ $activity->properties['old'][$attribute] }}</span>
                                    <span class="text-green-500">{{ $newValue }}</span>
                                </p>
                            @else
                                <p><strong>{{ ucfirst($attribute) }}:</strong> {{ $newValue }}</p>
                            @endif
                        @endforeach
                    @else
                        <p class="font-semibold mb-1">Détails :</p>
                        @foreach($activity->properties as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $subKey => $subValue)
                                    <p><strong>{{ ucfirst($subKey) }}:</strong> {{ $subValue }}</p>
                                @endforeach
                            @else
                                <p><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</p>
                            @endif
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    @empty
        <p class="text-gray-500 dark:text-gray-400">Aucun historique d'activité trouvé pour cette opportunité.</p>
    @endforelse
</div>