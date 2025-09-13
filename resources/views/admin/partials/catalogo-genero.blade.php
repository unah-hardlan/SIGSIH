<div x-data="{
        isModalOpenGenero: false,
        isEditModalOpenGenero: false,
        isDeleteModalOpenGenero: false,
        itemToEdit: {id: '', nombre: '', descripcion: ''},
        itemToDelete: {id: ''},
        searchGenero: '',
        generos: [
            {id: 1, nombre: 'Masculino'},
            {id: 2, nombre: 'Femenino'}
        ]
    }">
    <x-admin.tabla-mobile titulo="Gestión de Géneros" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchGenero',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'Id Género'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isModalOpenGenero = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                    Agregar género
                </button>
            </div>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Id Género</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Género</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="genero in generos.filter(g => !searchGenero || g.nombre.toLowerCase().includes(searchGenero.toLowerCase()))" :key="genero.id">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="genero.id"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="genero.nombre"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click.prevent="isEditModalOpenGenero = true; itemToEdit = {...genero}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isDeleteModalOpenGenero = true; itemToDelete = {id: genero.id}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="genero in generos.filter(g => !searchGenero || g.nombre.toLowerCase().includes(searchGenero.toLowerCase()))" :key="genero.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="genero.nombre"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + genero.id"></p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditModalOpenGenero = true; itemToEdit = {...genero}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteModalOpenGenero = true; itemToDelete = {id: genero.id}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

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