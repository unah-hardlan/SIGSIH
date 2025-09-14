<div x-data="{
        isModalOpenPerfil: false,
        isEditModalOpenPerfil: false,
        isDeleteModalOpenPerfil: false,
        itemToEdit: {id: '', perfil: '', descripcion: ''},
        itemToDelete: {id: ''},
        searchPerfil: '',
        perfiles: [
            {id: 1, perfil: 'Administrador', descripcion: 'Acceso total al sistema'},
            {id: 2, perfil: 'Técnico', descripcion: 'Acceso limitado al sistema'}
        ]
    }">
    <x-admin.tabla-mobile titulo="Gestión de Perfiles" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchPerfil',
                'ordenarOptions' => [
                    'perfil' => 'Nombre de Perfil',
                    'id' => 'Id Perfil'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isModalOpenPerfil = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                    Agregar perfil
                </button>
            </div>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Id Perfil</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre de Perfil</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="perfil in perfiles.filter(p => !searchPerfil || p.perfil.toLowerCase().includes(searchPerfil.toLowerCase()))" :key="perfil.id">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="perfil.id"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="perfil.perfil"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="perfil.descripcion"></td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="#" @click.prevent="isEditModalOpenPerfil = true; itemToEdit = {...perfil}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click.prevent="isDeleteModalOpenPerfil = true; itemToDelete = {id: perfil.id}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="perfil in perfiles.filter(p => !searchPerfil || p.perfil.toLowerCase().includes(searchPerfil.toLowerCase()))" :key="perfil.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="perfil.perfil"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + perfil.id"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="perfil.descripcion"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditModalOpenPerfil = true; itemToEdit = {...perfil}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteModalOpenPerfil = true; itemToDelete = {id: perfil.id}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modales Perfil -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpenPerfil" title="Agregar Perfil" submitLabel="Guardar" maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Nombre del Perfil</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Administrador" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Descripción del Perfil</label>
            <textarea class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Ej: Acceso total al sistema"></textarea>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpenPerfil" title="Editar Perfil" itemToEdit="itemToEdit"
        maxWidth="max-w-md">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Perfil</label>
            <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.perfil" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
            <textarea class="w-full border rounded px-3 py-2 nunito-regular" :value="itemToEdit?.descripcion"></textarea>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteModalOpenPerfil" itemToDelete="itemToDelete"
        message="¿Estás seguro de que deseas eliminar este perfil?" />
</div>