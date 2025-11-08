<div x-data="{
    isTipoProductoModalOpen: false,
    isTipoProductoEditModalOpen: false,
    isTipoProductoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    tipoProductos: [],
    loadingTipoProductos: false,

    numbersTipoProductos: [],
    currentPageTipoProductos: 1,
    perPageTipoProductos: 10,

    nombre_tipo_producto: '',
    descripcion_tipo_producto: '',
    filtroTipoProducto: '',
    ordenarPor: 'nombre',

    paginatedTipoProductos() {
        return this.tipoProductos.slice(
            (this.currentPageTipoProductos - 1) * this.perPageTipoProductos, 
            this.currentPageTipoProductos * this.perPageTipoProductos
        );
    },
    totalPagesTipoProductos() {
        return Math.ceil(this.tipoProductos.length / this.perPageTipoProductos);
    },
    nextPageTipoProductos() {
        if (this.currentPageTipoProductos < this.totalPagesTipoProductos()) {
            this.currentPageTipoProductos++;
        }
    },
    prevPageTipoProductos() {
        if (this.currentPageTipoProductos > 1) {
            this.currentPageTipoProductos--;
        }
    },

    async fetchTipoProductos() {
        await window.tipoProductosApiHandlers.fetchTipoProductos(this);
        this.numbersTipoProductos = this.tipoProductos; 
    },
    async submitTipoProducto() {
        await window.tipoProductosApiHandlers.submitTipoProducto(this);
        this.fetchTipoProductos(); 
    },
    async updateTipoProducto() {
        await window.tipoProductosApiHandlers.updateTipoProducto(this);
        this.fetchTipoProductos(); 
    },
    async deleteTipoProducto() {
        await window.tipoProductosApiHandlers.deleteTipoProducto(this);
        this.fetchTipoProductos(); 
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formTipoProducto') this.submitTipoProducto();
        if(event.detail.formId === 'formEditTipoProducto') this.updateTipoProducto();
    },
    handleDelete() {
        if (this.isTipoProductoDeleteModalOpen) {
            this.deleteTipoProducto();
        }
    }
}"
    x-init="fetchTipoProductos()"
    x-effect="
    $watch('filtroTipoProducto', () => { fetchTipoProductos(); currentPageTipoProductos = 1; });
    $watch('ordenarPor', () => { fetchTipoProductos(); currentPageTipoProductos = 1; });
"
    @keydown.escape.window="
    isTipoProductoModalOpen = false;
    isTipoProductoEditModalOpen = false;
    isTipoProductoDeleteModalOpen = false;
