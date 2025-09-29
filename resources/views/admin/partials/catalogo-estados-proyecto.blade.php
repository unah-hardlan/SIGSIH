<div x-data="{ 
    isEstadoModalOpen: false, 
    isEditEstadoModalOpen: false, 
    isDeleteEstadoModalOpen: false, 
    estadoToEdit: { id: '', nombre: '', descripcion: '' }, 
    estadoToDelete: null,
    searchEstadoProyecto: '' 
}">
    <x-admin.tabla-crud class="nunito-bold" :titulo="'Estados de Proyecto'">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchEstadoProyecto',
                'filtrosSelect' => [
                    'estadoProyecto' => [
                        'label' => 'Estado',
                        'options' => ['Planificación', 'En Proceso', 'Completado', 'Cancelado']
                    ]
                ],
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <div class="w-full flex justify-center sm:justify-end">
                <button @click="isEstadoModalOpen = true"
                    class="w-11/12 sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center text-sm">Nuevo Estado
                </button>
            </div>
        </x-slot>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Nombre</th> 
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 dark:text-white">1</td>
                        <td class="py-2 px-4 dark:text-white">Planificación</td>
                        <td class="py-2 px-4 dark:text-white">Proyecto en fase de planificación</td>
                        <td class="py-2 px-4 flex gap-2 dark:text-white">
                            <a href="#" @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 1, nombre: 'Planificación', descripcion: 'Proyecto en fase de planificación'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 1, nombre: 'Planificación'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 dark:text-white">2</td>
                        <td class="py-2 px-4 dark:text-white">En Proceso</td>
                        <td class="py-2 px-4 dark:text-white">El proyecto se encuentra actualmente en desarrollo</td>
                        <td class="py-2 px-4 flex gap-2 dark:text-white">
                            <a href="#" @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 2, nombre: 'En Proceso', descripcion: 'El proyecto se encuentra actualmente en desarrollo'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 2, nombre: 'En Proceso'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 dark:text-white">3</td>
                        <td class="py-2 px-4 dark:text-white">Completado</td>
                        <td class="py-2 px-4 dark:text-white">Proyecto finalizado exitosamente</td>
                        <td class="py-2 px-4 flex gap-2 dark:text-white">
                            <a href="#" @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 3, nombre: 'Completado', descripcion: 'Proyecto finalizado exitosamente'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 3, nombre: 'Completado'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700 nunito-regular">
                        <td class="py-2 px-4 dark:text-white">4</td>
                        <td class="py-2 px-4 dark:text-white">Cancelado</td>
                        <td class="py-2 px-4 dark:text-white">Proyecto cancelado por el cliente</td>
                        <td class="py-2 px-4 flex gap-2 dark:text-white">
                            <a href="#" @click="isEditEstadoModalOpen = true; estadoToEdit = {id: 4, nombre: 'Cancelado', descripcion: 'Proyecto cancelado por el cliente'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                            <a href="#" @click="isDeleteEstadoModalOpen = true; estadoToDelete = {id: 4, nombre: 'Cancelado'}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-admin.tabla-crud>

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
