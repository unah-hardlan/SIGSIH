@props([
'modalName',
'title',
'submitLabel',
'maxWidth' => 'max-w-xl',
'formId' => '',
'noScroll' => false,
'hideActions' => false,
])

<div x-show="{{ $modalName }}"
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 flex items-center justify-center z-50 p-4 bg-transparent"
  style="background-color: rgba(0,0,0,0.25); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
  @click="{{ $modalName }} = false"
  @keydown.window.escape="{{ $modalName }} = false"
  x-cloak>

  @php
  $containerExtra = $noScroll ? '' : 'max-h-[85vh] overflow-hidden';
  $formExtraClass = $noScroll ? 'modal-compact' : 'overflow-y-auto custom-scrollbar modal-form-scroll';
  @endphp
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-[92vw] sm:max-w-xl mx-auto {{ $containerExtra }} {{ $maxWidth }}" @click.stop>
    <div class="flex justify-between items-center pb-1 px-4 sm:px-6 pt-4 sm:pt-6">
      <h3 class="text-xl sm:text-2xl font-bold text-gray-700 dark:text-white">{{ $title }}</h3>
      <button @click="{{ $modalName }} = false" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white"><i class="fas fa-times"></i></button>
    </div>
    @php
    $showSubmit = !$hideActions && isset($submitLabel) && trim((string)$submitLabel) !== '';
    $cancelText = $showSubmit ? 'Cancelar' : 'Cerrar';
    @endphp
    <form @submit.prevent="$dispatch('modal-submit', { formId: '{{ $formId }}' })" id="{{ $formId }}" class="mt-2 mb-4 space-y-4 px-4 py-3 sm:px-6 sm:py-4 modal-form {{ $formExtraClass }}">
      {{ $slot }}
      <div class="flex flex-col sm:flex-row justify-end pt-4 gap-3">
        <button type="button" @click="{{ $modalName }} = false"
          class="bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 min-w-[120px] w-full sm:w-auto text-sm sm:text-base font-medium transition-colors duration-150 ease-in-out">{{ $cancelText }}</button>
        @if($showSubmit)
        <button type="submit"
          class="bg-blue-600 dark:bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 min-w-[120px] w-full sm:w-auto text-sm sm:text-base font-semibold transition-colors duration-150 ease-in-out">{{ $submitLabel }}</button>
        @endif
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

  /* Mobile tweaks: reduce sizes and spacing on small screens */
  @media (max-width: 640px) {
    .modal-form {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    .modal-form form input,
    .modal-form form select,
    .modal-form form textarea {
      font-size: 0.95rem !important;
      padding: 0.4rem 0 !important;
    }

    .modal-form form label {
      font-size: 0.9rem !important;
    }

    .modal-form .text-xl {
      font-size: 1.05rem !important;
    }

    .modal-form button {
      font-size: 0.95rem !important;
      padding: 0.5rem 0.75rem !important;
    }

    /* Reduce overall modal vertical footprint */
    .max-h-\[85vh\] {
      max-height: 80vh !important;
    }
  }

  form input,
  form select,
  form textarea {
    border-color: black !important;
    border-width: 1px;
    font-size: 1rem !important;
    padding: 0.5rem 1rem !important;
    border-radius: 0.5rem !important;
  }

  form input:focus,
  form select:focus,
  form textarea:focus {
    outline: none !important;
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
    padding: 0.5rem 1rem !important;
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

  /* Compact mode to avoid scroll and fit content */
  .modal-compact * {
    font-size: 0.95rem !important;
  }

  .modal-compact label {
    font-size: 0.95rem !important;
  }

  .modal-compact input,
  .modal-compact select,
  .modal-compact textarea {
    padding: 0.45rem 0.8rem !important;
  }

  .modal-compact .text-xl,
  .modal-compact .text-2xl {
    font-size: 1.15rem !important;
  }

  /* Scroll container when enabled */
  .modal-form-scroll {
    max-height: calc(85vh - 80px);
  }

  select:focus {
    outline: none;
    box-shadow: none;
  }
</style>