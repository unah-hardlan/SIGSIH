<div x-data="{
        isServicioModalOpen: false,
        isEditServicioModalOpen: false,
        isDeleteServicioModalOpen: false,
        servicioToEdit: {id: '', nombre: '', tarifa: ''},
        servicioToDelete: {id: ''},
        filtroServicio: '',
        servicios: [
            {id: 'SVC-01', nombre: 'Consultoría', tarifa: 1500},
            // Puedes agregar más servicios aquí
        ]
    }" class="overflow-x-auto">
    <x-admin.tabla-crud class="nunito-bold">
        <x-slot name="titulo">
            <h2 class="text-2xl text-gray-800 nunito-bold">Servicios</h2>
        </x-slot>
        <x-slot name="filtros">
            <input type="text" x-model="filtroServicio" placeholder="Buscar servicio..."
                class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isServicioModalOpen = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center justify-center">
                    Nuevo Servicio
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Nombre Servicio</th>
                        <th class="py-2 px-4 text-left">Tarifa</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template
                        x-for="servicio in servicios.filter(s => !filtroServicio || s.nombre.toLowerCase().includes(filtroServicio.toLowerCase()))"
                        :key="servicio.id">
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4" x-text="servicio.id"></td>
                            <td class="py-2 px-4" x-text="servicio.nombre"></td>
                            <td class="py-2 px-4" x-text="servicio.tarifa"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#"
                                    @click.prevent="isEditServicioModalOpen = true; servicioToEdit = {...servicio}"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#"
                                    @click.prevent="isDeleteServicioModalOpen = true; servicioToDelete = {id: servicio.id}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

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
</div>