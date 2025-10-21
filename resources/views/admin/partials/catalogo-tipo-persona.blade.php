<div x-data="{ isModalOpenTipoPersona: false, isEditModalOpenTipoPersona: false, isDeleteModalOpenTipoPersona: false, itemToEdit: {}, itemToDelete: {}, searchTipoPersona: '' }">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left">ID Tipo Persona</th>
                    <th class="py-2 px-4 text-left">Nombre</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="persona in [{id: 1, nombre: 'Cliente'}, {id: 2, nombre: 'Proveedor'}]" :key="persona.id">
                    <tr>
                        <td x-text="persona.id"></td>
                        <td x-text="persona.nombre"></td>
                        <td>
                            <button @click="isEditModalOpenTipoPersona = true; itemToEdit = {...persona}">Editar</button>
                            <button @click="isDeleteModalOpenTipoPersona = true; itemToDelete = {...persona}">Eliminar</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>