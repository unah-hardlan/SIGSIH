<div x-data="{
        isTipoModalOpen: false, 
        isTipoEditModalOpen: false, 
        tipoToEdit: {id_tipo_producto_pk: '', nombre_tipo_producto: '', descripcion_tipo_producto: ''}, 
        isTipoDeleteModalOpen: false, 
        tipoToDelete: {id_tipo_producto_pk: '', nombre_tipo_producto: ''}, 
        filtroNombre: ''
    }">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
            <h2 class="text-2xl text-gray-800 nunito-bold lg:whitespace-nowrap lg:block">Tipo de producto</h2>
            <div class="w-full flex justify-center sm:justify-end mt-2 sm:mt-0">
                <button @click="isTipoModalOpen = true"
                    class="bg-green-600 hover:bg-green-700 text-white sm:p-2 lg:px-4 lg:py-2 lg:text-base rounded-lg nunito-regular w-11/12 sm:w-auto text-sm transition-colors duration-200 ease-in-out">Nuevo tipo
                </button>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 items-center mb-4">
            <input type="text" x-model="filtroNombre" placeholder="Buscar por nombre..."
                class="border rounded px-3 py-2 text-sm w-full sm:w-48" />
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">ID Tipo Producto</th>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Descripción</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="tipo in [
                        {id_tipo_producto_pk: 1, nombre_tipo_producto: 'Hardware', descripcion_tipo_producto: 'Dispositivos físicos como computadoras, impresoras, etc.'},
                        {id_tipo_producto_pk: 2, nombre_tipo_producto: 'Software', descripcion_tipo_producto: 'Aplicaciones y licencias.'}
                        ]" :key="tipo.id_tipo_producto_pk">
                        <tr class="border-b nunito-regular"
                            x-show="!filtroNombre || tipo.nombre_tipo_producto.toLowerCase().includes(filtroNombre.toLowerCase())">
                            <td class="py-2 px-4" x-text="tipo.id_tipo_producto_pk"></td>
                            <td class="py-2 px-4" x-text="tipo.nombre_tipo_producto"></td>
                            <td class="py-2 px-4" x-text="tipo.descripcion_tipo_producto"></td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click="isTipoEditModalOpen = true; tipoToEdit = {...tipo}"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click="isTipoDeleteModalOpen = true; tipoToDelete = {...tipo}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Modal Nuevo Tipo de Producto -->
        <x-admin.form-modal class="nunito-bold" modalName="isTipoModalOpen" title="Nuevo Tipo de Producto" submitLabel="Guardar"
            maxWidth="max-w-md">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Nombre del tipo" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea class="w-full border rounded px-3 py-2 nunito-regular" placeholder="Descripción"></textarea>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Tipo de Producto -->
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoEditModalOpen" title="Editar Tipo de Producto" itemToEdit="tipoToEdit"
            maxWidth="max-w-md">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Nombre</label>
                <input type="text" x-model="tipoToEdit.nombre_tipo_producto" class="w-full border rounded px-3 py-2 nunito-regular" />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                <textarea x-model="tipoToEdit.descripcion_tipo_producto"
                    class="w-full border rounded px-3 py-2 nunito-regular"></textarea>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Eliminar Tipo de Producto -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoDeleteModalOpen" itemToDelete="tipoToDelete"
            message="¿Seguro que deseas eliminar este tipo de producto?" />
    </div>
</div>