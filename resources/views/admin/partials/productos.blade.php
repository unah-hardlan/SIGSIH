<div x-data="{
    isProductoModalOpen: false,
    isProductoEditModalOpen: false,
    isProductoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    productos: [],
    loadingProductos: false,
    tipoProductos: [], // Catálogo para el <select>
    loadingTipoProductos: false,
    // Campos para el formulario de NUEVO producto
    sku: '',
    nombre_producto: '',
    descripcion_producto: '',
    precio_unitario: '',
    precio_costo: '',
    precio_venta: '',
    stock_minimo: '',
    id_tipo_producto_fk: '',
    // Filtros
    filtroProducto: '',
    ordenarPor: '',
    async fetchProductos() { await window.productosApiHandlers.fetchProductos(this); },
    async fetchTipoProductos() { await window.tipoProductosApiHandlers.fetchTipoProductos(this); },
    async submitProducto() { await window.productosApiHandlers.submitProducto(this); },
    async updateProducto() { await window.productosApiHandlers.updateProducto(this); },
    async deleteProducto() { await window.productosApiHandlers.deleteProducto(this); },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formProducto') this.submitProducto();
        if(event.detail.formId === 'formEditProducto') this.updateProducto();
    },
    handleDelete() {
        if (this.isProductoDeleteModalOpen) this.deleteProducto();
    }
}"
x-init="fetchProductos(); fetchTipoProductos()"
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
                'ordenarOptions' => [ 'nombre_producto' => 'Nombre', 'precio_venta' => 'Precio Venta', 'id_producto_pk' => 'ID' ]
            ])
        </x-slot>
        <x-slot name="actions">
            <button @click="isProductoModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Producto
            </button>
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
                    <template x-if="loadingProductos"><tr><td colspan="6" class="py-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos...</td></tr></template>
                    <template x-if="!loadingProductos && productos.length === 0"><tr><td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">No hay productos registrados</td></tr></template>
                    <template x-if="!loadingProductos && productos.length > 0">
                        <template x-for="(producto, index) in productos" :key="producto.id_producto_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="producto.sku"></td>
                                <td class="py-2 px-4" x-text="producto.nombre_producto"></td>
                                <td class="py-2 px-4" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(producto.precio_venta)"></td>
                                <td class="py-2 px-4" x-text="producto.stock_minimo"></td>
                                <td class="py-2 px-4" x-text="producto.tipo_producto ? producto.tipo_producto.nombre_tipo_producto : 'N/A'"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="isProductoEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(producto))" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isProductoDeleteModalOpen = true; itemToDelete = { id_producto_pk: producto.id_producto_pk, nombre_producto: producto.nombre_producto }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>
    </x-responsive-table>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Producto -->
        <x-admin.form-modal class="nunito-bold" modalName="isProductoModalOpen" title="Nuevo Producto" submitLabel="Guardar Producto" formId="formProducto" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label for="sku" class="block text-sm font-medium">SKU</label><input type="text" id="sku" x-model="sku" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="nombre_producto" class="block text-sm font-medium">Nombre</label><input type="text" id="nombre_producto" x-model="nombre_producto" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label for="descripcion_producto" class="block text-sm font-medium">Descripción</label><textarea id="descripcion_producto" x-model="descripcion_producto" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
                <div><label for="precio_unitario" class="block text-sm font-medium">Precio Unitario</label><input type="number" step="0.01" id="precio_unitario" x-model="precio_unitario" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="precio_costo" class="block text-sm font-medium">Precio Costo</label><input type="number" step="0.01" id="precio_costo" x-model="precio_costo" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="precio_venta" class="block text-sm font-medium">Precio Venta</label><input type="number" step="0.01" id="precio_venta" x-model="precio_venta" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="stock_minimo" class="block text-sm font-medium">Stock Mínimo</label><input type="number" id="stock_minimo" x-model="stock_minimo" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label for="id_tipo_producto_fk" class="block text-sm font-medium">Tipo Producto</label><select id="id_tipo_producto_fk" x-model="id_tipo_producto_fk" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccionar Tipo</option><template x-for="tipo in tipoProductos" :key="tipo.id_tipo_producto_pk"><option :value="tipo.id_tipo_producto_pk" x-text="tipo.nombre_tipo_producto"></option></template></select></div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Producto -->
        <x-admin.edit-modal class="nunito-bold" modalName="isProductoEditModalOpen" title="Editar Producto" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditProducto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-if="itemToEdit">
                <div><label for="edit_sku" class="block text-sm font-medium">SKU</label><input type="text" id="edit_sku" x-model="itemToEdit.sku" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="edit_nombre_producto" class="block text-sm font-medium">Nombre</label><input type="text" id="edit_nombre_producto" x-model="itemToEdit.nombre_producto" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label for="edit_descripcion_producto" class="block text-sm font-medium">Descripción</label><textarea id="edit_descripcion_producto" x-model="itemToEdit.descripcion_producto" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
                <div><label for="edit_precio_unitario" class="block text-sm font-medium">Precio Unitario</label><input type="number" step="0.01" id="edit_precio_unitario" x-model="itemToEdit.precio_unitario" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="edit_precio_costo" class="block text-sm font-medium">Precio Costo</label><input type="number" step="0.01" id="edit_precio_costo" x-model="itemToEdit.precio_costo" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="edit_precio_venta" class="block text-sm font-medium">Precio Venta</label><input type="number" step="0.01" id="edit_precio_venta" x-model="itemToEdit.precio_venta" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label for="edit_stock_minimo" class="block text-sm font-medium">Stock Mínimo</label><input type="number" id="edit_stock_minimo" x-model="itemToEdit.stock_minimo" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label for="edit_id_tipo_producto_fk" class="block text-sm font-medium">Tipo Producto</label><select id="edit_id_tipo_producto_fk" x-model="itemToEdit.id_tipo_producto_fk" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccionar Tipo</option><template x-for="tipo in tipoProductos" :key="tipo.id_tipo_producto_pk"><option :value="tipo.id_tipo_producto_pk" x-text="tipo.nombre_tipo_producto"></option></template></select></div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isProductoDeleteModalOpen" itemToDelete="itemToDelete"
            itemNameProperty="nombre_producto"
            message="¿Estás seguro de que quieres eliminar este producto?" />
    </div>
</div>