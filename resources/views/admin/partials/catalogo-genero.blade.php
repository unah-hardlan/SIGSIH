<div x-data="{ 
    isModalOpenGenero: false, 
    isEditModalOpenGenero: false, 
    isDeleteModalOpenGenero: false, 
    itemToEdit: {id: '', nombre: '', descripcion: ''}, 
    itemToDelete: {id: ''}, 
    searchGenero: '' 
}">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Géneros'">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                <input type="text" x-model="searchGenero" placeholder="Buscar género..." class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
            </div>
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isModalOpenGenero = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center justify-center">Agregar
                    género
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 nunito-bold">
                        <th class="py-2 px-4 text-left">Id Género</th>
                        <th class="py-2 px-4 text-left">Género</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">1</td>
                        <td class="py-2 px-4">Masculino</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditModalOpenGenero = true; itemToEdit = {genero: 'Masculino'}"
                                class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteModalOpenGenero = true; itemToDelete = {genero: 'Masculino'}"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">2</td>
                        <td class="py-2 px-4">Femenino</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditModalOpenGenero = true; itemToEdit = {genero: 'Femenino'}"
                                class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteModalOpenGenero = true; itemToDelete = {genero: 'Femenino'}"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>
    
    <!-- Modales género -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpenGenero" title="Agregar Género" submitLabel="Guardar" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Género</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Masculino" />
        </div>
    </x-admin.form-modal>
    
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpenGenero" title="Editar Género" itemToEdit="itemToEdit" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Género</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.genero" />
        </div>
    </x-admin.edit-modal>
    
    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpenGenero" itemToDelete="itemToDelete"
        message="¿Estás seguro de que deseas eliminar este género?" />
</div>