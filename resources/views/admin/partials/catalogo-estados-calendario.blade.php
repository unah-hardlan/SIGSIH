<div x-data="{ isAddEstadoModalOpen: false, isEditEstadoModalOpen: false, isDeleteEstadoModalOpen: false, selectedEstado: null }" class="overflow-x-auto w-full">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 w-full">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 pb-4 mb-4 border-b dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
            <h2 class="text-2xl text-gray-800 dark:text-white nunito-bold">Estado Calendario</h2>
            <button @click="isAddEstadoModalOpen = true" class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo Estado</button>
        </div>
        <div class="mb-4">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchEstadoCalendario',
                'filtrosSelect' => [
                    'estadoCalendario' => [
                        'label' => 'Estado',
                        'options' => ['Programado', 'Realizado', 'Cancelado']
                    ]
                ],
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Estado'
                ]
            ])
        </div>
        <table class="min-w-full text-sm w-full">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID Estado</th>
                    <th class="py-2 px-4 text-left">Nombre</th>
                    <th class="py-2 px-4 text-left">Descripción</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 dark:text-white">E-001</td>
                    <td class="py-2 px-4 dark:text-white">Programado</td>
                    <td class="py-2 px-4 dark:text-white">Evento programado en el calendario</td>
                    <td class="py-2 px-4 flex gap-2 dark:text-white">
                        <button @click="selectedEstado = {id: 'E-001', nombre: 'Programado', descripcion: 'Evento programado en el calendario'}; isEditEstadoModalOpen = true" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="selectedEstado = {id: 'E-001', nombre: 'Programado'}; isDeleteEstadoModalOpen = true" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="border-b dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 dark:text-white">E-002</td>
                    <td class="py-2 px-4 dark:text-white">Realizado</td>
                    <td class="py-2 px-4 dark:text-white">Evento completado exitosamente</td>
                    <td class="py-2 px-4 flex gap-2 dark:text-white">
                        <button @click="selectedEstado = {id: 'E-002', nombre: 'Realizado', descripcion: 'Evento completado exitosamente'}; isEditEstadoModalOpen = true" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="selectedEstado = {id: 'E-002', nombre: 'Realizado'}; isDeleteEstadoModalOpen = true" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr class="border-b dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 dark:text-white">E-003</td>
                    <td class="py-2 px-4 dark:text-white">Cancelado</td>
                    <td class="py-2 px-4 dark:text-white">Evento cancelado o suspendido</td>
                    <td class="py-2 px-4 flex gap-2 dark:text-white">
                        <button @click="selectedEstado = {id: 'E-003', nombre: 'Cancelado', descripcion: 'Evento cancelado o suspendido'}; isEditEstadoModalOpen = true" class="text-blue-500 hover:text-blue-700" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="selectedEstado = {id: 'E-003', nombre: 'Cancelado'}; isDeleteEstadoModalOpen = true" class="text-red-500 hover:text-red-700" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Agregar Estado -->
    <x-admin.form-modal class="nunito-bold dark:bg-gray-800" modalName="isAddEstadoModalOpen" title="Agregar Estado de Calendario" submitLabel="Guardar Estado">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Nombre del Estado</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" placeholder="Ej: En Proceso">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" rows="3" placeholder="Descripción del estado del calendario"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal class="nunito-bold dark:bg-gray-800" modalName="isEditEstadoModalOpen" title="Editar Estado de Calendario" itemToEdit="selectedEstado">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">ID Estado</label>
                <input type="text" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 rounded-lg nunito-regular dark:text-white" x-bind:value="selectedEstado?.id" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Nombre del Estado</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" x-bind:value="selectedEstado?.nombre">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent nunito-regular dark:bg-gray-900 dark:text-white" rows="3" x-text="selectedEstado?.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Eliminar Estado -->
    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteEstadoModalOpen" itemToDelete="selectedEstado" message="¿Está seguro que desea eliminar este estado de calendario? Esta acción no se puede deshacer." />
</div>
