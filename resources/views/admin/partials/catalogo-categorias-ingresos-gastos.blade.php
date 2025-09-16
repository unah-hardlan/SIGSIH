<div x-data="{ 
    isCategoriaModalOpen: false, 
    isEditCategoriaModalOpen: false, 
    isDeleteCategoriaModalOpen: false, 
    categoriaToEdit: { id: '', tipo: '', nombre: '' }, 
    categoriaToDelete: null,
    searchCategoria: '',
    tipoCategoria: '',
    ordenarPor: 'nombre'
}" class="dark:bg-gray-900 min-h-screen">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Categorías de Ingresos y Gastos'">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchCategoria',
                'filtrosSelect' => [
                    'tipoCategoria' => [
                        'label' => 'Tipos',
                        'options' => ['Ingreso', 'Gasto']
                    ]
                ],
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'tipo' => 'Tipo',
                    'id' => 'ID'
                ]
            ])
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
                    <tr class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <th class="py-2 px-4 text-left nunito-bold">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold">Tipo</th>
                        <th class="py-2 px-4 text-left nunito-bold">Nombre</th>
                        <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 nunito-regular dark:text-white">1</td>
                        <td class="py-2 px-4"><span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-400 px-2 py-1 rounded nunito-regular">Ingreso</span></td>
                        <td class="py-2 px-4 nunito-regular dark:text-white">Salarios</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 1, tipo: 'Ingreso', nombre: 'Salarios'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 1, nombre: 'Salarios'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 nunito-regular dark:text-white">2</td>
                        <td class="py-2 px-4"><span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded nunito-regular">Gasto</span></td>
                        <td class="py-2 px-4 nunito-regular dark:text-white">Alquiler</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 2, tipo: 'Gasto', nombre: 'Alquiler'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 2, nombre: 'Alquiler'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 nunito-regular dark:text-white">3</td>
                        <td class="py-2 px-4"><span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-400 px-2 py-1 rounded nunito-regular">Ingreso</span></td>
                        <td class="py-2 px-4 nunito-regular dark:text-white">Ventas</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 3, tipo: 'Ingreso', nombre: 'Ventas'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 3, nombre: 'Ventas'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 nunito-regular dark:text-white">4</td>
                        <td class="py-2 px-4"><span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded nunito-regular">Gasto</span></td>
                        <td class="py-2 px-4 nunito-regular dark:text-white">Gastos Operativos</td>
                        <td class="py-2 px-4 flex gap-2 nunito-regular dark:text-white">
                            <a href="#" @click="isEditCategoriaModalOpen = true; categoriaToEdit = {id: 4, tipo: 'Gasto', nombre: 'Gastos Operativos'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteCategoriaModalOpen = true; categoriaToDelete = {id: 4, nombre: 'Gastos Operativos'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

    <!-- Modal Nueva Categoría -->
    <x-admin.form-modal class="nunito-bold dark:bg-gray-800"
        modalName="isCategoriaModalOpen" 
        title="Nueva Categoría" 
        submitLabel="Guardar Categoría">
        <div>
            <label for="tipo_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 nunito-bold">Tipo de Categoría</label>
            <select id="tipo_categoria" name="tipo_categoria"
                class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-700 shadow-sm border focus:border-gray-500 dark:focus:border-gray-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2 dark:bg-gray-900 dark:text-white">
                <option class="nunito-regular">Ingreso</option>
                <option class="nunito-regular">Gasto</option>
            </select>
        </div>
        <div>
            <label for="nombre_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 nunito-bold">Nombre de la Categoría</label>
            <input type="text" id="nombre_categoria" name="nombre_categoria"
                class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-700 shadow-sm border focus:border-gray-500 dark:focus:border-gray-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2 dark:bg-gray-900 dark:text-white">
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Categoría -->
    <x-admin.edit-modal class="nunito-bold dark:bg-gray-800"
        modalName="isEditCategoriaModalOpen" 
        title="Editar Categoría" 
        itemToEdit="categoriaToEdit">
        <div>
            <label for="edit_nombre_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 nunito-bold">Nombre de la Categoría</label>
            <input type="text" id="edit_nombre_categoria" name="edit_nombre_categoria" :value="categoriaToEdit.nombre"
                class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-700 shadow-sm border focus:border-gray-500 dark:focus:border-gray-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2 dark:bg-gray-900 dark:text-white">
        </div>
        <div>
            <label for="edit_tipo_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 nunito-bold">Tipo de Categoría</label>
            <select id="edit_tipo_categoria" name="edit_tipo_categoria" 
                class="mt-1 block w-full rounded-md border-gray-500 dark:border-gray-700 shadow-sm border focus:border-gray-500 dark:focus:border-gray-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2 dark:bg-gray-900 dark:text-white">
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
