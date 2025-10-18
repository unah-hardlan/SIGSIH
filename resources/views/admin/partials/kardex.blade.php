<div x-data="{
    // --- Estado del CRUD ---
    isKardexModalOpen: false,
    isKardexEditModalOpen: false,
    isKardexDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    kardex: [],
    loadingKardex: false,

    // --- Modelo para el formulario de Nuevo Movimiento ---
    newMovimiento: {
        id_origen_fk: null,
        id_producto_fk: '',
        id_tipo_movimiento_fk: '',
        cantidad: '',
        fecha_movimiento: '',
        motivo: ''
    },

    // --- Catálogos para los <select> ---
    catalogoProductos: [],
    catalogoTiposMovimiento: [],
    catalogoOrigenes: [],
    
    // --- Filtros ---
    filtroKardex: '',
    ordenarPor: '',

    // --- Lógica de la API ---
    async fetchKardex() {
        await window.kardexApiHandlers.fetchKardex(this);
    },
    async submitKardex() {
        await window.kardexApiHandlers.submitKardex(this);
    },
    async updateKardex() {
        await window.kardexApiHandlers.updateKardex(this);
    },
    async deleteKardex() {
        await window.kardexApiHandlers.deleteKardex(this);
    },
    
    async fetchCatalogos() {
        await window.catalogosKardexHandlers.fetchProductos(this);
        await window.catalogosKardexHandlers.fetchTiposMovimiento(this);
        await window.catalogosKardexHandlers.fetchOrigenes(this);
    },
    
    // --- Manejadores de Eventos ---
    handleModalSubmit(event) {
        if (event.detail.formId === 'formKardex') this.submitKardex();
        if (event.detail.formId === 'formEditKardex') this.updateKardex();
    },
    handleDelete() {
        if (this.isKardexDeleteModalOpen) this.deleteKardex();
    }
}"
x-init="fetchKardex(); fetchCatalogos();"
@keydown.escape.window="
    isKardexModalOpen = false;
    isKardexEditModalOpen = false;
    isKardexDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Kardex de Inventario</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'filtroKardex',
                'ordenarOptions' => [
                    'fecha_movimiento' => 'Fecha',
                    'cantidad' => 'Cantidad'
                ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button @click="isKardexModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Movimiento
            </button>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Producto</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Tipo Movimiento</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Cantidad</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Origen</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Motivo</th>
                        <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingKardex">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Cargando movimientos...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingKardex && kardex.length === 0">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay movimientos registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingKardex && kardex.length > 0">
                        <template x-for="(movimiento, index) in kardex" :key="movimiento.id_kardex_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === kardex.length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="movimiento.producto ? movimiento.producto.nombre_producto : 'N/A'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="movimiento.fecha_movimiento"></td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded text-xs font-semibold"
                                          :class="movimiento.tipo_movimiento && movimiento.tipo_movimiento.nombre === 'ENTRADA' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                          x-text="movimiento.tipo_movimiento ? movimiento.tipo_movimiento.nombre : 'N/A'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="movimiento.cantidad"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="movimiento.origen ? movimiento.origen.nombre_origen : 'N/A'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="movimiento.motivo"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="isKardexEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(movimiento))" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isKardexDeleteModalOpen = true; itemToDelete = { id_kardex_pk: movimiento.id_kardex_pk, motivo: movimiento.motivo }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingKardex">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando movimientos...
                </div>
            </template>
            <template x-if="!loadingKardex && kardex.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay movimientos registrados
                </div>
            </template>
            <template x-if="!loadingKardex && kardex.length > 0">
                <template x-for="movimiento in kardex" :key="movimiento.id_kardex_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="movimiento.producto ? movimiento.producto.nombre_producto : 'N/A'"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Fecha: ' + movimiento.fecha_movimiento"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular">
                            <span x-text="'Tipo: '"></span>
                            <span class="px-2 py-1 rounded text-xs font-semibold"
                                  :class="movimiento.tipo_movimiento && movimiento.tipo_movimiento.nombre === 'ENTRADA' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                  x-text="movimiento.tipo_movimiento ? movimiento.tipo_movimiento.nombre : 'N/A'"></span>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Cantidad: ' + movimiento.cantidad"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Origen: ' + (movimiento.origen ? movimiento.origen.nombre_origen : 'N/A')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Motivo: ' + movimiento.motivo"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button @click.prevent="isKardexEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(movimiento))" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="isKardexDeleteModalOpen = true; itemToDelete = { id_kardex_pk: movimiento.id_kardex_pk, motivo: movimiento.motivo }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Movimiento -->
        <x-admin.form-modal modalName="isKardexModalOpen" title="Nuevo Movimiento" submitLabel="Guardar Movimiento" formId="formKardex" maxWidth="max-w-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="new_id_producto_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Producto</label>
                    <select id="new_id_producto_fk" x-model="newMovimiento.id_producto_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option value="">Seleccione un Producto...</option>
                        <template x-for="producto in catalogoProductos" :key="producto.id_producto_pk">
                            <option :value="producto.id_producto_pk" x-text="producto.nombre_producto"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="new_id_tipo_movimiento_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Movimiento</label>
                    <select id="new_id_tipo_movimiento_fk" x-model="newMovimiento.id_tipo_movimiento_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option value="">Seleccione un Tipo...</option>
                        <template x-for="tipo in catalogoTiposMovimiento" :key="tipo.id_tipo_movimiento_pk">
                            <option :value="tipo.id_tipo_movimiento_pk" x-text="tipo.nombre_tipo_movimiento"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="new_id_origen_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Origen (Opcional)</label>
                    <select id="new_id_origen_fk" x-model="newMovimiento.id_origen_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option :value="null">Ninguno</option>
                        <template x-for="origen in catalogoOrigenes" :key="origen.id_origen_pk">
                            <option :value="origen.id_origen_pk" x-text="origen.nombre_origen"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="new_cantidad" class="block text-sm font-medium text-gray-700 nunito-bold">Cantidad</label>
                    <input type="number" step="0.001" id="new_cantidad" x-model="newMovimiento.cantidad" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="new_fecha_movimiento" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Movimiento</label>
                    <input type="date" id="new_fecha_movimiento" x-model="newMovimiento.fecha_movimiento" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="md:col-span-2">
                    <label for="new_motivo" class="block text-sm font-medium text-gray-700 nunito-bold">Motivo / Razón</label>
                    <textarea id="new_motivo" x-model="newMovimiento.motivo" rows="3" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Movimiento -->
        <x-admin.edit-modal modalName="isKardexEditModalOpen" title="Editar Movimiento" itemToEdit="itemToEdit" formId="formEditKardex" maxWidth="max-w-lg">
            <template x-if="itemToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="edit_id_producto_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Producto</label>
                    <select id="edit_id_producto_fk" x-model="itemToEdit.id_producto_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="producto in catalogoProductos" :key="producto.id_producto_pk">
                            <option :value="producto.id_producto_pk" x-text="producto.nombre_producto"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="edit_id_tipo_movimiento_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Movimiento</label>
                    <select id="edit_id_tipo_movimiento_fk" x-model="itemToEdit.id_tipo_movimiento_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="tipo in catalogoTiposMovimiento" :key="tipo.id_tipo_movimiento_pk">
                            <option :value="tipo.id_tipo_movimiento_pk" x-text="tipo.nombre_tipo_movimiento"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="edit_id_origen_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Origen (Opcional)</label>
                    <select id="edit_id_origen_fk" x-model="itemToEdit.id_origen_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                        <option :value="null">Ninguno</option>
                        <template x-for="origen in catalogoOrigenes" :key="origen.id_origen_pk">
                            <option :value="origen.id_origen_pk" x-text="origen.nombre_origen"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="edit_cantidad" class="block text-sm font-medium text-gray-700 nunito-bold">Cantidad</label>
                    <input type="number" step="0.001" id="edit_cantidad" x-model="itemToEdit.cantidad" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div>
                    <label for="edit_fecha_movimiento" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Movimiento</label>
                    <input type="date" id="edit_fecha_movimiento" x-model="itemToEdit.fecha_movimiento" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                </div>
                <div class="md:col-span-2">
                    <label for="edit_motivo" class="block text-sm font-medium text-gray-700 nunito-bold">Motivo / Razón</label>
                    <textarea id="edit_motivo" x-model="itemToEdit.motivo" rows="3" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal modalName="isKardexDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este movimiento?" />
    </div>
</div>