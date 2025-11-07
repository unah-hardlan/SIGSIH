<div x-data="{
    isTipoMovimientoModalOpen: false,
    isTipoMovimientoEditModalOpen: false,
    isTipoMovimientoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    tipoMovimientos: [],
    loadingTipoMovimientos: false,
    
    // 1️⃣ Variables de Paginación
    numbersTipoMovimientos: [],
    currentPageTipoMovimientos: 1,
    perPageTipoMovimientos: 10,

    nombre_tipo_movimiento: '',
    descripcion_tipo_movimiento: '',
    filtroTipoMovimiento: '',
    ordenarPor: 'nombre',

    // 2️⃣ Métodos de Paginación
    paginatedTipoMovimientos() {
        return this.tipoMovimientos.slice(
            (this.currentPageTipoMovimientos - 1) * this.perPageTipoMovimientos, 
            this.currentPageTipoMovimientos * this.perPageTipoMovimientos
        );
    },
    totalPagesTipoMovimientos() {
        return Math.ceil(this.tipoMovimientos.length / this.perPageTipoMovimientos);
    },
    nextPageTipoMovimientos() {
        if (this.currentPageTipoMovimientos < this.totalPagesTipoMovimientos()) {
            this.currentPageTipoMovimientos++;
        }
    },
    prevPageTipoMovimientos() {
        if (this.currentPageTipoMovimientos > 1) {
            this.currentPageTipoMovimientos--;
        }
    },

    // 3️⃣ Sincronizar Alias en cada operación CRUD
    async fetchTipoMovimientos() {
        await window.tipoMovimientosApiHandlers.fetchTipoMovimientos(this);
        this.numbersTipoMovimientos = this.tipoMovimientos; // ← LÍNEA AGREGADA
    },
    async submitTipoMovimiento() {
        await window.tipoMovimientosApiHandlers.submitTipoMovimiento(this);
        this.fetchTipoMovimientos(); // Refrescar datos
    },
    async updateTipoMovimiento() {
        await window.tipoMovimientosApiHandlers.updateTipoMovimiento(this);
        this.fetchTipoMovimientos(); // Refrescar datos
    },
    async deleteTipoMovimiento() {
        await window.tipoMovimientosApiHandlers.deleteTipoMovimiento(this);
        this.fetchTipoMovimientos(); // Refrescar datos
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formTipoMovimiento') this.submitTipoMovimiento();
        if(event.detail.formId === 'formEditTipoMovimiento') this.updateTipoMovimiento();
    },
    handleDelete() {
        if (this.isTipoMovimientoDeleteModalOpen) {
            this.deleteTipoMovimiento();
        }
    }
}"
    x-init="fetchTipoMovimientos()"
    x-effect="
    // 4️⃣ Reset de página en filtros
    $watch('filtroTipoMovimiento', () => { fetchTipoMovimientos(); currentPageTipoMovimientos = 1; });
    $watch('ordenarPor', () => { fetchTipoMovimientos(); currentPageTipoMovimientos = 1; });
"
    @keydown.escape.window="
    isTipoMovimientoModalOpen = false;
    isTipoMovimientoEditModalOpen = false;
    isTipoMovimientoDeleteModalOpen = false;
