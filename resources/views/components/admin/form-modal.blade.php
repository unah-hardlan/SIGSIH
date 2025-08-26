@props([
    'modalName',
    'title',
    'submitLabel',
    'maxWidth' => 'max-w-md', {{-- Cambiado el valor por defecto a md para un mejor equilibrio --}}
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
    {{-- Aquí es donde haremos el cambio principal --}}
    <div class="bg-white rounded-lg shadow-xl p-4 w-11/12 sm:w-full {{ $maxWidth }} mx-auto" @click.stop>
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-xl font-bold text-gray-700">{{ $title }}</h3>
            <button @click="{{ $modalName }} = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
        </div>
        <form @submit.prevent="$dispatch('modal-submit', { formId: '{{ $formId }}' })" id="{{ $formId }}" class="mt-4 space-y-4">
            {{ $slot }}
            <div class="flex flex-col sm:flex-row justify-end pt-4 gap-2">
                <button type="button" @click="{{ $modalName }} = false"
                    class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 w-full sm:w-auto">Cancelar</button>
                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full sm:w-auto">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
</div>