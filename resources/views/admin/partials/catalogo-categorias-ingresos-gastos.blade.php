<div x-data="{ 
    isCategoriaModalOpen: false, 
    isEditCategoriaModalOpen: false, 
    isDeleteCategoriaModalOpen: false, 
    categoriaToEdit: { id: '', tipo: '', nombre: '' }, 
    categoriaToDelete: null,
    searchCategoria: '' 
}">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Categorías de Ingresos y Gastos'">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                <input type="text" x-model="searchCategoria" placeholder="Buscar categoría..." class="border rounded px-3 py-2 text-sm w-full sm:w-48 nunito-regular" />
                <select class="border rounded px-3 py-2 text-sm nunito-regular">
                    <option class="nunito-regular" value="">Todos los tipos</option>
                    <option class="nunito-regular" value="Ingreso">Ingreso</option>
                    <option class="nunito-regular" value="Gasto">Gasto</option>
                </select>
            </div>
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isCategoriaModalOpen = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">Agregar Categoría
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 nunito-bold">
                        <th class="py-2 px-4 text-left nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold">Tipo</th>
                        <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4 nunito-regular">1</td>
                        <td class="py-2 px-4"><span class="bg-green-100 text-green-800 px-2 py-1 rounded nunito-regular">Ingreso</span></td>
                        <td class="py-2 px-4 nunito-regular">Salarios</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 1, tipo: 'Ingreso', nombre: 'Salarios'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 1, nombre: 'Salarios'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4 nunito-regular">2</td>
                        <td class="py-2 px-4"><span class="bg-red-100 text-red-800 px-2 py-1 rounded nunito-regular">Gasto</span></td>
                        <td class="py-2 px-4 nunito-regular">Alquiler</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 2, tipo: 'Gasto', nombre: 'Alquiler'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 2, nombre: 'Alquiler'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4 nunito-regular">3</td>
                        <td class="py-2 px-4"><span class="bg-green-100 text-green-800 px-2 py-1 rounded nunito-regular">Ingreso</span></td>
                        <td class="py-2 px-4 nunito-regular">Ventas</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 3, tipo: 'Ingreso', nombre: 'Ventas'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 3, nombre: 'Ventas'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular">
                        <td class="py-2 px-4 nunito-regular">4</td>
                        <td class="py-2 px-4"><span class="bg-red-100 text-red-800 px-2 py-1 rounded nunito-regular">Gasto</span></td>
                        <td class="py-2 px-4 nunito-regular">Gastos Operativos</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 4, tipo: 'Gasto', nombre: 'Gastos Operativos'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 4, nombre: 'Gastos Operativos'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Nueva Categoría -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isCategoriaModalOpen" 
        title="Nueva Categoría" 
        submitLabel="Guardar Categoría">
        <div>
            <label for="tipo_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Categoría</label>
            <select id="tipo_categoria" name="tipo_categoria"
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <option class="nunito-regular">Ingreso</option>
                <option class="nunito-regular">Gasto</option>
            </select>
        </div>
        <div>
            <label for="nombre_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre de la Categoría</label>
            <input type="text" id="nombre_categoria" name="nombre_categoria"
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Categoría -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditCategoriaModalOpen" 
        title="Editar Categoría" 
        itemToEdit="categoriaToEdit">
        <div>
            <label for="edit_nombre_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre de la Categoría</label>
            <input type="text" id="edit_nombre_categoria" name="edit_nombre_categoria" :value="categoriaToEdit.nombre"
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div>
            <label for="edit_tipo_categoria" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Categoría</label>
            <select id="edit_tipo_categoria" name="edit_tipo_categoria" 
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <option class="nunito-regular" :selected="categoriaToEdit.tipo === 'Ingreso'">Ingreso</option>
                <option class="nunito-regular" :selected="categoriaToEdit.tipo === 'Gasto'">Gasto</option>
            </select>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Categoría -->
    <x-admin.confirmation-modal class="nunito-bold"
        modalName="isDeleteCategoriaModalOpen"
        itemToDelete="categoriaToDelete"
        message="¿Estás seguro de que quieres eliminar la categoría?"
    />
</div>