"
    @modal-submit.window="handleModalSubmit($event)"
    @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Tipos de Movimiento</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroTipoMovimiento',
            'ordenarModel' => 'ordenarPor',
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'id' => 'ID Tipo'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'insercion')
            <button
                @click="formTipoMovimiento = { _touched: {} }; nombre_tipo_movimiento = ''; descripcion_tipo_movimiento = ''; isTipoMovimientoModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo tipo de movimiento
            </button>
            @else
            <button type="button" disabled title="Sin permiso para crear"
                class="bg-green-600/60 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm opacity-60 cursor-not-allowed">
                Nuevo tipo de movimiento
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
                    <template x-if="loadingTipoMovimientos">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de movimiento...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoMovimientos && tipoMovimientos.length === 0">
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay tipos de movimiento registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingTipoMovimientos && tipoMovimientos.length > 0">
                        <!-- 5️⃣ Usar paginatedTipoMovimientos() en el template -->
                        <template x-for="(tipoMovimiento, index) in paginatedTipoMovimientos()" :key="tipoMovimiento.id_tipo_movimiento_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedTipoMovimientos().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoMovimiento.nombre_tipo_movimiento"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="tipoMovimiento.descripcion_tipo_movimiento"></td>
                                <td class="py-2 px-4 flex gap-2" :class="{ 'last:rounded-br-lg': index === paginatedTipoMovimientos().length - 1 }">
                                    @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'actualizacion')
                                    <a href="#" @click.prevent="formEditTipoMovimiento = { _touched: {} }; isTipoMovimientoEditModalOpen = true; itemToEdit = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre_tipo_movimiento: tipoMovimiento.nombre_tipo_movimiento, descripcion_tipo_movimiento: tipoMovimiento.descripcion_tipo_movimiento}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span title="Sin permiso para editar" class="text-blue-300 cursor-not-allowed"><i class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'eliminacion')
                                    <a href="#" @click.prevent="isTipoMovimientoDeleteModalOpen = true; itemToDelete = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre: tipoMovimiento.nombre_tipo_movimiento}" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
            <template x-if="loadingTipoMovimientos">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando tipos de movimiento...
                </div>
            </template>
            <template x-if="!loadingTipoMovimientos && tipoMovimientos.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                    No hay tipos de movimiento registrados
                </div>
            </template>
            <template x-if="!loadingTipoMovimientos && tipoMovimientos.length > 0">
                <template x-for="tipoMovimiento in paginatedTipoMovimientos()" :key="tipoMovimiento.id_tipo_movimiento_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="tipoMovimiento.nombre_tipo_movimiento"></h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="tipoMovimiento.descripcion_tipo_movimiento"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'actualizacion')
                            <button @click.prevent="formEditTipoMovimiento = { _touched: {} }; isTipoMovimientoEditModalOpen = true; itemToEdit = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre_tipo_movimiento: tipoMovimiento.nombre_tipo_movimiento, descripcion_tipo_movimiento: tipoMovimiento.descripcion_tipo_movimiento}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar" class="px-3 py-1 text-xs bg-blue-600/60 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'eliminacion')
                            <button @click.prevent="isTipoMovimientoDeleteModalOpen = true; itemToDelete = {id_tipo_movimiento_pk: tipoMovimiento.id_tipo_movimiento_pk, nombre: tipoMovimiento.nombre_tipo_movimiento}" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
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

    <!-- 6️⃣ Componente de Paginación -->
    <div x-show="tipoMovimientos.length > perPageTipoMovimientos" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <!-- Mostrando (centered, supports light/dark) -->
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageTipoMovimientos - 1) * perPageTipoMovimientos + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageTipoMovimientos * perPageTipoMovimientos, tipoMovimientos.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="tipoMovimientos.length"></strong>
                resultados
            </span>
        </div>

        <!-- Controls (light/dark) -->
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageTipoMovimientos()" :disabled="currentPageTipoMovimientos === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesTipoMovimientos()}, (_, i) => i + 1).slice(Math.max(0, currentPageTipoMovimientos - 3), currentPageTipoMovimientos + 2)" :key="page">
                    <button @click="currentPageTipoMovimientos = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageTipoMovimientos ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageTipoMovimientos()" :disabled="currentPageTipoMovimientos === totalPagesTipoMovimientos()"
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
        <!-- Modal Nuevo Tipo de Movimiento -->
        @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'insercion')
        <x-admin.form-modal class="nunito-bold" modalName="isTipoMovimientoModalOpen" title="Nuevo Tipo de Movimiento"
            submitLabel="Guardar Tipo de Movimiento" formId="formTipoMovimiento" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre_tipo_movimiento" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" id="nombre_tipo_movimiento" x-model="nombre_tipo_movimiento" required maxlength="150"
                        @input="formTipoMovimiento = formTipoMovimiento || { _touched: {} }; formTipoMovimiento._touched.nombre_tipo_movimiento = true"
                        @blur="formTipoMovimiento = formTipoMovimiento || { _touched: {} }; formTipoMovimiento._touched.nombre_tipo_movimiento = true"
                        :class="formTipoMovimiento && formTipoMovimiento._touched && formTipoMovimiento._touched.nombre_tipo_movimiento && (nombre_tipo_movimiento === '' || (nombre_tipo_movimiento && nombre_tipo_movimiento.length > 150)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <small class="block text-xs nunito-regular mt-1" :class="formTipoMovimiento && formTipoMovimiento._touched && formTipoMovimiento._touched.nombre_tipo_movimiento && (nombre_tipo_movimiento === '' || (nombre_tipo_movimiento && nombre_tipo_movimiento.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion_tipo_movimiento"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_tipo_movimiento" x-model="descripcion_tipo_movimiento" rows="2" maxlength="255"
                        @input="formTipoMovimiento = formTipoMovimiento || { _touched: {} }; formTipoMovimiento._touched.descripcion_tipo_movimiento = true"
                        @blur="formTipoMovimiento = formTipoMovimiento || { _touched: {} }; formTipoMovimiento._touched.descripcion_tipo_movimiento = true"
                        :class="formTipoMovimiento && formTipoMovimiento._touched && formTipoMovimiento._touched.descripcion_tipo_movimiento && (descripcion_tipo_movimiento === '' || (descripcion_tipo_movimiento && descripcion_tipo_movimiento.length > 255)) ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                    <small class="block text-xs nunito-regular mt-1" :class="formTipoMovimiento && formTipoMovimiento._touched && formTipoMovimiento._touched.descripcion_tipo_movimiento && (descripcion_tipo_movimiento === '' || (descripcion_tipo_movimiento && descripcion_tipo_movimiento.length > 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        <!-- Modal Editar Tipo de Movimiento -->
        @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'actualizacion')
        <x-admin.edit-modal class="nunito-bold" modalName="isTipoMovimientoEditModalOpen" title="Editar Tipo de Movimiento" itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditTipoMovimiento">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_nombre_tipo_movimiento" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                        <input type="text" id="edit_nombre_tipo_movimiento" x-model="itemToEdit.nombre_tipo_movimiento" required maxlength="150"
                            @input="formEditTipoMovimiento = formEditTipoMovimiento || { _touched: {} }; formEditTipoMovimiento._touched.nombre_tipo_movimiento = true"
                            @blur="formEditTipoMovimiento = formEditTipoMovimiento || { _touched: {} }; formEditTipoMovimiento._touched.nombre_tipo_movimiento = true"
                            :class="formEditTipoMovimiento && formEditTipoMovimiento._touched && formEditTipoMovimiento._touched.nombre_tipo_movimiento && (itemToEdit.nombre_tipo_movimiento === '' || (itemToEdit.nombre_tipo_movimiento && itemToEdit.nombre_tipo_movimiento.length > 150)) ? 'border-red-500' : ''"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <small class="block text-xs nunito-regular mt-1" :class="formEditTipoMovimiento && formEditTipoMovimiento._touched && formEditTipoMovimiento._touched.nombre_tipo_movimiento && (itemToEdit.nombre_tipo_movimiento === '' || (itemToEdit.nombre_tipo_movimiento && itemToEdit.nombre_tipo_movimiento.length > 150)) ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                    </div>
                    <div class="col-span-2">
                        <label for="edit_descripcion_tipo_movimiento"
                            class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                        <textarea id="edit_descripcion_tipo_movimiento" x-model="itemToEdit.descripcion_tipo_movimiento" rows="2" maxlength="255"
                            @input="formEditTipoMovimiento = formEditTipoMovimiento || { _touched: {} }; formEditTipoMovimiento._touched.descripcion_tipo_movimiento = true"
                            @blur="formEditTipoMovimiento = formEditTipoMovimiento || { _touched: {} }; formEditTipoMovimiento._touched.descripcion_tipo_movimiento = true"
                            :class="formEditTipoMovimiento && formEditTipoMovimiento._touched && formEditTipoMovimiento._touched.descripcion_tipo_movimiento && (itemToEdit.descripcion_tipo_movimiento === '' || (itemToEdit.descripcion_tipo_movimiento && itemToEdit.descripcion_tipo_movimiento.length > 255)) ? 'border-red-500' : ''"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                        <small class="block text-xs nunito-regular mt-1" :class="formEditTipoMovimiento && formEditTipoMovimiento._touched && formEditTipoMovimiento._touched.descripcion_tipo_movimiento && (itemToEdit.descripcion_tipo_movimiento === '' || (itemToEdit.descripcion_tipo_movimiento && itemToEdit.descripcion_tipo_movimiento.length > 255)) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>
        @endperm

        <!-- Modal Confirmar Eliminación -->
        @perm(['Catálogo','Tipos de Movimiento','Tipo de Movimiento'], 'eliminacion')
        <x-admin.confirmation-modal class="nunito-regular" modalName="isTipoMovimientoDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este tipo de movimiento?" />
        @endperm
    </div>
</div>