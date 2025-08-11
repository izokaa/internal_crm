@filamentStyles
@vite('resources/css/app.css')

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
    @forelse($activities as $activityAction)
        <div class="mb-10 ml-6">
        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">

            @php
            $icon = match ($activityAction['activity']->type) {
            'task' => 'clipboard-document-check',
            'event' => 'calendar',
            'call' => 'phone',
            default => 'information-circle', // Default icon if type is not matched
            };
            @endphp

            @switch($activityAction['activity']->type)
            @case('task')
            <x-heroicon-o-calendar class="h-3 w-3" />
            @break

            @case('event')
            <x-heroicon-o-clipboard-document-check class="h-3 w-3" />
            @break

            @default
            <x-heroicon-o-phone class="h-3 w-3" />
            @break

            @endswitch
        </span>

            <div class="flex items-center mb-1 gap-2 ">
                @if($activityAction['activity']->causer)
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-black dark:bg-gray-700 text-white dark:text-gray-200 text-sm font-semibold mr-2">
                        {{ strtoupper(substr($activityAction['activity']->causer->name, 0, 1)) }}
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $activityAction['activity']->causer->name }}

                {{ $activityAction['activity'] }}
                    </h3>
                @endif
            </div>
            <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                {{ $activityAction['activity']->created_at->diffForHumans() }}
            </time>
        </div>
    @empty
        <p class="text-gray-500 dark:text-gray-400">Aucun historique d\'activité trouvé pour cette opportunité.</p>
    @endforelse
</div>
