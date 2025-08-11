@vite('resources/css/app.css')
<div class="border-b border-gray-200 dark:border-gray-700 pb-4">
    <nav class="-mb-px flex">
        <button
            wire:click="mountAction('createTask')"
            class="whitespace-nowrap flex items-center gap-2 py-2 px-4 rounded-md font-medium text-sm focus:outline-none s-center space-x-2 transition-colors duration-200"
            style="background-color: #3B82F6; color: #FFFFFF; margin-right: 1rem;" /* bg-blue-500 text-white */
        >
            <x-heroicon-o-clipboard-document-check class="h-5 w-5" />
            <span>Tâche</span>
        </button>
        <button
            @click="activeActionTab = 'evenement'"
            wire:click="mountAction('createEvent')"
            class="whitespace-nowrap py-2 px-4 rounded-md font-medium text-sm focus:outline-none flex items-center gap-2 space-x-2 transition-colors duration-200"
            style="background-color: #A855F7; color: #FFFFFF; margin-right: 1rem;" /* bg-green-500 text-white */
        >
            <x-heroicon-o-calendar class="h-5 w-5" />
            <span>Événement</span>
        </button>
        <button
            @click="activeActionTab = 'appel'"
            wire:click="mountAction('createCall')"
            class="whitespace-nowrap py-2 px-4 rounded-md font-medium text-sm focus:outline-none flex items-center gap-2 space-x-2 transition-colors duration-200"
            style="background-color: #22C55E; color: #FFFFFF; " /* bg-purple-500 text-white */
        >
            <span>Appel</span>
            <x-heroicon-o-phone class="h-5 w-5" />
        </button>
    </nav>
</div>


