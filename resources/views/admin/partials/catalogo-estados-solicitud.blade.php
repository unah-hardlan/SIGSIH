<div x-data="{
        isEstadoModalOpen: false,
        isEditEstadoModalOpen: false,
        isDeleteEstadoModalOpen: false,
        estadoToEdit: {
            id: null,
            nombre_estado: '',
            descripcion_estado: ''
        },
        estadoToDelete: null,
        searchEstadoSolicitud: '',
        estados: [
            {id: 1, nombre_estado: 'Abierta', descripcion_estado: 'Solicitud recién creada'},
            {id: 2, nombre_estado: 'En Proceso', descripcion_estado: 'Solicitud siendo procesada'},
            {id: 3, nombre_estado: 'Cerrada', descripcion_estado: 'Solicitud completada y cerrada'}
        ]
    }">
    <x-admin.tabla-mobile titulo="Estados de Solicitud" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchEstadoSolicitud',
                'filtrosSelect' => [
                    'estadoSolicitud' => [
                        'label' => 'Estado',
                        'options' => ['Abierta', 'En Proceso', 'Cerrada']
                    ]
                ],
                'ordenarOptions' => [
                    'nombre_estado' => 'Nombre',
                    'id' => 'ID'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isEstadoModalOpen = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                    Nuevo Estado
                </button>
            </div>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre Estado</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="estado in estados.filter(e => !searchEstadoSolicitud || e.nombre_estado.toLowerCase().includes(searchEstadoSolicitud.toLowerCase()))" :key="estado.id">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.id"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.nombre_estado"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.descripcion_estado"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {...estado}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: estado.id}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="estado in estados.filter(e => !searchEstadoSolicitud || e.nombre_estado.toLowerCase().includes(searchEstadoSolicitud.toLowerCase()))" :key="estado.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estado.nombre_estado"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + estado.id"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.descripcion_estado"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {...estado}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: estado.id}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modal Nuevo Estado -->
    <x-admin.form-modal class="nunito-bold" modalName="isEstadoModalOpen" title="Nuevo Estado de Solicitud" submitLabel="Guardar Estado"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="nombre_estado" name="nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_estado" name="descripcion_estado" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditEstadoModalOpen" title="Editar Estado de Solicitud" itemToEdit="estadoToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="edit_nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="edit_nombre_estado" name="edit_nombre_estado" x-model="estadoToEdit.nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion_estado" name="edit_descripcion_estado" rows="2"
                    x-model="estadoToEdit.descripcion_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Estado -->
    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteEstadoModalOpen" itemToDelete="estadoToDelete"
        message="¿Estás seguro de que quieres eliminar el estado?" />
</div>
