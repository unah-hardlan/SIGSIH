<div x-data="{
        isAddEstadoModalOpen: false,
        isEditEstadoModalOpen: false,
        isDeleteEstadoModalOpen: false,
        selectedEstado: null,
        searchEstadoCalendario: '',
        estados: [
            {id: 'E-001', nombre: 'Programado', descripcion: 'Evento programado en el calendario'},
            {id: 'E-002', nombre: 'Realizado', descripcion: 'Evento completado exitosamente'},
            {id: 'E-003', nombre: 'Cancelado', descripcion: 'Evento cancelado o suspendido'}
        ]
    }">
    <x-admin.tabla-mobile titulo="Estado Calendario" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
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
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isAddEstadoModalOpen = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                    Nuevo Estado
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
                <template x-for="estado in estados.filter(e => !searchEstadoCalendario || e.nombre.toLowerCase().includes(searchEstadoCalendario.toLowerCase()))" :key="estado.id">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.id"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.nombre"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.descripcion"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <button @click.prevent="selectedEstado = {...estado}; isEditEstadoModalOpen = true"
                                class="text-blue-500 hover:text-blue-700" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click.prevent="selectedEstado = {id: estado.id}; isDeleteEstadoModalOpen = true"
                                class="text-red-500 hover:text-red-700" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="estado in estados.filter(e => !searchEstadoCalendario || e.nombre.toLowerCase().includes(searchEstadoCalendario.toLowerCase()))" :key="estado.id">
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
                            <button @click.prevent="selectedEstado = {...estado}; isEditEstadoModalOpen = true"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="selectedEstado = {id: estado.id}; isDeleteEstadoModalOpen = true"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

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
