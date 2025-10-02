<div x-data="{
        isServicioModalOpen: false,
        isEditModalOpen: false,
        servicioToEdit: null,
        isDeleteModalOpen: false,
        servicioToDelete: null,
        servicios: [
            {
                id_servicio: 'SR-001',
                tipo_servicio: 'Mantenimiento',
                descripcion: 'Mantenimiento preventivo de equipos',
                fecha: '2025-07-28'
            }
        ],
        filtroServicio: '',
        filtroTipo: ''
    }">
    <x-admin.tabla-mobile titulo="Servicio Realizado" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroServicio',
                'filtrosSelect' => [
                    'filtroTipo' => [
                        'label' => 'Tipo de Servicio',
                        'options' => ['Mantenimiento', 'Instalación', 'Reparación']
                    ]
                ],
                'ordenarOptions' => [
                    'descripcion' => 'Descripción',
                    'tipo_servicio' => 'Tipo de Servicio',
                    'id_servicio' => 'ID Servicio'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button @click="isServicioModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo servicio
            </button>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr class="border-0">
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 first:rounded-tl-lg border-0">ID Servicio</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Tipo de Servicio</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Descripción</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 border-0">Fecha</th>
                        <th class="py-2 px-4 text-left nunito-bold dark:text-gray-300 last:rounded-tr-lg border-0">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="servicio in servicios
                        .filter(s => 
                            (!filtroServicio || s.descripcion.toLowerCase().includes(filtroServicio.toLowerCase()))
                            && (!filtroTipo || s.tipo_servicio === filtroTipo)
                        )" :key="servicio.id_servicio">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular last:border-b-0">
                            <td class="py-2 px-4 first:rounded-bl-lg border-t-0" x-text="servicio.id_servicio"></td>
                            <td class="py-2 px-4 border-t-0" x-text="servicio.tipo_servicio"></td>
                            <td class="py-2 px-4 border-t-0" x-text="servicio.descripcion"></td>
                            <td class="py-2 px-4 border-t-0" x-text="servicio.fecha"></td>
                            <td class="py-2 px-4 flex gap-2 last:rounded-br-lg border-t-0">
                                <a href="#" @click.prevent="isEditModalOpen = true; servicioToEdit = servicio" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="isDeleteModalOpen = true; servicioToDelete = servicio" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="servicio in servicios
                    .filter(s => 
                        (!filtroServicio || s.descripcion.toLowerCase().includes(filtroServicio.toLowerCase()))
                        && (!filtroTipo || s.tipo_servicio === filtroTipo)
                    )" :key="servicio.id_servicio">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="servicio.tipo_servicio"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + servicio.id_servicio"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicio.descripcion"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Fecha:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicio.fecha"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditModalOpen = true; servicioToEdit = servicio"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteModalOpen = true; servicioToDelete = servicio"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modal Nuevo Servicio -->
    <x-admin.form-modal class="nunito-bold" modalName="isServicioModalOpen" title="Nuevo Servicio Realizado" submitLabel="Guardar Servicio"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="tipo_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Servicio</label>
                <select id="tipo_servicio" name="tipo_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Mantenimiento</option>
                    <option>Instalación</option>
                    <option>Reparación</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="descripcion_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_servicio" name="descripcion_servicio" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="fecha_servicio" name="fecha_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Servicio -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Servicio Realizado" itemToEdit="servicioToEdit" maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_tipo_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Servicio</label>
                <select id="edit_tipo_servicio" name="edit_tipo_servicio" :value="servicioToEdit?.tipo_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option>Mantenimiento</option>
                    <option>Instalación</option>
                    <option>Reparación</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="edit_descripcion_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion_servicio" name="edit_descripcion_servicio" rows="2" :value="servicioToEdit?.descripcion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
            <div>
                <label for="edit_fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="edit_fecha_servicio" name="edit_fecha_servicio" :value="servicioToEdit?.fecha"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación -->
    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpen" itemToDelete="servicioToDelete"
        message="¿Estás seguro de que quieres eliminar este servicio?" />
</div>