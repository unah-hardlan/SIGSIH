<div x-data="{ 
    isEstadoModalOpen: false, 
    isEditEstadoModalOpen: false, 
    isDeleteEstadoModalOpen: false, 
    estadoToEdit: { id: '', nombre: '', descripcion: '' }, 
    estadoToDelete: null,
    searchEstadoProyecto: '' 
}">
    <x-admin.tabla-mobile titulo="Estados de Proyecto" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            <div class="flex flex-wrap gap-2 items-center">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchEstadoProyecto',
                    'ordenarOptions' => [
                        'nombre' => 'Nombre',
                        'id' => 'ID'
                    ]
                ])
            </div>
        </x-slot>
        <x-slot name="boton">
            <button 
                @click="isEstadoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm"
            >
                Nuevo Estado
            </button>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">1</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Pendiente</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">El proyecto está pendiente de inicio</td>
                    <td class="py-2 px-4 flex gap-2">
                        <a href="#" @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {id: 1, nombre: 'Pendiente', descripcion: 'El proyecto está pendiente de inicio'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="#" @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 1}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">2</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">En Proceso</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">El proyecto se encuentra actualmente en desarrollo</td>
                    <td class="py-2 px-4 flex gap-2">
                        <a href="#" @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {id: 2, nombre: 'En Proceso', descripcion: 'El proyecto se encuentra actualmente en desarrollo'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="#" @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 2}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">3</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Completado</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Proyecto finalizado exitosamente</td>
                    <td class="py-2 px-4 flex gap-2">
                        <a href="#" @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {id: 3, nombre: 'Completado', descripcion: 'Proyecto finalizado exitosamente'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="#" @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 3}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">4</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Cancelado</td>
                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">Proyecto cancelado por el cliente</td>
                    <td class="py-2 px-4 flex gap-2">
                        <a href="#" @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = {id: 4, nombre: 'Cancelado', descripcion: 'Proyecto cancelado por el cliente'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="#" @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 4}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="estado in [
                    {id: 1, nombre: 'Pendiente', descripcion: 'El proyecto está pendiente de inicio'},
                    {id: 2, nombre: 'En Proceso', descripcion: 'El proyecto se encuentra actualmente en desarrollo'},
                    {id: 3, nombre: 'Completado', descripcion: 'Proyecto finalizado exitosamente'},
                    {id: 4, nombre: 'Cancelado', descripcion: 'Proyecto cancelado por el cliente'}
                ]" :key="estado.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estado.nombre"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular">ID: <span x-text="estado.id"></span></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="estado.descripcion"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isEditEstadoModalOpen = true; estadoToEdit = estado" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isDeleteEstadoModalOpen = true; estadoToDelete = estado" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modal Nuevo Estado -->
    <x-admin.form-modal class="nunito-bold"
        modalName="isEstadoModalOpen" 
        title="Nuevo Estado" 
        submitLabel="Guardar Estado">
        <div class="space-y-4">
            <div>
                <label for="nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
                <input type="text" id="nombre_estado" name="nombre_estado"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <textarea id="descripcion_estado" name="descripcion_estado" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Estado -->
    <x-admin.edit-modal class="nunito-bold"
        modalName="isEditEstadoModalOpen" 
        title="Editar Estado" 
        itemToEdit="estadoToEdit">
        <div>
            <label for="edit_nombre_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre del Estado</label>
            <input type="text" id="edit_nombre_estado" name="edit_nombre_estado" :value="estadoToEdit.nombre"
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
        </div>
        <div class="mt-4">
            <label for="edit_descripcion_estado" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
            <textarea id="edit_descripcion_estado" name="edit_descripcion_estado" rows="3" 
                class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2" 
                x-text="estadoToEdit.descripcion"></textarea>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Estado -->
    <x-admin.confirmation-modal class="nunito-regular"
        modalName="isDeleteEstadoModalOpen"
        itemToDelete="estadoToDelete"
        message="¿Estás seguro de que quieres eliminar el estado?"
    />
</div>
