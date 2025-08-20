@props([
    'modalName',
    'title',
    'submitLabel',
    'maxWidth' => 'max-w-2xl',
    'formId' => ''
])

<div x-show="{{ $modalName }}" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
     @click.away="{{ $modalName }} = false"
     style="display: none;">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full {{ $maxWidth }}" @click.stop>
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-xl font-bold text-gray-700">{{ $title }}</h3>
            <button @click="{{ $modalName }} = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
        </div>
        <form @submit.prevent="$dispatch('modal-submit', { formId: '{{ $formId }}' })" id="{{ $formId }}" class="mt-4 space-y-4">
            {{ $slot }}
            <div class="flex justify-end pt-4">
                <button type="button" @click="{{ $modalName }} = false"
                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancelar</button>
                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
</div>
