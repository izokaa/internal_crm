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
        <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-8 ring-white dark:ring-gray-900
            @php
                $colorClass = match ($activityAction['activity']->type) {
                    'task' => 'bg-blue-500 dark:bg-blue-700',
                    'event' => 'bg-purple-500 dark:bg-purple-700',
                    'call' => 'bg-green-500 dark:bg-green-700',
                    default => 'bg-gray-500 dark:bg-gray-700', // Default color
                };
            @endphp
            {{ $colorClass }}">

            @php
            $icon = match ($activityAction['activity']->type) {
            'task' => 'clipboard-document-check',
            'event' => 'calendar',
            'call' => 'phone',
            default => 'information-circle', // Default icon if type is not matched
            };
            @endphp
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-4 w-4 text-white" />
        </span>

            <div class="flex items-center mb-1 gap-2 ">
                @if($activityAction['causer'])
                    <div class="flex items-center justify-center w-6 h-6 rounded-full bg-black dark:bg-gray-700 text-white dark:text-gray-200 text-sm font-semibold mr-2">
                        {{ strtoupper(substr($activityAction['causer']->name, 0, 1)) }}
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $activityAction['causer']->name }}
                    </h3>
                @endif
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $activityAction['activity']->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $activityAction['activity']->titre }}
                </h4>

                @if($activityAction['activity']->description)
                    <p class="text-gray-700 dark:text-gray-300 text-sm mb-2">
                        {{ $activityAction['activity']->description }}
                    </p>
                @endif

                <div class="flex flex-wrap items-center gap-2 mb-2">
                    @php
                        $statutColors = [
                            	App\Enums\ActivityStatut::OVERDUE->value => 'bg-red-500 text-white',
                            	App\Enums\ActivityStatut::TODO->value => 'bg-blue-500 text-white',
                            	App\Enums\ActivityStatut::UPCOMING->value => 'bg-yellow-500 text-white',
                            	App\Enums\ActivityStatut::COMPLETED->value => 'bg-green-500 text-white',
                        ];
                        $currentStatutColor = $statutColors[$activityAction['activity']->statut->value] ?? 'bg-gray-500 text-white';
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $currentStatutColor }}">
                        {{ $activityAction['activity']->statut->value }}
                    </span>

                    @if($activityAction['activity']->label)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                            {{ $activityAction['activity']->label->name }}
                        </span>
                    @endif

                    @if($activityAction['activity']->prioritaire)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            Prioritaire
                        </span>
                    @endif
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    @if($activityAction['activity']->type == 'task')
                        @if($activityAction['activity']->due_date)
                            <p>Date d'échéance: {{ $activityAction['activity']->due_date->format('d M Y H:i') }}</p>
                        @endif
                    @elseif($activityAction['activity']->type == 'event')
                        @if($activityAction['activity']->date_debut && $activityAction['activity']->date_fin)
                            <p>Début: {{ $activityAction['activity']->date_debut->format('d M Y H:i') }}</p>
                            <p>Fin: {{ $activityAction['activity']->date_fin->format('d M Y H:i') }}</p>
                        @elseif($activityAction['activity']->date_debut)
                            <p>Début: {{ $activityAction['activity']->date_debut->format('d M Y H:i') }}</p>
                        @endif
                        @if($activityAction['activity']->is_all_day)
                            <p>Toute la journée</p>
                        @endif
                    @elseif($activityAction['activity']->type == 'call')
                        @if($activityAction['activity']->due_date)
                            <p>Date de l'appel: {{ $activityAction['activity']->due_date->format('d M Y H:i') }}</p>
                        @endif
                    @endif

                    @if($activityAction['activity']->contact)
                        <p>Contact: {{ $activityAction['activity']->contact->name }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500 dark:text-gray-400">Aucun historique d\'activité trouvé pour cette opportunité.</p>
    @endforelse
</div>