"
    @modal-submit.window="handleModalSubmit($event)"
    @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Tipos de Producto</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroTipoProducto',
            'ordenarModel' => 'ordenarPor',
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'id' => 'ID Tipo'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'insercion')
            <button
                @click="formTipoProducto = { _touched: {} }; nombre_tipo_producto = ''; descripcion_tipo_producto = ''; isTipoProductoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo tipo de producto
            </button>
            @else
            <button type="button" disabled title="Sin permiso para crear"
                class="bg-green-600/60 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm opacity-60 cursor-not-allowed">
                Nuevo tipo de producto
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingTipoProductos">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de producto...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoProductos && tipoProductos.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay tipos de producto registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoProductos && tipoProductos.length > 0">
                        <template x-for="(tipoProducto, index) in paginatedTipoProductos()" :key="tipoProducto.id_tipo_producto_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedTipoProductos().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoProducto.nombre_tipo_producto"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoProducto.descripcion_tipo_producto"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === paginatedTipoProductos().length - 1 }">
                                    @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'actualizacion')
                                    <a href="#" @click.prevent="formEditTipoProducto = { _touched: {} }; isTipoProductoEditModalOpen = true; itemToEdit = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre_tipo_producto: tipoProducto.nombre_tipo_producto, descripcion_tipo_producto: tipoProducto.descripcion_tipo_producto}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span title="Sin permiso para editar" class="text-blue-300 cursor-not-allowed"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'eliminacion')
                                    <a href="#" @click.prevent="isTipoProductoDeleteModalOpen = true; itemToDelete = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre: tipoProducto.nombre_tipo_producto}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span title="Sin permiso para eliminar" class="text-red-300 cursor-not-allowed"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingTipoProductos">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de producto...
                </div>
            </template>
            <template x-if="!loadingTipoProductos && tipoProductos.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay tipos de producto registrados
                </div>
            </template>
            <template x-if="!loadingTipoProductos && tipoProductos.length > 0">
                <template x-for="tipoProducto in paginatedTipoProductos()" :key="tipoProducto.id_tipo_producto_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipoProducto.nombre_tipo_producto"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="tipoProducto.descripcion_tipo_producto"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'actualizacion')
                            <button @click.prevent="formEditTipoProducto = { _touched: {} }; isTipoProductoEditModalOpen = true; itemToEdit = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre_tipo_producto: tipoProducto.nombre_tipo_producto, descripcion_tipo_producto: tipoProducto.descripcion_tipo_producto}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar" class="px-3 py-1 text-xs bg-blue-600/60 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'eliminacion')
                            <button @click.prevent="isTipoProductoDeleteModalOpen = true; itemToDelete = {id_tipo_producto_pk: tipoProducto.id_tipo_producto_pk, nombre: tipoProducto.nombre_tipo_producto}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para eliminar" class="px-3 py-1 text-xs bg-red-600/60 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="tipoProductos.length > perPageTipoProductos" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageTipoProductos - 1) * perPageTipoProductos + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageTipoProductos * perPageTipoProductos, tipoProductos.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="tipoProductos.length"></strong>
                resultados
            </span>
        </div>

        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageTipoProductos()" :disabled="currentPageTipoProductos === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesTipoProductos()}, (_, i) => i + 1).slice(Math.max(0, currentPageTipoProductos - 3), currentPageTipoProductos + 2)" :key="page">
                    <button @click="currentPageTipoProductos = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-blue-900 hover:text-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageTipoProductos ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageTipoProductos()" :disabled="currentPageTipoProductos === totalPagesTipoProductos()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isTipoProductoModalOpen" title="Nuevo Tipo de Producto"
            submitLabel="Guardar Tipo de Producto" formId="formTipoProducto" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_tipo_producto" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_tipo_producto" x-model="nombre_tipo_producto" required maxlength="150"
                        @input="formTipoProducto = formTipoProducto || { _touched: {} }; formTipoProducto._touched.nombre_tipo_producto = true"
                        @blur="formTipoProducto = formTipoProducto || { _touched: {} }; formTipoProducto._touched.nombre_tipo_producto = true"
                        :class="formTipoProducto && formTipoProducto._touched && formTipoProducto._touched.nombre_tipo_producto && (nombre_tipo_producto === '' || (nombre_tipo_producto && nombre_tipo_producto.length > 150)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small class="block text-xs nunito-regular mt-1" :class="formTipoProducto && formTipoProducto._touched && formTipoProducto._touched.nombre_tipo_producto && (nombre_tipo_producto === '' || (nombre_tipo_producto && nombre_tipo_producto.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion_tipo_producto"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_tipo_producto" x-model="descripcion_tipo_producto" rows="2" maxlength="255"
                        @input="formTipoProducto = formTipoProducto || { _touched: {} }; formTipoProducto._touched.descripcion_tipo_producto = true"
                        @blur="formTipoProducto = formTipoProducto || { _touched: {} }; formTipoProducto._touched.descripcion_tipo_producto = true"
                        :class="formTipoProducto && formTipoProducto._touched && formTipoProducto._touched.descripcion_tipo_producto && (descripcion_tipo_producto === '' || (descripcion_tipo_producto && descripcion_tipo_producto.length > 255)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                    <small class="block text-xs nunito-regular mt-1" :class="formTipoProducto && formTipoProducto._touched && formTipoProducto._touched.descripcion_tipo_producto && (descripcion_tipo_producto === '' || (descripcion_tipo_producto && descripcion_tipo_producto.length > 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoProductoEditModalOpen" title="Editar Tipo de Producto" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditTipoProducto">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_nombre_tipo_producto" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                        <input type="text" id="edit_nombre_tipo_producto" x-model="itemToEdit.nombre_tipo_producto" required maxlength="150"
                            @input="formEditTipoProducto = formEditTipoProducto || { _touched: {} }; formEditTipoProducto._touched.nombre_tipo_producto = true"
                            @blur="formEditTipoProducto = formEditTipoProducto || { _touched: {} }; formEditTipoProducto._touched.nombre_tipo_producto = true"
                            :class="formEditTipoProducto && formEditTipoProducto._touched && formEditTipoProducto._touched.nombre_tipo_producto && (itemToEdit.nombre_tipo_producto === '' || (itemToEdit.nombre_tipo_producto && itemToEdit.nombre_tipo_producto.length > 150)) ? 'border-red-500' : ''"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <small class="block text-xs nunito-regular mt-1" :class="formEditTipoProducto && formEditTipoProducto._touched && formEditTipoProducto._touched.nombre_tipo_producto && (itemToEdit.nombre_tipo_producto === '' || (itemToEdit.nombre_tipo_producto && itemToEdit.nombre_tipo_producto.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                    </div>
                    <div class="col-span-2">
                        <label for="edit_descripcion_tipo_producto"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                        <textarea id="edit_descripcion_tipo_producto" x-model="itemToEdit.descripcion_tipo_producto" rows="2" maxlength="255"
                            @input="formEditTipoProducto = formEditTipoProducto || { _touched: {} }; formEditTipoProducto._touched.descripcion_tipo_producto = true"
                            @blur="formEditTipoProducto = formEditTipoProducto || { _touched: {} }; formEditTipoProducto._touched.descripcion_tipo_producto = true"
                            :class="formEditTipoProducto && formEditTipoProducto._touched && formEditTipoProducto._touched.descripcion_tipo_producto && (itemToEdit.descripcion_tipo_producto === '' || (itemToEdit.descripcion_tipo_producto && itemToEdit.descripcion_tipo_producto.length > 255)) ? 'border-red-500' : ''"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                        <small class="block text-xs nunito-regular mt-1" :class="formEditTipoProducto && formEditTipoProducto._touched && formEditTipoProducto._touched.descripcion_tipo_producto && (itemToEdit.descripcion_tipo_producto === '' || (itemToEdit.descripcion_tipo_producto && itemToEdit.descripcion_tipo_producto.length > 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Tipos de Producto','Tipo de Producto'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoProductoDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este tipo de producto?" />
        @endperm
    </div>
</div>