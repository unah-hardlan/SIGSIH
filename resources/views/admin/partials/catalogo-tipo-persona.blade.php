<div x-data="{ 
    isModalOpenTipoPersona: false, 
    isEditModalOpenTipoPersona: false, 
    isDeleteModalOpenTipoPersona: false, 
    itemToEdit: {id: '', nombre: '', descripcion: ''}, 
    itemToDelete: {id: ''}, 
    searchTipoPersona: '' 
}">
    <x-admin.tabla-mobile titulo="Gestión de Tipos de Persona" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchTipoPersona',
                'ordenarOptions' => [
                'nombre' => 'Nombre Tipo Persona',
                'id' => 'ID Tipo Persona'
                ]
                ])
                <div class="p-4 text-sm text-gray-600">Esta vista fue eliminada. El catálogo de Tipos de Persona ya no
                    está disponible.</div>