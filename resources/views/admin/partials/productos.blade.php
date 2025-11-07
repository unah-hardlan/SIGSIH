<div x-data="{
    isProductoModalOpen: false,
    isProductoEditModalOpen: false,
    isProductoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    productos: [],
    loadingProductos: false,

    // Variables de Paginación
    numbersProductos: [],
    currentPageProductos: 1,
    perPageProductos: 10,

    tipoProductos: [],
    loadingTipoProductos: false,
    formProducto: { _touched: {} },
    formEditProducto: { _touched: {} },
    sku: '',
    nombre_producto: '',
    descripcion_producto: '',
    precio_unitario: '',
    precio_costo: '',
    precio_venta: '',
    stock_minimo: '',
    id_tipo_producto_fk: '',
    filtroProducto: '',
    ordenarPor: 'nombre_producto',

    // Métodos de Paginación
    paginatedProductos() {
        return this.productos.slice(
            (this.currentPageProductos - 1) * this.perPageProductos, 
            this.currentPageProductos * this.perPageProductos
        );
    },
    totalPagesProductos() {
        return Math.ceil(this.productos.length / this.perPageProductos);
    },
    nextPageProductos() {
        if (this.currentPageProductos < this.totalPagesProductos()) {
            this.currentPageProductos++;
        }
    },
    prevPageProductos() {
        if (this.currentPageProductos > 1) {
            this.currentPageProductos--;
        }
    },

    // Sincronizar Alias en cada operación CRUD
    async fetchProductos() { 
        await window.productosApiHandlers.fetchProductos(this); 
        this.numbersProductos = this.productos;
    },
    async fetchTipoProductos() { await window.tipoProductosApiHandlers.fetchTipoProductos(this); },
    async submitProducto() { 
        await window.productosApiHandlers.submitProducto(this); 
        this.fetchProductos();
    },
    async updateProducto() { 
        await window.productosApiHandlers.updateProducto(this); 
        this.fetchProductos();
    },
    async deleteProducto() { 
        await window.productosApiHandlers.deleteProducto(this); 
        this.fetchProductos();
    },

    handleModalSubmit(event) {
        if(event.detail.formId === 'formProducto') this.submitProducto();
        if(event.detail.formId === 'formEditProducto') this.updateProducto();
    },
    handleDelete() {
        if (this.isProductoDeleteModalOpen) this.deleteProducto();
    }
}"
    x-init="fetchProductos(); fetchTipoProductos()"
    x-effect="
    $watch('filtroProducto', () => { fetchProductos(); currentPageProductos = 1; });
    $watch('ordenarPor', () => { fetchProductos(); currentPageProductos = 1; });
