<div x-data="{
        isTipoModalOpen: false,
        isTipoEditModalOpen: false,
        tipoToEdit: {id_tipo_movimiento_pk: '', nombre_tipo_movimiento: '', descipcion_tipo_movimiento: ''},
        isTipoDeleteModalOpen: false,
        tipoToDelete: {id_tipo_movimiento_pk: '', nombre_tipo_movimiento: ''},
        filtroNombre: '',
        filtroTipoMovimiento: ''
    }">
    <x-admin.tabla-mobile titulo="Tipo de Movimiento" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
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
        </x-slot>
        <x-slot name="boton">
            <button @click="isTipoModalOpen = true"
                class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular flex items-center justify-center text-sm">
                Nuevo tipo
            </button>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
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

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="tipo in [
                    {id_tipo_movimiento_pk: 1, nombre_tipo_movimiento: 'Entrada', descipcion_tipo_movimiento: 'Movimiento de ingreso de productos'},
                    {id_tipo_movimiento_pk: 2, nombre_tipo_movimiento: 'Salida', descipcion_tipo_movimiento: 'Movimiento de egreso de productos'}
                ]" :key="tipo.id_tipo_movimiento_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                    x-text="tipo.nombre_tipo_movimiento"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular"
                                    x-text="'ID: ' + tipo.id_tipo_movimiento_pk"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span
                                    class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span
                                    class="text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="tipo.descipcion_tipo_movimiento"></span></div>
                        </div>
                        <div
                            class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click="isTipoEditModalOpen = true; tipoToEdit = {...tipo}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click="isTipoDeleteModalOpen = true; tipoToDelete = {...tipo}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

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