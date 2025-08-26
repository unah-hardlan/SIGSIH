@props([
    'modalName',
    'title',
    'submitLabel' => 'Guardar Cambios',
    'itemToEdit',
    'maxWidth' => 'max-w-md',
    'formId' => ''
])

<x-admin.form-modal 
    :modalName="$modalName" 
    :title="$title" 
    :submitLabel="$submitLabel"
    :maxWidth="$maxWidth"
    :formId="$formId">
    <div x-show="{{ $itemToEdit }}" class="space-y-4">
        {{ $slot }}
    </div>
</x-admin.form-modal>
