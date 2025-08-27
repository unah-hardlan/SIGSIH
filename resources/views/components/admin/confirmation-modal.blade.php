@props([
    'modalName',
    'title' => 'Confirmar',
    'itemToDelete',
    'itemNameProperty' => 'nombre',
    'message'
])

<div x-show="{{ $modalName }}" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4" {{-- AÑADIDO: p-4 para margen exterior --}}
    @click="{{ $modalName }} = false"
    @keydown.window.escape="{{ $modalName }} = false"
    x-cloak>
    <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 sm:w-full max-w-sm mx-auto" @click.stop> {{-- MODIFICADO: w-11/12, sm:w-full, max-w-sm, mx-auto --}}
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-xl font-bold text-gray-700">{{ $title }}</h3>
            <button @click="{{ $modalName }} = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
        </div>
        <div class="mt-4">
            <p>{{ $message }} <strong x-text="{{ $itemToDelete }} ? {{ $itemToDelete }}.{{ $itemNameProperty }} : ''"></strong>?</p>
        </div>
        <div class="flex justify-end pt-4">
            <button type="button" @click="{{ $modalName }} = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mr-2 transition-colors duration-200 ease-in-out">Cancelar</button>
            <button type="button" @click="$dispatch('confirm-delete'); {{ $modalName }} = false" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors duration-200 ease-in-out">Confirmar</button>
        </div>
    </div>
</div>