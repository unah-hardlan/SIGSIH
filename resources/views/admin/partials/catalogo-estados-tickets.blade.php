<div x-data="{
        isModalOpenEstadoTicket: false,
        isEditModalOpenEstadoTicket: false,
        isDeleteModalOpenEstadoTicket: false,
        itemToEdit: {id: '', nombre: '', descripcion: ''},
        itemToDelete: {id: ''},
        searchEstadoTicket: '',
        estados: [
            {id: 'E-001', nombre: 'Pendiente', descripcion: 'Ticket en espera de atención'},
            {id: 'E-002', nombre: 'En proceso', descripcion: 'Ticket siendo atendido activamente'},
            {id: 'E-003', nombre: 'Finalizado', descripcion: 'Ticket resuelto completamente'}
        ]
    }">
    <x-admin.tabla-mobile titulo="Estados de Tickets" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchEstadoTicket',
                'filtrosSelect' => [
                    'estado' => [
                        'label' => 'Estados',
                        'options' => ['Pendiente', 'En proceso', 'Finalizado']
                    ]
                ],
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Estado'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isModalOpenEstadoTicket = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                    Agregar Estado
                </button>
            </div>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID Estado</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="estado in estados.filter(e => !searchEstadoTicket || e.nombre.toLowerCase().includes(searchEstadoTicket.toLowerCase()))" :key="estado.id">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.id"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.nombre"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.descripcion"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click.prevent="isEditModalOpenEstadoTicket = true; itemToEdit = {...estado}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isDeleteModalOpenEstadoTicket = true; itemToDelete = {id: estado.id}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="estado in estados.filter(e => !searchEstadoTicket || e.nombre.toLowerCase().includes(searchEstadoTicket.toLowerCase()))" :key="estado.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estado.nombre"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + estado.id"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.descripcion"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditModalOpenEstadoTicket = true; itemToEdit = {...estado}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteModalOpenEstadoTicket = true; itemToDelete = {id: estado.id}"
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
    <x-admin.form-modal class="nunito-bold"
        modalName="isModalOpenEstadoTicket" 
        title="Nuevo Estado de Ticket" 
        submitLabel="Guardar Estado">
        <div class="space-y-4">
            <div>
                <label for="id_estado" class="block text-sm font-medium text-gray-700 nunito-bold">ID Estado</label>
                <input type="text" id="id_estado" name="id_estado" placeholder="E-004" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="nombre_estado" name="nombre_estado" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_estado" name="descripcion_estado" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditModalOpenEstadoTicket" 
        title="Editar Estado de Ticket" 
        itemToEdit="itemToEdit">
        <div class="space-y-4">
            <div>
                <label for="edit_id_estado" class="block text-sm font-medium text-gray-700 nunito-bold">ID Estado</label>
                <input type="text" id="edit_id_estado" name="edit_id_estado" :value="itemToEdit.id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="edit_nombre_estado" name="edit_nombre_estado" :value="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion_estado" name="edit_descripcion_estado" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" x-text="itemToEdit.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Estado -->
    <x-admin.confirmation-modal class="nunito-regular"
        modalName="isDeleteModalOpenEstadoTicket"
        itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar el estado"
    />
</div>
