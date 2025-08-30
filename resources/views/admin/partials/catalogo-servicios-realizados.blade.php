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
    }" class="overflow-x-auto">
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <div
            class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-2xl text-gray-800 nunito-bold">Servicio Realizado</h2>
            <div class="flex flex-col sm:flex-row gap-2 flex-1 md:ml-6 nunito-bold">
                <input type="text" x-model="filtroServicio" placeholder="Buscar servicio..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
                <select x-model="filtroTipo" class="border rounded px-1 py-2 text-sm w-full sm:w-40">
                    <option value="">Todos los tipos</option>
                    <option>Mantenimiento</option>
                    <option>Instalación</option>
                    <option>Reparación</option>
                </select>
            </div>
            <button @click="isServicioModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular    transition whitespace-nowrap text-sm">
                Nuevo servicio
            </button>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID Servicio</th>
                    <th class="py-2 px-4 text-left">Tipo de Servicio</th>
                    <th class="py-2 px-4 text-left">Descripción</th>
                    <th class="py-2 px-4 text-left">Fecha</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="servicio in servicios
                    .filter(s => 
                        (!filtroServicio || s.descripcion.toLowerCase().includes(filtroServicio.toLowerCase()))
                        && (!filtroTipo || s.tipo_servicio === filtroTipo)
                    )" :key="servicio.id_servicio">
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4" x-text="servicio.id_servicio"></td>
                        <td class="py-2 px-4" x-text="servicio.tipo_servicio"></td>
                        <td class="py-2 px-4" x-text="servicio.descripcion"></td>
                        <td class="py-2 px-4" x-text="servicio.fecha"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click.prevent="isEditModalOpen = true; servicioToEdit = servicio" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isDeleteModalOpen = true; servicioToDelete = servicio" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

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