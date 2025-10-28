@props([])

<div x-show="numbers.length > perPage" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
    <div class="mb-2">
        <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
            Mostrando
            <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPage - 1) * perPage + 1"></strong>
            a
            <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPage * perPage, numbers.length)"></strong>
            de
            <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="numbers.length"></strong>
            resultados
        </span>
    </div>

    <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
        <button @click="prevPage()" :disabled="currentPage === 1"
                :class="currentPage === 1
                    ? 'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-400 dark:bg-gray-800/60 dark:text-gray-500'
                    : 'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors bg-gray-400 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600'">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Anterior</span>
        </button>

        <div class="flex items-center gap-1">
            <template x-for="page in Array.from({length: totalPages()}, (_, i) => i + 1).slice(Math.max(0, currentPage - 3), currentPage + 2)" :key="page">
                <button @click="currentPage = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform"
                        :class="page === currentPage
                            ? 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'">
                    <span x-text="page"></span>
                </button>
            </template>
        </div>

        <button @click="nextPage()" :disabled="currentPage === totalPages()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
            <span>Siguiente</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>
</div>