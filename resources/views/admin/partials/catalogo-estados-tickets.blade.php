<div x-data="{ 
    isModalOpenEstadoTicket: false, 
    isEditModalOpenEstadoTicket: false, 
    isDeleteModalOpenEstadoTicket: false, 
    itemToEdit: {id: '', nombre: '', descripcion: ''}, 
    itemToDelete: {id: ''}, 
    searchEstadoTicket: '' 
}">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Estados de Tickets'">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                <input type="text" x-model="searchEstadoTicket" placeholder="Buscar estado..." class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
                <select class="border rounded px-3 py-2 text-sm nunito-regular">
                    <option value="">Prioridad</option>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isModalOpenEstadoTicket = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">Agregar Estado
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm w-full">
                <thead class="bg-gray-100 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">ID Estado</th>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">E-001</td>
                        <td class="py-2 px-4">Pendiente</td>
                        <td class="py-2 px-4">Ticket en espera de atención</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditModalOpenEstadoTicket = true; itemToEdit = {id: 'E-001', nombre: 'Pendiente', descripcion: 'Ticket en espera de atención'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteModalOpenEstadoTicket = true; itemToDelete = {id: 'E-001', nombre: 'Pendiente'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">E-002</td>
                        <td class="py-2 px-4">En proceso</td>
                        <td class="py-2 px-4">Ticket siendo atendido activamente</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditModalOpenEstadoTicket = true; itemToEdit = {id: 'E-002', nombre: 'En proceso', descripcion: 'Ticket siendo atendido activamente'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteModalOpenEstadoTicket = true; itemToDelete = {id: 'E-002', nombre: 'En proceso'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">E-003</td>
                        <td class="py-2 px-4">Finalizado</td>
                        <td class="py-2 px-4">Ticket resuelto completamente</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditModalOpenEstadoTicket = true; itemToEdit = {id: 'E-003', nombre: 'Finalizado', descripcion: 'Ticket resuelto completamente'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteModalOpenEstadoTicket = true; itemToDelete = {id: 'E-003', nombre: 'Finalizado'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Nuevo Estado -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isModalOpenEstadoTicket" 
        title="Nuevo Estado de Ticket" 
        submitLabel="Guardar Estado">
        <div class="space-y-4">
            <div>
                <label for="id_estado" class="block text-sm font-medium text-gray-700 nunito-bold">ID Estado</label>
                <input type="text" id="id_estado" name="id_estado" placeholder="E-004" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="nombre_estado" name="nombre_estado" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_estado" name="descripcion_estado" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditModalOpenEstadoTicket" 
        title="Editar Estado de Ticket" 
        itemToEdit="itemToEdit">
        <div class="space-y-4">
            <div>
                <label for="edit_id_estado" class="block text-sm font-medium text-gray-700 nunito-bold">ID Estado</label>
                <input type="text" id="edit_id_estado" name="edit_id_estado" :value="itemToEdit.id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="edit_nombre_estado" name="edit_nombre_estado" :value="itemToEdit.nombre" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="edit_descripcion_estado" name="edit_descripcion_estado" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" x-text="itemToEdit.descripcion"></textarea>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Estado -->
    <x-admin.confirmation-modal class="nunito-regular"
        modalName="isDeleteModalOpenEstadoTicket"
        itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar el estado"
    />
</div>
