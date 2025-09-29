<div x-data="{ 
    isModalOpenTipoPersona: false, 
    isEditModalOpenTipoPersona: false, 
    isDeleteModalOpenTipoPersona: false, 
    itemToEdit: {id: '', nombre: '', descripcion: ''}, 
    itemToDelete: {id: ''}, 
    searchTipoPersona: '' 
}">
    <x-admin.tabla-mobile titulo="Gestión de Tipos de Persona" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchTipoPersona',
                    'ordenarOptions' => [
                        'nombre' => 'Nombre Tipo Persona',
                        'id' => 'ID Tipo Persona'
                    ]
                ])
            </div>
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isModalOpenTipoPersona = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">
                    Agregar tipo
                </button>
            </div>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <th class="py-2 px-4 text-left dark:text-white">ID Tipo Persona</th>
                    <th class="py-2 px-4 text-left dark:text-white">Nombre Tipo Persona</th>
                    <th class="py-2 px-4 text-left dark:text-white">Descripción</th>
                    <th class="py-2 px-4 text-left dark:text-white">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="tipo in [
                    {id: 1, nombre: 'Técnico', descripcion: 'Persona que trabaja en la empresa'},
                    {id: 2, nombre: 'Cliente', descripcion: 'Persona que adquiere servicios'}
                ]" :key="tipo.id">
                    <tr class="border-b nunito-regular dark:border-gray-700">
                        <td class="py-2 px-4 dark:text-white" x-text="tipo.id"></td>
                        <td class="py-2 px-4 dark:text-white" x-text="tipo.nombre"></td>
                        <td class="py-2 px-4 dark:text-white" x-text="tipo.descripcion"></td>
                        <td class="py-2 px-4 flex gap-2 dark:text-white">
                            <a href="#" @click="isEditModalOpenTipoPersona = true; itemToEdit = {...tipo}"
                                class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteModalOpenTipoPersona = true; itemToDelete = {...tipo}"
                                class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="tipo in [
                    {id: 1, nombre: 'Técnico', descripcion: 'Persona que trabaja en la empresa'},
                    {id: 2, nombre: 'Cliente', descripcion: 'Persona que adquiere servicios'}
                ]" :key="tipo.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipo.nombre"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + tipo.id"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipo.descripcion"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click="isEditModalOpenTipoPersona = true; itemToEdit = {...tipo}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click="isDeleteModalOpenTipoPersona = true; itemToDelete = {...tipo}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

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