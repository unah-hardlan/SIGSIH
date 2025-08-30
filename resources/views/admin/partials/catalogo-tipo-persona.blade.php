<div x-data="{ 
    isModalOpenTipoPersona: false, 
    isEditModalOpenTipoPersona: false, 
    isDeleteModalOpenTipoPersona: false, 
    itemToEdit: {id: '', nombre: '', descripcion: ''}, 
    itemToDelete: {id: ''}, 
    searchTipoPersona: '' 
}">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Tipos de Persona'">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                <input type="text" x-model="searchTipoPersona" placeholder="Buscar tipo de persona..."
                    class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
            </div>
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isModalOpenTipoPersona = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">Agregar
                    tipo
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 nunito-bold">
                        <th class="py-2 px-4 text-left">ID Tipo Persona</th>
                        <th class="py-2 px-4 text-left">Nombre Tipo Persona</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">1</td>
                        <td class="py-2 px-4">Técnico</td>
                        <td class="py-2 px-4">Persona que trabaja en la empresa</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#"
                                @click="isEditModalOpenTipoPersona = true; itemToEdit = {id: 1, nombre: 'Técnico', descripcion: 'Persona que trabaja en la empresa'}"
                                class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <a href="#"
                                @click="isDeleteModalOpenTipoPersona = true; itemToDelete = {id: 1, nombre: 'Técnico'}"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">2</td>
                        <td class="py-2 px-4">Cliente</td>
                        <td class="py-2 px-4">Persona que adquiere servicios</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#"
                                @click="isEditModalOpenTipoPersona = true; itemToEdit = {id: 2, nombre: 'Cliente', descripcion: 'Persona que adquiere servicios'}"
                                class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <a href="#"
                                @click="isDeleteModalOpenTipoPersona = true; itemToDelete = {id: 2, nombre: 'Cliente'}"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modales Tipo de Persona -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpenTipoPersona" title="Agregar Tipo de Persona" submitLabel="Guardar"
        maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Nombre Tipo Persona</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Empleado" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular"
                placeholder="Ej: Persona que trabaja en la empresa" />
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpenTipoPersona" title="Editar Tipo de Persona" itemToEdit="itemToEdit"
        maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Nombre Tipo Persona</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.nombre" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.descripcion" />
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpenTipoPersona" itemToDelete="itemToDelete"
        message="¿Estás seguro de que deseas eliminar este tipo de persona?" />
</div>