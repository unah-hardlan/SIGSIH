<div x-data="{
    isTipoProductoModalOpen: false,
    isTipoProductoEditModalOpen: false,
    isTipoProductoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    tipoProductos: [],
    loadingTipoProductos: false,
    nombre_tipo_producto: '',
    descripcion_tipo_producto: '',
    async fetchTipoProductos() {
        await window.tipoProductosApiHandlers.fetchTipoProductos(this);
    },
    async submitTipoProducto() {
        await window.tipoProductosApiHandlers.submitTipoProducto(this);
    },
    async updateTipoProducto() {
        await window.tipoProductosApiHandlers.updateTipoProducto(this);
    },
    async deleteTipoProducto() {
        await window.tipoProductosApiHandlers.deleteTipoProducto(this);
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formTipoProducto') this.submitTipoProducto();
        if(event.detail.formId === 'formEditTipoProducto') this.updateTipoProducto();
    },
    handleDelete() {
        this.deleteTipoProducto();
    }
}"
x-init="fetchTipoProductos()"
@keydown.escape.window="
    isTipoProductoModalOpen = false;
    isTipoProductoEditModalOpen = false;
    isTipoProductoDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete="handleDelete()">

    <x-admin.tabla-mobile titulo="Tipo de Producto" class="nunito-bold bg-white dark:bg-gray-900">
        <x-slot name="filtros">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroTipoProducto',
                'ordenarOptions' => [
                    'nombre' => 'Nombre',
                    'id' => 'ID Tipo'
                ]
            ])
        </x-slot>
        <x-slot name="boton">
            <button
                @click="isTipoProductoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm"
            >
                Nuevo tipo de producto
            </button>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">ID Tipo</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(tipoProducto, index) in tipoProductos" :key="tipoProducto.id_tipo_producto_pk">
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                            :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === tipoProductos.length - 1 }">
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" :class="{ 'first:rounded-bl-lg': index === tipoProductos.length - 1 }" x-text="tipoProducto.id_tipo_producto_pk"></td>
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoProducto.nombre_tipo_producto"></td>
                            <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoProducto.descripcion_tipo_producto"></td>
                            <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === tipoProductos.length - 1 }">
                                <a href="#" @click.prevent="isTipoProductoEditModalOpen = true; itemToEdit = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre_tipo_producto: tipoProducto.nombre_tipo_producto, descripcion_tipo_producto: tipoProducto.descripcion_tipo_producto}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="#" @click.prevent="isTipoProductoDeleteModalOpen = true; itemToDelete = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre: tipoProducto.nombre_tipo_producto}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <x-slot name="mobileTemplate">
            <div class="space-y-4">
                <template x-for="tipoProducto in tipoProductos" :key="tipoProducto.id_tipo_producto_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipoProducto.nombre_tipo_producto"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'ID: ' + tipoProducto.id_tipo_producto_pk"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Descripción:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoProducto.descripcion_tipo_producto"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isTipoProductoEditModalOpen = true; itemToEdit = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre_tipo_producto: tipoProducto.nombre_tipo_producto, descripcion_tipo_producto: tipoProducto.descripcion_tipo_producto}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isTipoProductoDeleteModalOpen = true; itemToDelete = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre: tipoProducto.nombre_tipo_producto}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-admin.tabla-mobile>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Tipo de Producto -->
        <x-admin.form-modal class="nunito-bold" modalName="isTipoProductoModalOpen" title="Nuevo Tipo de Producto"
            submitLabel="Guardar Tipo de Producto" formId="formTipoProducto" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_tipo_producto" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_tipo_producto" x-model="nombre_tipo_producto"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion_tipo_producto"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_tipo_producto" x-model="descripcion_tipo_producto" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Tipo de Producto -->
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoProductoEditModalOpen" title="Editar Tipo de Producto" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditTipoProducto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_nombre_tipo_producto" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="edit_nombre_tipo_producto" x-model="itemToEdit.nombre_tipo_producto"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion_tipo_producto"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_tipo_producto" x-model="itemToEdit.descripcion_tipo_producto" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoProductoDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este tipo de producto?" />
    </div>
</div>