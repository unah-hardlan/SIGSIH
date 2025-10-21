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
    <div class="space-y-4" x-show="{{ $itemToEdit }}" x-cloak>
        {{ $slot }}
    </div>
</x-admin.form-modal>