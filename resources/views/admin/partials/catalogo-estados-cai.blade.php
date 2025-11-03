<div x-data="{
    isEstadoCaiModalOpen: false,
    isEditEstadoCaiModalOpen: false,
    isDeleteEstadoCaiModalOpen: false,
    itemToEdit: {
        id_estado_cai_pk: null,
        codigo_estado_cai: '',
        nombre_estado_cai: '',
        descripcion_estado_cai: '',
        es_final: false,
        orden: 0
    },
    itemToDelete: null,
    estadosCai: [],
    categorias: [],
    numbers: [],
    loadingEstadosCai: false,
    codigo_estado_cai: '',
    nombre_estado_cai: '',
    descripcion_estado_cai: '',
    es_final: false,
    orden: 0,
    formEstadoCai: { _touched: {} },
    formEditEstadoCai: { _touched: {} },
    filtroEstadoCai: '',
    ordenarPor: 'nombre_estado_cai',
    currentPage: 1,
    perPage: 10,
    paginatedEstadosCai() {
        return this.filteredEstadosCai.slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage);
    },
        totalPages() {
            return Math.max(1, Math.ceil((this.filteredEstadosCai || []).length / this.perPage));
        },
    nextPage() {
        if (this.currentPage < this.totalPages()) {
            this.currentPage++;
        }
    },
    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
        }
    },
    goToPage(page) {
        this.currentPage = page;
    },
    get filteredEstadosCai() {
        const term = String(this.filtroEstadoCai || '').toLowerCase().trim();
        let list = Array.from(this.estadosCai || []);
        if (term) {
            list = list.filter((ec) => {
                const codigo = String(ec?.codigo_estado_cai || '').toLowerCase();
                const nombre = String(ec?.nombre_estado_cai || '').toLowerCase();
                const desc = String(ec?.descripcion_estado_cai || '').toLowerCase();
                const orden = String(ec?.orden ?? '').toLowerCase();
                return (
                    codigo.includes(term) ||
                    nombre.includes(term) ||
                    desc.includes(term) ||
                    orden.includes(term)
                );
            });
        }
        const key = this.ordenarPor || 'nombre_estado_cai';
        const collator = new Intl.Collator('es', { sensitivity: 'base', numeric: true });
        const getVal = (ec) => {
            if (key === 'codigo_estado_cai') return String(ec?.codigo_estado_cai || '');
            if (key === 'orden') return Number(ec?.orden) || 0;
            return String(ec?.nombre_estado_cai || '');
        };
        list.sort((a, b) => {
            const va = getVal(a);
            const vb = getVal(b);
            if (key === 'orden') return (va - vb);
            return collator.compare(va, vb);
        });
        return list;
    },
    async fetchEstadosCai() {
        await window.estadosCaiApiHandlers.fetchEstadosCai(this);
        // synchronize aliases for reusable pagination components
        this.categorias = this.estadosCai;
        this.numbers = this.estadosCai;
    },
    async submitEstadoCai() {
        await window.estadosCaiApiHandlers.submitEstadoCai(this);
        this.categorias = this.estadosCai;
        this.numbers = this.estadosCai;
    },
    async updateEstadoCai() {
        await window.estadosCaiApiHandlers.updateEstadoCai(this);
        this.categorias = this.estadosCai;
        this.numbers = this.estadosCai;
    },
    async deleteEstadoCai() {
        await window.estadosCaiApiHandlers.deleteEstadoCai(this);
        this.categorias = this.estadosCai;
        this.numbers = this.estadosCai;
    },
    handleModalSubmit(event) {
        if(event.detail.formId === 'formEstadoCai') this.submitEstadoCai();
        if(event.detail.formId === 'formEditEstadoCai') this.updateEstadoCai();
    },
    handleDelete() {
        if (this.isDeleteEstadoCaiModalOpen) {
            this.deleteEstadoCai();
        }
    }
}" x-init="fetchEstadosCai()"
x-effect="
$watch('filtroEstadoCai', () => { currentPage = 1; });
$watch('ordenarPor', () => { currentPage = 1; });
"
@keydown.escape.window="
    isEstadoCaiModalOpen = false;
    isEditEstadoCaiModalOpen = false;
    isDeleteEstadoCaiModalOpen = false;
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Estados CAI</h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroEstadoCai',
            'ordenarOptions' => [
            'codigo_estado_cai' => 'Código',
            'nombre_estado_cai' => 'Nombre',
            'orden' => 'Orden'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            <button @click="formEstadoCai = { _touched: {} }; codigo_estado_cai=''; nombre_estado_cai=''; descripcion_estado_cai=''; es_final=false; orden=0; isEstadoCaiModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Estado CAI
            </button>
        </x-slot>

        <x-slot name="table">
            <table
                class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse table-white-dividers">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Código</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Nombre Estado CAI</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Descripción</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Orden</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Es Final</th>
                        <th
                            class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEstadosCai">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados CAI...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosCai && filteredEstadosCai.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay estados CAI registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosCai && filteredEstadosCai.length > 0">
                        <template x-for="(estadoCai, index) in paginatedEstadosCai()" :key="estadoCai.id_estado_cai_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedEstadosCai().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoCai.codigo_estado_cai || '-'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoCai.nombre_estado_cai"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoCai.descripcion_estado_cai || '-'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="estadoCai.orden || '0'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">
                                    <span x-show="estadoCai.es_final"
                                        class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 dark:ring-1 dark:ring-green-500/40">Sí</span>
                                    <span x-show="!estadoCai.es_final"
                                        class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 dark:ring-1 dark:ring-gray-500/40">No</span>
                                </td>
                                <td class="py-2 px-4 flex gap-2"
                                    :class="{ 'last:rounded-br-lg': index === paginatedEstadosCai().length - 1 }">
                                    <a href="#"
                                        @click.prevent="formEditEstadoCai = { _touched: {} }; isEditEstadoCaiModalOpen = true; itemToEdit = {id_estado_cai_pk: estadoCai.id_estado_cai_pk, codigo_estado_cai: estadoCai.codigo_estado_cai, nombre_estado_cai: estadoCai.nombre_estado_cai, descripcion_estado_cai: estadoCai.descripcion_estado_cai, es_final: estadoCai.es_final, orden: estadoCai.orden}"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#"
                                        @click.prevent="isDeleteEstadoCaiModalOpen = true; itemToDelete = {id_estado_cai_pk: estadoCai.id_estado_cai_pk, nombre_estado_cai: estadoCai.nombre_estado_cai}"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingEstadosCai">
                <div class="p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados CAI...
                </div>
            </template>
            <template x-if="!loadingEstadosCai && filteredEstadosCai.length === 0">
                <div class="p-8 text-center text-gray-500 nunito-regular">
                    No hay estados CAI registrados
                </div>
            </template>
            <template x-if="!loadingEstadosCai && filteredEstadosCai.length > 0">
                <template x-for="estadoCai in paginatedEstadosCai()" :key="estadoCai.id_estado_cai_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2 border border-black dark:border-gray-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold"
                                    x-text="estadoCai.nombre_estado_cai"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                    x-text="'Código: '+(estadoCai.codigo_estado_cai || '-')"></p>
                            </div>
                            <div>
                                <span class="px-2 py-1 rounded text-xs"
                                    :class="estadoCai.es_final ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 dark:ring-1 dark:ring-green-500/40' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 dark:ring-1 dark:ring-gray-500/40'"
                                    x-text="estadoCai.es_final ? 'Final' : 'No final'"></span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 grid grid-cols-2 gap-2">
                            <div><span class="nunito-bold text-gray-600 dark:text-gray-300">Orden:</span> <span
                                    x-text="estadoCai.orden || '0'"></span></div>
                            <div class="col-span-2"><span
                                    class="nunito-bold text-gray-600 dark:text-gray-300">Descripción:</span> <span
                                    x-text="estadoCai.descripcion_estado_cai || '-' "></span></div>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button
                                @click.prevent="formEditEstadoCai = { _touched: {} }; isEditEstadoCaiModalOpen = true; itemToEdit = {id_estado_cai_pk: estadoCai.id_estado_cai_pk, codigo_estado_cai: estadoCai.codigo_estado_cai, nombre_estado_cai: estadoCai.nombre_estado_cai, descripcion_estado_cai: estadoCai.descripcion_estado_cai, es_final: estadoCai.es_final, orden: estadoCai.orden}"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button
                                @click.prevent="isDeleteEstadoCaiModalOpen = true; itemToDelete = {id_estado_cai_pk: estadoCai.id_estado_cai_pk, nombre_estado_cai: estadoCai.nombre_estado_cai}"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Paginación del lado del cliente -->
    <x-pagination />

    <!-- Modales -->
    <div>
        <!-- Modal Nuevo Estado CAI -->
        <x-admin.form-modal class="nunito-bold" modalName="isEstadoCaiModalOpen" title="Nuevo Estado CAI"
            submitLabel="Guardar Estado" formId="formEstadoCai" maxWidth="max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="codigo_estado_cai"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="codigo_estado_cai" x-model="codigo_estado_cai" maxlength="10" @input="formEstadoCai._touched.codigo = true" @blur="formEstadoCai._touched.codigo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCai._touched && formEstadoCai._touched.codigo && (codigo_estado_cai === '' || (codigo_estado_cai && codigo_estado_cai.length >= 10)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCai._touched && formEstadoCai._touched.codigo && (codigo_estado_cai === '' || (codigo_estado_cai && codigo_estado_cai.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                </div>
                <div>
                    <label for="nombre_estado_cai" class="block text-sm font-medium text-gray-700 nunito-bold">Nombre
                        Estado CAI</label>
                    <input type="text" id="nombre_estado_cai" x-model="nombre_estado_cai" maxlength="150" required @input="formEstadoCai._touched.nombre = true" @blur="formEstadoCai._touched.nombre = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCai._touched && formEstadoCai._touched.nombre && !nombre_estado_cai ? 'border-red-500' : (formEstadoCai._touched && formEstadoCai._touched.nombre && (nombre_estado_cai && nombre_estado_cai.length >= 150) ? 'border-red-500' : '')">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCai._touched && formEstadoCai._touched.nombre && !nombre_estado_cai ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion_estado_cai"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="descripcion_estado_cai" x-model="descripcion_estado_cai" maxlength="500" rows="2" @input="formEstadoCai._touched.descripcion = true" @blur="formEstadoCai._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCai._touched && formEstadoCai._touched.descripcion && (descripcion_estado_cai === '' || (descripcion_estado_cai && descripcion_estado_cai.length >= 500)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCai._touched && formEstadoCai._touched.descripcion && (descripcion_estado_cai === '' || (descripcion_estado_cai && descripcion_estado_cai.length >= 500)) ? 'text-red-500' : ''">Máximo 500 caracteres.</small>
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="orden" x-model="orden" min="0" @input="formEstadoCai._touched.orden = true" @blur="formEstadoCai._touched.orden = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEstadoCai._touched && formEstadoCai._touched.orden && (orden === '' || orden < 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoCai._touched && formEstadoCai._touched.orden && (orden === '' || orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 ">
                    <label for="es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">¿Es estado
                        final?</label>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Estado CAI -->
        <x-admin.edit-modal class="nunito-bold" modalName="isEditEstadoCaiModalOpen" title="Editar Estado CAI"
            itemToEdit="itemToEdit" maxWidth="max-w-4xl" formId="formEditEstadoCai">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_codigo_estado_cai"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Código</label>
                    <input type="text" id="edit_codigo_estado_cai" x-model="itemToEdit.codigo_estado_cai" maxlength="10" @input="formEditEstadoCai._touched.codigo = true" @blur="formEditEstadoCai._touched.codigo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCai._touched && formEditEstadoCai._touched.codigo && (itemToEdit.codigo_estado_cai === '' || (itemToEdit.codigo_estado_cai && itemToEdit.codigo_estado_cai.length >= 10)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCai._touched && formEditEstadoCai._touched.codigo && (itemToEdit.codigo_estado_cai === '' || (itemToEdit.codigo_estado_cai && itemToEdit.codigo_estado_cai.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                </div>
                <div>
                    <label for="edit_nombre_estado_cai"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Nombre Estado CAI</label>
                    <input type="text" id="edit_nombre_estado_cai" x-model="itemToEdit.nombre_estado_cai" maxlength="150" required @input="formEditEstadoCai._touched.nombre = true" @blur="formEditEstadoCai._touched.nombre = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCai._touched && formEditEstadoCai._touched.nombre && !itemToEdit.nombre_estado_cai ? 'border-red-500' : (formEditEstadoCai._touched && formEditEstadoCai._touched.nombre && (itemToEdit.nombre_estado_cai && itemToEdit.nombre_estado_cai.length >= 150) ? 'border-red-500' : '')">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCai._touched && formEditEstadoCai._touched.nombre && !itemToEdit.nombre_estado_cai ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="edit_descripcion_estado_cai"
                        class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea id="edit_descripcion_estado_cai" x-model="itemToEdit.descripcion_estado_cai" maxlength="500" rows="2" @input="formEditEstadoCai._touched.descripcion = true" @blur="formEditEstadoCai._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCai._touched && formEditEstadoCai._touched.descripcion && (itemToEdit.descripcion_estado_cai === '' || (itemToEdit.descripcion_estado_cai && itemToEdit.descripcion_estado_cai.length >= 500)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCai._touched && formEditEstadoCai._touched.descripcion && (itemToEdit.descripcion_estado_cai === '' || (itemToEdit.descripcion_estado_cai && itemToEdit.descripcion_estado_cai.length >= 500)) ? 'text-red-500' : ''">Máximo 500 caracteres.</small>
                </div>
                <div>
                    <label for="edit_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Orden</label>
                    <input type="number" id="edit_orden" x-model="itemToEdit.orden" min="0" @input="formEditEstadoCai._touched.orden = true" @blur="formEditEstadoCai._touched.orden = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                        :class="formEditEstadoCai._touched && formEditEstadoCai._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoCai._touched && formEditEstadoCai._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 ">
                    <label for="edit_es_final" class="ml-2 block text-sm font-medium text-gray-700 nunito-bold">¿Es
                        estado final?</label>
                </div>
            </div>
        </x-admin.edit-modal>

        <!-- Modal Confirmar Eliminación -->
        <x-admin.confirmation-modal class="nunito-regular" modalName="isDeleteEstadoCaiModalOpen"
            itemToDelete="itemToDelete" message="¿Estás seguro de que quieres eliminar este estado CAI?" />
    </div>
</div>