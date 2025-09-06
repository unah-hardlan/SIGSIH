<div x-data="{
        isTipoModalOpen: false,
        isTipoEditModalOpen: false,
        tipoToEdit: {id_tipo_movimiento_pk: '', nombre_tipo_movimiento: '', descipcion_tipo_movimiento: ''},
        isTipoDeleteModalOpen: false,
        tipoToDelete: {id_tipo_movimiento_pk: '', nombre_tipo_movimiento: ''},
        filtroNombre: '',
        filtroTipoMovimiento: ''
    }">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full">
                <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Tipo de Movimiento</h2>
                <div class="flex flex-wrap gap-2 items-center ml-0 sm:ml-4">
                    @include('partials.filtros-generales', [
                        'searchModel' => 'filtroNombre',
                        'filtrosSelect' => [
                            'filtroTipoMovimiento' => [
                                'label' => 'Tipo de Movimiento',
                                'options' => ['Entrada', 'Salida']
                            ]
                        ],
                        'ordenarOptions' => [
                            'nombre_tipo_movimiento' => 'Nombre',
                            'id_tipo_movimiento_pk' => 'ID Tipo Movimiento'
                        ]
                    ])
                </div>
            </div>
            <button @click="isTipoModalOpen = true"
                class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular flex items-center justify-center text-sm">Nuevo tipo
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
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
                        {id_tipo_movimiento_pk: 1, nombre_tipo_movimiento: 'Entrada', descipcion_tipo_movimiento: 'Movimiento de ingreso de productos'},
                        {id_tipo_movimiento_pk: 2, nombre_tipo_movimiento: 'Salida', descipcion_tipo_movimiento: 'Movimiento de egreso de productos'}
                        ]" :key="tipo.id_tipo_movimiento_pk">
                        <tr class="border-b dark:border-gray-700 nunito-regular"
                            x-show="(!filtroNombre || tipo.nombre_tipo_movimiento.toLowerCase().includes(filtroNombre.toLowerCase())) && 
                                   (!filtroTipoMovimiento || tipo.nombre_tipo_movimiento === filtroTipoMovimiento)">>
                            <td class="py-2 px-4 dark:text-white" x-text="tipo.id_tipo_movimiento_pk"></td>
                            <td class="py-2 px-4 dark:text-white" x-text="tipo.nombre_tipo_movimiento"></td>
                            <td class="py-2 px-4 dark:text-white" x-text="tipo.descipcion_tipo_movimiento"></td>
                            <td class="py-2 px-4 flex gap-2 dark:text-white">
                                <a href="#" @click="isTipoEditModalOpen = true; tipoToEdit = {...tipo}"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isTipoDeleteModalOpen = true; tipoToDelete = {...tipo}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Modal Nuevo Tipo de Movimiento -->
        <x-admin.form-modal class="nunito-bold" modalName="isTipoModalOpen" title="Nuevo Tipo de Movimiento" submitLabel="Guardar"
            maxWidth="max-w-md">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Nombre del tipo" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Descripción"></textarea>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Tipo de Movimiento -->
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoEditModalOpen" title="Editar Tipo de Movimiento" itemToEdit="tipoToEdit"
            maxWidth="max-w-md">
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
</div>