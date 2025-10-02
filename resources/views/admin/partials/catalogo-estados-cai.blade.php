<div x-data="{ 
    isEstadoModalOpen: false, 
    isEditEstadoModalOpen: false, 
    estadoToEdit: {id: '', nombre: '', descripcion: ''}, 
    isDeleteEstadoModalOpen: false, 
    estadoToDelete: {id: ''} 
}" class="overflow-x-auto w-full">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 mt-6 w-full">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 pb-4 mb-4 border-b dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
            <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Estados CAI</h2>
            <button @click="isEstadoModalOpen = true" class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo Estado CAI</button>
        </div>
        <div class="mb-4">
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
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr class="border-0">
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 first:rounded-tl-lg border-0">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Nombre Estado CAI</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Descripción Estado CAI</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 last:rounded-tr-lg border-0">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 nunito-regular border-t-0">1</td>
                        <td class="py-2 px-4 nunito-regular border-t-0">Activo</td>
                        <td class="py-2 px-4 nunito-regular border-t-0">Estado activo para el CAI</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular border-t-0">
                            <button @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 1, nombre: 'Activo', descripcion: 'Estado activo para el CAI'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 1}" class="text-red-500 hover:text-red-700" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 nunito-regular border-t-0">2</td>
                        <td class="py-2 px-4 nunito-regular border-t-0">Inactivo</td>
                        <td class="py-2 px-4 nunito-regular border-t-0">Estado inactivo para el CAI</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular border-t-0">
                            <button @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 2, nombre: 'Inactivo', descripcion: 'Estado inactivo para el CAI'}" class="text-blue-500 hover:text-blue-700" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 2}" class="text-red-500 hover:text-red-700" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular last:border-b-0">
                        <td class="py-2 px-4 nunito-regular first:rounded-bl-lg border-t-0">3</td>
                        <td class="py-2 px-4 nunito-regular border-t-0">Por Vencer</td>
                        <td class="py-2 px-4 nunito-regular border-t-0">CAI próximo a vencer</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular last:rounded-br-lg border-t-0">
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
        </div>
    </div>

    <!-- Modal Nuevo Estado CAI -->
    <x-admin.form-modal class="nunito-bold dark:bg-gray-800" modalName="isEstadoModalOpen" title="Nuevo Estado CAI" submitLabel="Guardar Estado CAI">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Nombre Estado CAI</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" placeholder="Ej: Vencido">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Descripción Estado CAI</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" rows="3" placeholder="Descripción del estado del CAI"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado CAI -->
    <x-admin.edit-modal class="nunito-bold dark:bg-gray-800" modalName="isEditEstadoModalOpen" title="Editar Estado CAI" itemToEdit="estadoToEdit">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">ID Estado</label>
                <input type="text" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 rounded-lg nunito-regular dark:text-white" x-bind:value="estadoToEdit?.id" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Nombre Estado CAI</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" x-bind:value="estadoToEdit?.nombre">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Descripción Estado CAI</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" rows="3" x-text="estadoToEdit?.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Estado CAI -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteEstadoModalOpen" itemToDelete="estadoToDelete" message="¿Está seguro que desea eliminar este estado CAI? Esta acción no se puede deshacer." />
</div>
