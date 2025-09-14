<div x-data="{
        isServicioModalOpen: false,
        isEditServicioModalOpen: false,
        isDeleteServicioModalOpen: false,
        servicioToEdit: null,
        servicioToDelete: null,
        filtroServicio: '',
        servicios: [
            {id: 'SVC-01', nombre: 'Consultoría', tarifa: 1500},
            // Puedes agregar más servicios aquí
        ]
    }" class="overflow-x-auto">

    <!-- Botones y tabla -->
    <x-admin.tabla-mobile titulo="Servicios" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroServicio',
                'ordenarOptions' => [
                    'nombre' => 'Nombre Servicio',
                    'id' => 'ID'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isServicioModalOpen = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                    Nuevo Servicio
                </button>
            </div>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre Servicio</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Tarifa</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="servicio in servicios.filter(s => !filtroServicio || s.nombre.toLowerCase().includes(filtroServicio.toLowerCase()))" :key="servicio.id">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicio.id"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicio.nombre"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicio.tarifa"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click.prevent="isEditServicioModalOpen = true; servicioToEdit = {...servicio}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isDeleteServicioModalOpen = true; servicioToDelete = {id: servicio.id}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="servicio in servicios.filter(s => !filtroServicio || s.nombre.toLowerCase().includes(filtroServicio.toLowerCase()))" :key="servicio.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="servicio.nombre"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + servicio.id"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Tarifa:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="servicio.tarifa"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditServicioModalOpen = true; servicioToEdit = {...servicio}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteServicioModalOpen = true; servicioToDelete = {id: servicio.id}"
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
    <x-admin.form-modal class="nunito-bold" modalName="isServicioModalOpen" title="Nuevo Servicio" submitLabel="Guardar Servicio"
        maxWidth="max-w-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_servicio_nuevo" class="block text-sm font-medium text-gray-700 nunito-bold">ID</label>
                <input type="text" id="id_servicio_nuevo" name="id_servicio_nuevo"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre Servicio</label>
                <input type="text" id="nombre_servicio" name="nombre_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="tarifa" class="block text-sm font-medium text-gray-700 nunito-bold">Tarifa</label>
                <input type="number" id="tarifa" name="tarifa"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Servicio -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditServicioModalOpen" title="Editar Servicio" itemToEdit="servicioToEdit"
        maxWidth="max-w-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_id_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">ID</label>
                <input type="text" id="edit_id_servicio" name="edit_id_servicio" :value="servicioToEdit.id"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_nombre_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                    Servicio</label>
                <input type="text" id="edit_nombre_servicio" name="edit_nombre_servicio" :value="servicioToEdit.nombre"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_tarifa" class="block text-sm font-medium text-gray-700 nunito-bold">Tarifa</label>
                <input type="number" id="edit_tarifa" name="edit_tarifa" :value="servicioToEdit.tarifa"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Servicio -->
    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteServicioModalOpen" itemToDelete="servicioToDelete"
        message="¿Estás seguro de que quieres eliminar el servicio?" />
