<div x-data="{ 
    isModalOpenPerfil: false, 
    isEditModalOpenPerfil: false, 
    isDeleteModalOpenPerfil: false, 
    itemToEdit: {id: '', perfil: '', descripcion: ''}, 
    itemToDelete: {id: ''}, 
    searchPerfil: '' 
}">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Gestión de Perfiles'">
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
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">Agregar
                    perfil
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <th class="py-2 px-4 text-left">Id Perfil</th>
                        <th class="py-2 px-4 text-left">Nombre de Perfil</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 dark:text-white">1</td>
                        <td class="py-2 px-4 dark:text-white">Administrador</td>
                        <td class="py-2 px-4 dark:text-white">Acceso total al sistema</td>
                        <td class="py-2 px-4 flex gap-2 dark:text-white">
                            <a href="#"
                                @click="isEditModalOpenPerfil = true; itemToEdit = {id: 1, perfil: 'Administrador', descripcion: 'Acceso total al sistema'}"
                                class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <a href="#"
                                @click="isDeleteModalOpenPerfil = true; itemToDelete = {id: 1, perfil: 'Administrador'}"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 dark:text-white">2</td>
                        <td class="py-2 px-4 dark:text-white">Técnico</td>
                        <td class="py-2 px-4 dark:text-white">Acceso limitado al sistema</td>
                        <td class="py-2 px-4 flex gap-2 dark:text-white">
                            <a href="#"
                                @click="isEditModalOpenPerfil = true; itemToEdit = {id: 2, perfil: 'Técnico', descripcion: 'Acceso limitado al sistema'}"
                                class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                            <a href="#"
                                @click="isDeleteModalOpenPerfil = true; itemToDelete = {id: 2, perfil: 'Técnico'}"
                                class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

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