<div x-data="{
    isKardexModalOpen: false,
    isKardexEditModalOpen: false,
    isKardexDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    kardex: [],
    loadingKardex: false,

    numbersKardex: [],
    currentPageKardex: 1,
    perPageKardex: 10,

    formKardex: { _touched: {} },
    formEditKardex: { _touched: {} },
    newMovimiento: {
        id_origen_fk: null,
        id_producto_fk: '',
        id_tipo_movimiento_fk: '',
        cantidad: '',
        fecha_movimiento: '',
        motivo: ''
    },

    catalogoProductos: [],
    catalogoTiposMovimiento: [],
    catalogoOrigenes: [],
    
    filtroKardex: '',
    ordenarPor: 'fecha_movimiento',
    ordenarDirection: 'desc',

    tipoColorPalette: [ 'bg-green-100 text-green-800', 'bg-red-100 text-red-800', 'bg-blue-100 text-blue-800', 'bg-yellow-100 text-yellow-800', 'bg-purple-100 text-purple-800', 'bg-teal-100 text-teal-800', 'bg-indigo-100 text-indigo-800', 'bg-pink-100 text-pink-800' ],
    getTipoColorClass(tipo) {
        if (!tipo) return 'bg-gray-100 text-gray-800';
        const key = tipo.id_tipo_movimiento_pk ?? tipo.id ?? tipo.nombre ?? tipo.nombre_tipo_movimiento ?? String(tipo);
        let h = 0;
        const s = String(key);
        for (let i = 0; i < s.length; i++) {
            h = ((h << 5) - h) + s.charCodeAt(i);
            h |= 0;
        }
        const idx = Math.abs(h) % this.tipoColorPalette.length;
        return this.tipoColorPalette[idx];
    },

    paginatedKardex() {
        return this.kardex.slice(
            (this.currentPageKardex - 1) * this.perPageKardex, 
            this.currentPageKardex * this.perPageKardex
        );
    },
    totalPagesKardex() {
        return Math.ceil(this.kardex.length / this.perPageKardex);
    },
    nextPageKardex() {
        if (this.currentPageKardex < this.totalPagesKardex()) {
            this.currentPageKardex++;
        }
    },
    prevPageKardex() {
        if (this.currentPageKardex > 1) {
            this.currentPageKardex--;
        }
    },

    async fetchKardex() {
        await window.kardexApiHandlers.fetchKardex(this);
        this.numbersKardex = this.kardex; // ← LÍNEA AGREGADA
    },
    async submitKardex() {
        await window.kardexApiHandlers.submitKardex(this);
        this.fetchKardex(); // Refrescar datos
    },
    async updateKardex() {
        await window.kardexApiHandlers.updateKardex(this);
        this.fetchKardex(); // Refrescar datos
    },
    async deleteKardex() {
        await window.kardexApiHandlers.deleteKardex(this);
        this.fetchKardex(); // Refrescar datos
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
}" x-init="fetchKardex(); fetchCatalogos();" x-effect="
    $watch('filtroKardex', () => { fetchKardex(); currentPageKardex = 1; });
    $watch('ordenarPor', () => { fetchKardex(); currentPageKardex = 1; });
    $watch('ordenarDirection', () => { fetchKardex(); currentPageKardex = 1; });
