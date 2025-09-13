<div 
    x-data="{ 
        isEstadoModalOpen: false, 
        isEditEstadoModalOpen: false, 
        estadoToEdit: {id: '', nombre: '', descripcion: ''}, 
        isDeleteEstadoModalOpen: false, 
        estadoToDelete: {id: ''} 
    }"
>
    <!-- Tabla Mobile -->
    <x-admin.tabla-mobile titulo="Estados CAI" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchEstado',
                'filtrosSelect' => [
                    'tipoEstado' => [
                        'label' => 'Tipo',
                        'options' => ['Activo', 'Inactivo', 'Por Vencer', 'Vencido']
                    ]
                ],
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button 
                @click="isEstadoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm"
            >
                Nuevo Estado CAI
            </button>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre Estado CAI</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción Estado CAI</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">1</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Activo</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Estado activo para el CAI</td>
                    <td class="py-2 px-4 flex gap-2">
                        <a href="#" @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {id: 1, nombre: 'Activo', descripcion: 'Estado activo para el CAI'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="#" @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 1}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr class="border-b nunito-regular">
                    <td class="py-2 px-4 nunito-regular">2</td>
                    <td class="py-2 px-4 nunito-regular">Inactivo</td>
                    <td class="py-2 px-4 nunito-regular">Estado inactivo para el CAI</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular">
                        <button @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 2, nombre: 'Inactivo', descripcion: 'Estado inactivo para el CAI'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 2}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="border-b nunito-regular">
                    <td class="py-2 px-4 nunito-regular">3</td>
                    <td class="py-2 px-4 nunito-regular">Por Vencer</td>
                    <td class="py-2 px-4 nunito-regular">CAI próximo a vencer</td>
                    <td class="py-2 px-4 flex gap-2 nunito-regular">
                        <button @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 3, nombre: 'Por Vencer', descripcion: 'CAI próximo a vencer'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 3}" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold">Activo</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">ID: 1</p>
                        </div>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular">Estado activo para el CAI</span></div>
                    </div>
                    <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {id: 1, nombre: 'Activo', descripcion: 'Estado activo para el CAI'}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 1}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Estado CAI -->
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoModalOpen" title="Nuevo Estado CAI"
            submitLabel="Guardar Estado CAI" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_estado_cai" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_estado_cai" name="nombre_estado_cai"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion_estado_cai"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_estado_cai" name="descripcion_estado_cai" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Estado CAI -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEditEstadoModalOpen" title="Editar Estado CAI" itemToEdit="estadoToEdit" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_estado_cai" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre_estado_cai" name="edit_nombre_estado_cai" :value="estadoToEdit?.nombre"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion_estado_cai"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_estado_cai" name="edit_descripcion_estado_cai" rows="2" :value="estadoToEdit?.descripcion"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteEstadoModalOpen" itemToDelete="estadoToDelete"
            message="¿Estás seguro de que quieres eliminar este estado CAI?" />
    </div>
</div>
