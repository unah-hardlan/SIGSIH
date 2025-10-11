<div x-data="{
        isTipoModalOpen: false,
        isTipoEditModalOpen: false,
        tipoToEdit: {id_tipo_movimiento_pk: '', nombre_tipo_movimiento: '', descipcion_tipo_movimiento: ''},
        isTipoDeleteModalOpen: false,
        tipoToDelete: {id_tipo_movimiento_pk: '', nombre_tipo_movimiento: ''},
        filtroNombre: '',
        filtroTipoMovimiento: ''
    }">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID Tipo Movimiento</th>
                    <th class="py-2 px-4 text-left">Nombre</th>
                    <th class="py-2 px-4 text-left">Descripción</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="tipo in [
                    {id_tipo_movimiento_pk: 1, nombre_tipo_movimiento: 'Entrada', descipcion_tipo_movimiento: 'Ingreso de productos'},
                    {id_tipo_movimiento_pk: 2, nombre_tipo_movimiento: 'Salida', descipcion_tipo_movimiento: 'Egreso de productos'}
                ]" :key="tipo.id_tipo_movimiento_pk">
                    <tr>
                        <td x-text="tipo.id_tipo_movimiento_pk"></td>
                        <td x-text="tipo.nombre_tipo_movimiento"></td>
                        <td x-text="tipo.descipcion_tipo_movimiento"></td>
                        <td>
                            <button @click="isTipoEditModalOpen = true; tipoToEdit = {...tipo}"
                                class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click="isTipoDeleteModalOpen = true; tipoToDelete = {...tipo}"
                                class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Modal Nuevo Tipo de Movimiento -->
    <x-admin.form-modal class="nunito-bold" modalName="isTipoModalOpen" title="Nuevo Tipo de Movimiento"
        submitLabel="Guardar" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                placeholder="Nombre del tipo" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
            <textarea class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Descripción"></textarea>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Tipo de Movimiento -->
    <x-admin.edit-modal class="nunito-bold" modalName="isTipoEditModalOpen" title="Editar Tipo de Movimiento"
        itemToEdit="tipoToEdit" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
            <input type="text" x-model="tipoToEdit.nombre_tipo_movimiento"
                class="w-full border rounded px-3 py-2 nunito-regular" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
            <textarea x-model="tipoToEdit.descipcion_tipo_movimiento"
                class="w-full border rounded px-3 py-2 nunito-regular"></textarea>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Tipo de Movimiento -->
    <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoDeleteModalOpen" itemToDelete="tipoToDelete"
        message="¿Seguro que deseas eliminar este tipo de movimiento?" />
</div>