<div x-data="{ isTipoModalOpen: false, isTipoEditModalOpen: false, tipoToEdit: {id_tipo_producto_pk: '', nombre_tipo_producto: '', descripcion_tipo_producto: ''}, isTipoDeleteModalOpen: false, tipoToDelete: {id_tipo_producto_pk: '', nombre_tipo_producto: ''}, filtroNombre: '' }">
    <x-admin.tabla-mobile titulo="Tipo de Producto" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroNombre',
                'ordenarOptions' => [
                    'nombre_tipo_producto' => 'Nombre',
                    'id_tipo_producto_pk' => 'ID Tipo Producto'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button @click="isTipoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo tipo
            </button>
        </x-slot>

        <table class="min-w-full text-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                <tr>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">ID Tipo Producto
                    </th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Nombre</th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Descripción
                    </th>
                    <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="tipo in [
                    {id_tipo_producto_pk: 1, nombre_tipo_producto: 'Hardware', descripcion_tipo_producto: 'Dispositivos físicos como computadoras, impresoras, etc.'},
                    {id_tipo_producto_pk: 2, nombre_tipo_producto: 'Software', descripcion_tipo_producto: 'Aplicaciones y licencias.'}
                    ]" :key="tipo.id_tipo_producto_pk">
                    <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                        x-show="!filtroNombre || tipo.nombre_tipo_producto.toLowerCase().includes(filtroNombre.toLowerCase())">
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="tipo.id_tipo_producto_pk"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="tipo.nombre_tipo_producto"></td>
                        <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="tipo.descripcion_tipo_producto"></td>
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

        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="tipo in [
                    {id_tipo_producto_pk: 1, nombre_tipo_producto: 'Hardware', descripcion_tipo_producto: 'Dispositivos físicos como computadoras, impresoras, etc.'},
                    {id_tipo_producto_pk: 2, nombre_tipo_producto: 'Software', descripcion_tipo_producto: 'Aplicaciones y licencias.'}
                ]" :key="tipo.id_tipo_producto_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                    x-text="tipo.nombre_tipo_producto"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular"
                                    x-text="'ID: ' + tipo.id_tipo_producto_pk"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span
                                    class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span
                                    class="text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="tipo.descripcion_tipo_producto"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click="isTipoEditModalOpen = true; tipoToEdit = {...tipo}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click="isTipoDeleteModalOpen = true; tipoToDelete = {...tipo}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

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