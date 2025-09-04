<div 
    x-data="{ 
        isTipoVisitaModalOpen: false,
        isEditModalOpen: false,
        tipoVisitaToEdit: null,
        isDeleteModalOpen: false,
        tipoVisitaToDelete: null
    }"
    class="bg-white dark:bg-gray-900 rounded-lg shadow p-6"
>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full">
            <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Tipo de Visita</h2>
            <div class="flex flex-wrap gap-2 items-center ml-0 sm:ml-4">
                @include('partials.filtros-generales', [
                    'searchModel' => 'filtroTipoVisita',
                    'ordenarOptions' => [
                        'nombre' => 'Nombre',
                        'id' => 'ID Tipo'
                    ]
                ])
            </div>
        </div>
        <button 
            @click="isTipoVisitaModalOpen = true"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm"
        >
            Nuevo tipo de visita
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left dark:text-white">ID Tipo</th>
                    <th class="py-2 px-4 text-left dark:text-white">Nombre</th>
                    <th class="py-2 px-4 text-left dark:text-white">Descripción</th>
                    <th class="py-2 px-4 text-left dark:text-white">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b nunito-regular dark:border-gray-700">
                    <td class="py-2 px-4 dark:text-white">TV-001</td>
                    <td class="py-2 px-4 dark:text-white">Técnica</td>
                    <td class="py-2 px-4 dark:text-white">Visita para revisión técnica de equipos</td>
                    <td class="py-2 px-4 flex gap-2 dark:text-white">
                        <a href="#" @click.prevent="isEditModalOpen = true; tipoVisitaToEdit = {id: 'TV-001', nombre: 'Técnica', descripcion: 'Visita para revisión técnica de equipos'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="#" @click.prevent="isDeleteModalOpen = true; tipoVisitaToDelete = {id: 'TV-001'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Nuevo Tipo de Visita -->
    <x-admin.form-modal class="nunito-bold" modalName="isTipoVisitaModalOpen" title="Nuevo Tipo de Visita"
        submitLabel="Guardar Tipo de Visita" maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nombre_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                <input type="text" id="nombre_tipo_visita" name="nombre_tipo_visita"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="descripcion_tipo_visita"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_tipo_visita" name="descripcion_tipo_visita" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Tipo de Visita -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Tipo de Visita" itemToEdit="tipoVisitaToEdit" maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_nombre_tipo_visita" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                <input type="text" id="edit_nombre_tipo_visita" name="edit_nombre_tipo_visita" :value="tipoVisitaToEdit?.nombre"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div class="col-span-2">
                <label for="edit_descripcion_tipo_visita"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion_tipo_visita" name="edit_descripcion_tipo_visita" rows="2" :value="tipoVisitaToEdit?.descripcion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación -->
    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpen" itemToDelete="tipoVisitaToDelete"
        message="¿Estás seguro de que quieres eliminar este tipo de visita?" />
</div>