" @keydown.escape.window="
    isKardexModalOpen = false;
    isKardexEditModalOpen = false;
    isKardexDeleteModalOpen = false;
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Kardex de Inventario</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroKardex',
            'ordenarModel' => 'ordenarPor',
            'ordenarDirectionModel' => 'ordenarDirection',
            'ordenarOptions' => [
            'fecha_movimiento' => 'Fecha',
            'cantidad' => 'Cantidad'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Kardex','Inventario'], 'insercion')
            <button
                @click="formKardex = { _touched: {} }; newMovimiento = { id_origen_fk: null, id_producto_fk: '', id_tipo_movimiento_fk: '', cantidad: '', fecha_movimiento: '', motivo: '' }; isKardexModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Movimiento
            </button>
            @else
            <button disabled title="Sin permiso para crear"
                class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm cursor-not-allowed">
                Nuevo Movimiento
            </button>
            @endperm
            <a href="/admin/reportes-header?modulo=Kardex&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                <i class="fas fa-file-alt"></i> Generar Reporte
            </a>
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Producto</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Fecha</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Tipo Movimiento</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Cantidad</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Origen</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Motivo</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingKardex">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular"><i
                                    class="fas fa-spinner fa-spin mr-2"></i>Cargando movimientos...</td>
                        </tr>
                    </template>
                    <template x-if="!loadingKardex && kardex.length === 0">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">No hay movimientos
                                registrados</td>
                        </tr>
                    </template>
                    <template x-if="!loadingKardex && kardex.length > 0">
                        <template x-for="(movimiento, index) in paginatedKardex()" :key="movimiento.id_kardex_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedKardex().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-text="movimiento.producto ? movimiento.producto.nombre_producto : 'N/A'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-text="movimiento.fecha_movimiento"></td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded text-xs font-semibold"
                                        :class="getTipoColorClass(movimiento.tipo_movimiento)"
                                        x-text="movimiento.tipo_movimiento ? (movimiento.tipo_movimiento.nombre_tipo_movimiento || movimiento.tipo_movimiento.nombre) : 'N/A'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="movimiento.cantidad">
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200"
                                    x-text="movimiento.origen ? movimiento.origen.nombre_origen : 'N/A'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="movimiento.motivo"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    @perm(['Kardex','Inventario'], 'actualizacion')
                                    <a href="#"
                                        @click.prevent="formEditKardex = { _touched: {} }; isKardexEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(movimiento))"
                                        class="text-blue-500 hover:text-blue-700" title="Editar"><i
                                            class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar"><i
                                            class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Kardex','Inventario'], 'eliminacion')
                                    <a href="#"
                                        @click.prevent="isKardexDeleteModalOpen = true; itemToDelete = { id_kardex_pk: movimiento.id_kardex_pk, motivo: movimiento.motivo }"
                                        class="text-red-500 hover:text-red-700" title="Eliminar"><i
                                            class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-red-300 cursor-not-allowed" title="Sin permiso para eliminar"><i
                                            class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingKardex">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando movimientos...
                </div>
            </template>
            <template x-if="!loadingKardex && kardex.length === 0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay movimientos registrados</div>
            </template>
            <template x-if="!loadingKardex && kardex.length > 0">
                <template x-for="movimiento in paginatedKardex()" :key="movimiento.id_kardex_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                x-text="movimiento.producto ? movimiento.producto.nombre_producto : 'N/A'"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Fecha: ' + movimiento.fecha_movimiento"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular">
                            <span x-text="'Tipo: '"></span>
                            <span class="px-2 py-1 rounded text-xs font-semibold"
                                :class="getTipoColorClass(movimiento.tipo_movimiento)"
                                x-text="movimiento.tipo_movimiento ? (movimiento.tipo_movimiento.nombre_tipo_movimiento || movimiento.tipo_movimiento.nombre) : 'N/A'"></span>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Cantidad: ' + movimiento.cantidad"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Origen: ' + (movimiento.origen ? movimiento.origen.nombre_origen : 'N/A')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular"
                            x-text="'Motivo: ' + movimiento.motivo"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Kardex','Inventario'], 'actualizacion')
                            <button
                                @click.prevent="formEditKardex = { _touched: {} }; isKardexEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(movimiento))"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular"><i
                                    class="fas fa-edit"></i> Editar</button>
                            @else
                            <button disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-gray-400 text-white rounded cursor-not-allowed flex items-center gap-1 nunito-regular"><i
                                    class="fas fa-edit"></i> Editar</button>
                            @endperm
                            @perm(['Kardex','Inventario'], 'eliminacion')
                            <button
                                @click.prevent="isKardexDeleteModalOpen = true; itemToDelete = { id_kardex_pk: movimiento.id_kardex_pk, motivo: movimiento.motivo }"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular"><i
                                    class="fas fa-trash"></i> Eliminar</button>
                            @else
                            <button disabled title="Sin permiso para eliminar"
                                class="px-3 py-1 text-xs bg-red-300 text-white rounded cursor-not-allowed flex items-center gap-1 nunito-regular"><i
                                    class="fas fa-trash"></i> Eliminar</button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="kardex.length > perPageKardex"
        class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span
                class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="(currentPageKardex - 1) * perPageKardex + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="Math.min(currentPageKardex * perPageKardex, kardex.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="kardex.length"></strong>
                resultados
            </span>
        </div>
        <div
            class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageKardex()" :disabled="currentPageKardex === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>
            <div class="flex items-center gap-1">
                <template
                    x-for="page in Array.from({length: totalPagesKardex()}, (_, i) => i + 1).slice(Math.max(0, currentPageKardex - 3), currentPageKardex + 2)"
                    :key="page">
                    <button @click="currentPageKardex = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-blue-900 hover:text-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageKardex ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>
            <button @click="nextPageKardex()" :disabled="currentPageKardex === totalPagesKardex()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <div>
        <x-admin.form-modal modalName="isKardexModalOpen" title="Nuevo Movimiento" submitLabel="Guardar Movimiento"
            formId="formKardex" maxWidth="max-w-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="new_id_producto_fk"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Producto</label>
                    <select id="new_id_producto_fk" x-model="newMovimiento.id_producto_fk" required
                        @change="formKardex._touched.producto = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formKardex._touched && formKardex._touched.producto && !newMovimiento.id_producto_fk ? 'border-red-500' : ''">
                        <option value="">Seleccione un Producto...</option>
                        <template x-for="producto in catalogoProductos" :key="producto.id_producto_pk">
                            <option :value="producto.id_producto_pk" x-text="producto.nombre_producto"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formKardex._touched && formKardex._touched.producto && !newMovimiento.id_producto_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label for="new_id_tipo_movimiento_fk"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Movimiento</label>
                    <select id="new_id_tipo_movimiento_fk" x-model="newMovimiento.id_tipo_movimiento_fk" required
                        @change="formKardex._touched.tipo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formKardex._touched && formKardex._touched.tipo && !newMovimiento.id_tipo_movimiento_fk ? 'border-red-500' : ''">
                        <option value="">Seleccione un Tipo...</option>
                        <template x-for="tipo in catalogoTiposMovimiento" :key="tipo.id_tipo_movimiento_pk">
                            <option :value="tipo.id_tipo_movimiento_pk" x-text="tipo.nombre_tipo_movimiento"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formKardex._touched && formKardex._touched.tipo && !newMovimiento.id_tipo_movimiento_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label for="new_id_origen_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Origen
                        (Opcional)</label>
                    <select id="new_id_origen_fk" x-model="newMovimiento.id_origen_fk"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option :value="null">Ninguno</option>
                        <template x-for="origen in catalogoOrigenes" :key="origen.id_origen_pk">
                            <option :value="origen.id_origen_pk" x-text="origen.nombre_origen"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="new_cantidad"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Cantidad</label>
                    <input type="number" step="0.001" id="new_cantidad" x-model="newMovimiento.cantidad" required
                        @input="formKardex._touched.cantidad = true" @blur="formKardex._touched.cantidad = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formKardex._touched && formKardex._touched.cantidad && (!newMovimiento.cantidad || newMovimiento.cantidad <= 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formKardex._touched && formKardex._touched.cantidad && (!newMovimiento.cantidad || newMovimiento.cantidad <= 0) ? 'text-red-500' : ''">Requerido.
                        Debe ser mayor que 0.</small>
                </div>
                <div>
                    <label for="new_fecha_movimiento" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                        de Movimiento</label>
                    <input type="date" id="new_fecha_movimiento" x-model="newMovimiento.fecha_movimiento" required
                        @change="formKardex._touched.fecha = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formKardex._touched && formKardex._touched.fecha && !newMovimiento.fecha_movimiento ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formKardex._touched && formKardex._touched.fecha && !newMovimiento.fecha_movimiento ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label for="new_motivo" class="block text-sm font-medium text-gray-700 nunito-bold">Motivo /
                        Razón</label>
                    <textarea id="new_motivo" x-model="newMovimiento.motivo" maxlength="255" rows="3" required
                        @input="formKardex._touched.motivo = true" @blur="formKardex._touched.motivo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formKardex._touched && formKardex._touched.motivo && (newMovimiento.motivo === '' || (newMovimiento.motivo && newMovimiento.motivo.length >= 255)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formKardex._touched && formKardex._touched.motivo && (newMovimiento.motivo === '' || (newMovimiento.motivo && newMovimiento.motivo.length >= 255)) ? 'text-red-500' : ''">Requerido.
                        Máximo 255 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal modalName="isKardexEditModalOpen" title="Editar Movimiento" itemToEdit="itemToEdit"
            formId="formEditKardex" maxWidth="max-w-lg">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="edit_id_producto_fk"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Producto</label>
                        <select id="edit_id_producto_fk" x-model="itemToEdit.id_producto_fk" required
                            @change="formEditKardex._touched.producto = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditKardex._touched && formEditKardex._touched.producto && !itemToEdit.id_producto_fk ? 'border-red-500' : ''">
                            <option value="">Seleccione...</option>
                            <template x-for="producto in catalogoProductos" :key="producto.id_producto_pk">
                                <option :value="producto.id_producto_pk" x-text="producto.nombre_producto"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEditKardex._touched && formEditKardex._touched.producto && !itemToEdit.id_producto_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label for="edit_id_tipo_movimiento_fk"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Tipo de Movimiento</label>
                        <select id="edit_id_tipo_movimiento_fk" x-model="itemToEdit.id_tipo_movimiento_fk" required
                            @change="formEditKardex._touched.tipo = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditKardex._touched && formEditKardex._touched.tipo && !itemToEdit.id_tipo_movimiento_fk ? 'border-red-500' : ''">
                            <option value="">Seleccione...</option>
                            <template x-for="tipo in catalogoTiposMovimiento" :key="tipo.id_tipo_movimiento_pk">
                                <option :value="tipo.id_tipo_movimiento_pk" x-text="tipo.nombre_tipo_movimiento">
                                </option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEditKardex._touched && formEditKardex._touched.tipo && !itemToEdit.id_tipo_movimiento_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label for="edit_id_origen_fk"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Origen (Opcional)</label>
                        <select id="edit_id_origen_fk" x-model="itemToEdit.id_origen_fk"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                            <option :value="null">Ninguno</option>
                            <template x-for="origen in catalogoOrigenes" :key="origen.id_origen_pk">
                                <option :value="origen.id_origen_pk" x-text="origen.nombre_origen"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label for="edit_cantidad"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Cantidad</label>
                        <input type="number" step="0.001" id="edit_cantidad" x-model="itemToEdit.cantidad" required
                            @input="formEditKardex._touched.cantidad = true"
                            @blur="formEditKardex._touched.cantidad = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditKardex._touched && formEditKardex._touched.cantidad && (!itemToEdit.cantidad || itemToEdit.cantidad <= 0) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEditKardex._touched && formEditKardex._touched.cantidad && (!itemToEdit.cantidad || itemToEdit.cantidad <= 0) ? 'text-red-500' : ''">Requerido.
                            Debe ser mayor que 0.</small>
                    </div>
                    <div>
                        <label for="edit_fecha_movimiento"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Movimiento</label>
                        <input type="date" id="edit_fecha_movimiento" x-model="itemToEdit.fecha_movimiento" required
                            @change="formEditKardex._touched.fecha = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditKardex._touched && formEditKardex._touched.fecha && !itemToEdit.fecha_movimiento ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEditKardex._touched && formEditKardex._touched.fecha && !itemToEdit.fecha_movimiento ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_motivo" class="block text-sm font-medium text-gray-700 nunito-bold">Motivo /
                            Razón</label>
                        <textarea id="edit_motivo" x-model="itemToEdit.motivo" maxlength="255" rows="3" required
                            @input="formEditKardex._touched.motivo = true" @blur="formEditKardex._touched.motivo = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                            :class="formEditKardex._touched && formEditKardex._touched.motivo && (itemToEdit.motivo === '' || (itemToEdit.motivo && itemToEdit.motivo.length >= 255)) ? 'border-red-500' : ''"></textarea>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEditKardex._touched && formEditKardex._touched.motivo && (itemToEdit.motivo === '' || (itemToEdit.motivo && itemToEdit.motivo.length >= 255)) ? 'text-red-500' : ''">Requerido.
                            Máximo 255 caracteres.</small>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal modalName="isKardexDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este movimiento?" />
    </div>
</div>