"
    @keydown.escape.window="isProductoModalOpen = false; isProductoEditModalOpen = false; isProductoDeleteModalOpen = false;"
    @modal-submit.window="handleModalSubmit($event)"
    @confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Productos</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroProducto',
            'ordenarModel' => 'ordenarPor',
            'ordenarOptions' => [ 'nombre_producto' => 'Nombre', 'precio_venta' => 'Precio Venta', 'id_producto_pk' => 'ID' ]
            ])
        </x-slot>
        <x-slot name="actions">
            @perm(['Productos','Inventario'], 'insercion')
            <button @click="isProductoModalOpen = true; formProducto._touched = {}; sku=''; nombre_producto=''; descripcion_producto=''; precio_unitario=''; precio_costo=''; precio_venta=''; stock_minimo=''; id_tipo_producto_fk='';"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Producto
            </button>
            @else
            <button disabled title="Sin permiso para crear"
                class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm cursor-not-allowed">
                Nuevo Producto
            </button>
            @endperm
            <a href="/admin/reportes-header?modulo=Productos&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </x-slot>
        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">SKU</th>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Precio Venta</th>
                        <th class="py-2 px-4 text-left">Stock Mínimo</th>
                        <th class="py-2 px-4 text-left">Tipo Producto</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingProductos">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos...</td>
                        </tr>
                    </template>
                    <template x-if="!loadingProductos && productos.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">No hay productos registrados</td>
                        </tr>
                    </template>
                    <template x-if="!loadingProductos && productos.length > 0">
                        <template x-for="(producto, index) in paginatedProductos()" :key="producto.id_producto_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="producto.sku"></td>
                                <td class="py-2 px-4" x-text="producto.nombre_producto"></td>
                                <td class="py-2 px-4" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(producto.precio_venta)"></td>
                                <td class="py-2 px-4" x-text="producto.stock_minimo"></td>
                                <td class="py-2 px-4" x-text="producto.tipo_producto ? producto.tipo_producto.nombre_tipo_producto : 'N/A'"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    @perm(['Productos','Inventario'], 'actualizacion')
                                    <a href="#" @click.prevent="formEditProducto = { _touched: {} }; isProductoEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(producto))" class="text-blue-500 hover:text-blue-700" title="Editar"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Productos','Inventario'], 'eliminacion')
                                    <a href="#" @click.prevent="isProductoDeleteModalOpen = true; itemToDelete = { id_producto_pk: producto.id_producto_pk, nombre_producto: producto.nombre_producto }" class="text-red-500 hover:text-red-700" title="Eliminar"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-red-300 cursor-not-allowed" title="Sin permiso para eliminar"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingProductos">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos...
                </div>
            </template>
            <template x-if="!loadingProductos && productos.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 nunito-regular">
                    No hay productos registrados
                </div>
            </template>
            <template x-if="!loadingProductos && productos.length > 0">
                <template x-for="producto in paginatedProductos()" :key="producto.id_producto_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2 border border-gray-600 dark:border-gray-500">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="producto.nombre_producto"></h3>
                            <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded nunito-regular" x-text="producto.sku"></span>
                        </div>

                        <div class="space-y-1 text-sm">
                            <p class="text-gray-600 dark:text-gray-400 nunito-regular" x-text="producto.descripcion_producto"></p>

                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400 nunito-regular">Precio:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(producto.precio_venta)"></span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 nunito-regular">Stock Mínimo:</span>
                                <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="producto.stock_minimo"></span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 nunito-regular">Tipo:</span>
                                <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="producto.tipo_producto ? producto.tipo_producto.nombre_tipo_producto : 'N/A'"></span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Productos','Inventario'], 'actualizacion')
                            <button @click.prevent="formEditProducto = { _touched: {} }; isProductoEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(producto))" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button disabled title="Sin permiso para editar" class="px-3 py-1 text-xs bg-gray-400 text-white rounded cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Productos','Inventario'], 'eliminacion')
                            <button @click.prevent="isProductoDeleteModalOpen = true; itemToDelete = { id_producto_pk: producto.id_producto_pk, nombre_producto: producto.nombre_producto }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button disabled title="Sin permiso para eliminar" class="px-3 py-1 text-xs bg-red-300 text-white rounded cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Componente de Paginación -->
    <div x-show="productos.length > perPageProductos" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <!-- Mostrando -->
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageProductos - 1) * perPageProductos + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageProductos * perPageProductos, productos.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="productos.length"></strong>
                resultados
            </span>
        </div>

        <!-- Controls -->
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageProductos()" :disabled="currentPageProductos === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesProductos()}, (_, i) => i + 1).slice(Math.max(0, currentPageProductos - 3), currentPageProductos + 2)" :key="page">
                    <button @click="currentPageProductos = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageProductos ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageProductos()" :disabled="currentPageProductos === totalPagesProductos()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Producto -->
        <x-admin.form-modal class="nunito-bold" modalName="isProductoModalOpen" title="Nuevo Producto" submitLabel="Guardar Producto" formId="formProducto" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-sm font-medium">SKU</label>
                    <input type="text" id="sku" x-model="sku" maxlength="50" required @input="formProducto._touched.sku = true" @blur="formProducto._touched.sku = true"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.sku && (sku === '' || (sku && sku.length >= 50)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.sku && (sku === '' || (sku && sku.length >= 50)) ? 'text-red-500' : ''">Requerido. Máximo 50 caracteres.</small>
                </div>
                <div>
                    <label for="nombre_producto" class="block text-sm font-medium">Nombre</label>
                    <input type="text" id="nombre_producto" x-model="nombre_producto" maxlength="150" required @input="formProducto._touched.nombre = true" @blur="formProducto._touched.nombre = true"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.nombre && (nombre_producto === '' || (nombre_producto && nombre_producto.length >= 150)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.nombre && (nombre_producto === '' || (nombre_producto && nombre_producto.length >= 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="md:col-span-2">
                    <label for="descripcion_producto" class="block text-sm font-medium">Descripción</label>
                    <textarea id="descripcion_producto" x-model="descripcion_producto" maxlength="500" rows="3" @input="formProducto._touched.descripcion = true" @blur="formProducto._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.descripcion && (descripcion_producto === '' || (descripcion_producto && descripcion_producto.length >= 255)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.descripcion && (descripcion_producto === '' || (descripcion_producto && descripcion_producto.length >= 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="precio_unitario" class="block text-sm font-medium">Precio Unitario</label>
                    <input type="number" step="0.01" id="precio_unitario" x-model="precio_unitario" required @input="formProducto._touched.precio_unitario = true" @blur="formProducto._touched.precio_unitario = true"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.precio_unitario && !precio_unitario ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.precio_unitario && !precio_unitario ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label for="precio_costo" class="block text-sm font-medium">Precio Costo</label>
                    <input type="number" step="0.01" id="precio_costo" x-model="precio_costo" @input="formProducto._touched.precio_costo = true" @blur="formProducto._touched.precio_costo = true"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.precio_costo && !precio_costo ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.precio_costo && !precio_costo ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label for="precio_venta" class="block text-sm font-medium">Precio Venta</label>
                    <input type="number" step="0.01" id="precio_venta" x-model="precio_venta" required @input="formProducto._touched.precio_venta = true" @blur="formProducto._touched.precio_venta = true"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.precio_venta && !precio_venta ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.precio_venta && !precio_venta ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label for="stock_minimo" class="block text-sm font-medium">Stock Mínimo</label>
                    <input type="number" id="stock_minimo" x-model="stock_minimo" required @input="formProducto._touched.stock = true" @blur="formProducto._touched.stock = true"
                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.stock && !stock_minimo ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.stock && !stock_minimo ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label for="id_tipo_producto_fk" class="block text-sm font-medium">Tipo Producto</label>
                    <select id="id_tipo_producto_fk" x-model="id_tipo_producto_fk" required @change="formProducto._touched.tipo = true" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                        :class="formProducto._touched && formProducto._touched.tipo && !id_tipo_producto_fk ? 'border-red-500' : ''">
                        <option value="">Seleccionar Tipo</option>
                        <template x-for="tipo in tipoProductos" :key="tipo.id_tipo_producto_pk">
                            <option :value="tipo.id_tipo_producto_pk" x-text="tipo.nombre_tipo_producto"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500" :class="formProducto._touched && formProducto._touched.tipo && !id_tipo_producto_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Producto -->
        <x-admin.edit-modal class="nunito-bold" modalName="isProductoEditModalOpen" title="Editar Producto" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditProducto">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_sku" class="block text-sm font-medium">SKU</label>
                        <input type="text" id="edit_sku" x-model="itemToEdit.sku" maxlength="50" required @input="formEditProducto._touched.sku = true" @blur="formEditProducto._touched.sku = true"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.sku && (itemToEdit.sku === '' || (itemToEdit.sku && itemToEdit.sku.length >= 50)) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.sku && (itemToEdit.sku === '' || (itemToEdit.sku && itemToEdit.sku.length >= 50)) ? 'text-red-500' : ''">Requerido. Máximo 50 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_nombre_producto" class="block text-sm font-medium">Nombre</label>
                        <input type="text" id="edit_nombre_producto" x-model="itemToEdit.nombre_producto" maxlength="150" required @input="formEditProducto._touched.nombre = true" @blur="formEditProducto._touched.nombre = true"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.nombre && (itemToEdit.nombre_producto === '' || (itemToEdit.nombre_producto && itemToEdit.nombre_producto.length >= 150)) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.nombre && (itemToEdit.nombre_producto === '' || (itemToEdit.nombre_producto && itemToEdit.nombre_producto.length >= 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_descripcion_producto" class="block text-sm font-medium">Descripción</label>
                        <textarea id="edit_descripcion_producto" x-model="itemToEdit.descripcion_producto" maxlength="255" rows="3" @input="formEditProducto._touched.descripcion = true" @blur="formEditProducto._touched.descripcion = true"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.descripcion && (itemToEdit.descripcion_producto === '' || (itemToEdit.descripcion_producto && itemToEdit.descripcion_producto.length >= 255)) ? 'border-red-500' : ''"></textarea>
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.descripcion && (itemToEdit.descripcion_producto === '' || (itemToEdit.descripcion_producto && itemToEdit.descripcion_producto.length >= 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_precio_unitario" class="block text-sm font-medium">Precio Unitario</label>
                        <input type="number" step="0.01" id="edit_precio_unitario" x-model="itemToEdit.precio_unitario" required @input="formEditProducto._touched.precio_unitario = true" @blur="formEditProducto._touched.precio_unitario = true"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.precio_unitario && !itemToEdit.precio_unitario ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.precio_unitario && !itemToEdit.precio_unitario ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label for="edit_precio_costo" class="block text-sm font-medium">Precio Costo</label>
                        <input type="number" step="0.01" id="edit_precio_costo" x-model="itemToEdit.precio_costo" @input="formEditProducto._touched.precio_costo = true" @blur="formEditProducto._touched.precio_costo = true"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.precio_costo && !itemToEdit.precio_costo ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.precio_costo && !itemToEdit.precio_costo ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label for="edit_precio_venta" class="block text-sm font-medium">Precio Venta</label>
                        <input type="number" step="0.01" id="edit_precio_venta" x-model="itemToEdit.precio_venta" required @input="formEditProducto._touched.precio_venta = true" @blur="formEditProducto._touched.precio_venta = true"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.precio_venta && !itemToEdit.precio_venta ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.precio_venta && !itemToEdit.precio_venta ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label for="edit_stock_minimo" class="block text-sm font-medium">Stock Mínimo</label>
                        <input type="number" id="edit_stock_minimo" x-model="itemToEdit.stock_minimo" required @input="formEditProducto._touched.stock = true" @blur="formEditProducto._touched.stock = true"
                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.stock && !itemToEdit.stock_minimo ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.stock && !itemToEdit.stock_minimo ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_id_tipo_producto_fk" class="block text-sm font-medium">Tipo Producto</label>
                        <select id="edit_id_tipo_producto_fk" x-model="itemToEdit.id_tipo_producto_fk" required @change="formEditProducto._touched.tipo = true" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
                            :class="formEditProducto._touched && formEditProducto._touched.tipo && !itemToEdit.id_tipo_producto_fk ? 'border-red-500' : ''">
                            <option value="">Seleccionar Tipo</option>
                            <template x-for="tipo in tipoProductos" :key="tipo.id_tipo_producto_pk">
                                <option :value="tipo.id_tipo_producto_pk" x-text="tipo.nombre_tipo_producto"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditProducto._touched && formEditProducto._touched.tipo && !itemToEdit.id_tipo_producto_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isProductoDeleteModalOpen" itemToDelete="itemToDelete"
            itemNameProperty="nombre_producto"
            message="¿Estás seguro de que quieres eliminar este producto?" />
    </div>
</div>