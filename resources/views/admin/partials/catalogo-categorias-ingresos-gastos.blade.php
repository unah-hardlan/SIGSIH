<div x-data="{ 
    isCategoriaModalOpen: false, 
    isEditCategoriaModalOpen: false, 
    isDeleteCategoriaModalOpen: false, 
    categoriaToEdit: { id: '', tipo: '', nombre: '' }, 
    categoriaToDelete: null,
    searchCategoria: '' 
}">
    <x-admin.tabla-crud :titulo="'Gestión de Categorías de Ingresos y Gastos'">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                <input type="text" x-model="searchCategoria" placeholder="Buscar categoría..." class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
                <select class="border rounded px-3 py-2 text-sm">
                    <option value="">Todos los tipos</option>
                    <option value="Ingreso">Ingreso</option>
                    <option value="Gasto">Gasto</option>
                </select>
            </div>
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isCategoriaModalOpen = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center justify-center">Agregar Categoría
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 nunito-bold">
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Tipo</th>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">1</td>
                        <td class="py-2 px-4"><span class="bg-green-100 text-green-800 px-2 py-1 rounded">Ingreso</span></td>
                        <td class="py-2 px-4">Salarios</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 1, tipo: 'Ingreso', nombre: 'Salarios'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 1, nombre: 'Salarios'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">2</td>
                        <td class="py-2 px-4"><span class="bg-red-100 text-red-800 px-2 py-1 rounded">Gasto</span></td>
                        <td class="py-2 px-4">Alquiler</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 2, tipo: 'Gasto', nombre: 'Alquiler'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 2, nombre: 'Alquiler'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">3</td>
                        <td class="py-2 px-4"><span class="bg-green-100 text-green-800 px-2 py-1 rounded">Ingreso</span></td>
                        <td class="py-2 px-4">Ventas</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 3, tipo: 'Ingreso', nombre: 'Ventas'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 3, nombre: 'Ventas'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4">4</td>
                        <td class="py-2 px-4"><span class="bg-red-100 text-red-800 px-2 py-1 rounded">Gasto</span></td>
                        <td class="py-2 px-4">Gastos Operativos</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 4, tipo: 'Gasto', nombre: 'Gastos Operativos'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 4, nombre: 'Gastos Operativos'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Nueva Categoría -->
    <x-admin.form-modal 
        modalName="isCategoriaModalOpen" 
        title="Nueva Categoría" 
        submitLabel="Guardar Categoría">
        <div>
            <label for="tipo_categoria" class="block text-sm font-medium text-gray-700">Tipo de Categoría</label>
            <select id="tipo_categoria" name="tipo_categoria"
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <option>Ingreso</option>
                <option>Gasto</option>
            </select>
        </div>
        <div>
            <label for="nombre_categoria" class="block text-sm font-medium text-gray-700">Nombre de la Categoría</label>
            <input type="text" id="nombre_categoria" name="nombre_categoria"
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Categoría -->
    <x-admin.edit-modal 
        modalName="isEditCategoriaModalOpen" 
        title="Editar Categoría" 
        itemToEdit="categoriaToEdit">
        <div>
            <label for="edit_nombre_categoria" class="block text-sm font-medium text-gray-700">Nombre de la Categoría</label>
            <input type="text" id="edit_nombre_categoria" name="edit_nombre_categoria" :value="categoriaToEdit.nombre"
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
            <label for="edit_tipo_categoria" class="block text-sm font-medium text-gray-700">Tipo de Categoría</label>
            <select id="edit_tipo_categoria" name="edit_tipo_categoria" 
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <option :selected="categoriaToEdit.tipo === 'Ingreso'">Ingreso</option>
                <option :selected="categoriaToEdit.tipo === 'Gasto'">Gasto</option>
            </select>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Categoría -->
    <x-admin.confirmation-modal
        modalName="isDeleteCategoriaModalOpen"
        itemToDelete="categoriaToDelete"
        message="¿Estás seguro de que quieres eliminar la categoría?"
    />
</div>
