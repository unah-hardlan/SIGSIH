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
    <template x-if="{{ $itemToEdit }}">
        <div class="space-y-4">
            {{ $slot }}
        </div>
    </template>
</x-admin.form-modal>
