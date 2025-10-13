@props([
    'modalName',
    'title',
    'submitLabel',
  'maxWidth' => 'max-w-xl',
    'formId' => ''
])

<div x-show="{{ $modalName }}" wire:ignore.self
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-black dark:bg-opacity-70 flex items-center justify-center z-50 p-4"
  @click="{{ $modalName }} = false"
  @keydown.window.escape="{{ $modalName }} = false"
  x-cloak>
    
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-11/12 sm:w-full max-h-[90vh] overflow-hidden {{ $maxWidth }}" @click.stop>
    <div class="flex justify-between items-center pb-1 px-6 pt-6">
      <h3 class="text-2xl font-bold text-gray-700 dark:text-white">{{ $title }}</h3>
      <button @click="{{ $modalName }} = false" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white"><i class="fas fa-times"></i></button>
    </div>
  <form @submit.prevent="$dispatch('modal-submit', { formId: '{{ $formId }}' })" id="{{ $formId }}" class="mt-2 mb-4 space-y-4 overflow-y-auto px-6 py-4 custom-scrollbar" style="max-height: calc(85vh - 80px);"> 
      {{ $slot }}
      <div class="flex flex-col sm:flex-row justify-end pt-6 gap-3">
        <button type="button" @click="{{ $modalName }} = false"
          class="bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white px-5 py-2 rounded hover:bg-gray-400 dark:hover:bg-gray-500 min-w-[120px] w-full sm:w-auto text-base font-semibold transition-colors duration-200 ease-in-out">Cancelar</button>
        <button type="submit"
          class="bg-blue-500 dark:bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-600 dark:hover:bg-blue-700 min-w-[120px] w-full sm:w-auto text-base font-semibold transition-colors duration-200 ease-in-out">{{ $submitLabel }}</button>
      </div>
    </form>
  </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-track {
  background: #374151;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #6b7280;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #555;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

form input,
form select,
form textarea {
  border-color: #A1A1A1 !important;
  border-width: 1px;
  font-size: 1rem !important;
  padding: 0.75rem 1rem !important;
  border-radius: 0.5rem !important;
}

form input:focus,
form select:focus,
form textarea:focus {
  outline: none;
  border-color: black !important;
}

form label {
  color: #374151;
  font-size: 1rem !important;
  font-weight: 600 !important;
  margin-bottom: 0.25rem !important;
}

.dark form input,
.dark form select, 
.dark form textarea {
  border-color: #9ca3af !important;
  background-color: #1f2937 !important;
  color: white !important;
  font-size: 1rem !important;
  padding: 0.75rem 1rem !important;
  border-radius: 0.5rem !important;
}

.dark form input:focus,
.dark form select:focus,
.dark form textarea:focus {
  border-color: white !important;
  outline: none;
}

.dark form label {
  color: white !important;
}

.dark form input::placeholder,
.dark form select::placeholder,
.dark form textarea::placeholder {
  color: #9ca3af !important;
}

/* Asegurar que todos los textos sean blancos en modo oscuro */
.dark form .block,
.dark form .font-medium,
.dark form .nunito-bold {
  color: white !important;
}
</style>