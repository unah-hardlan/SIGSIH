<div x-data="{
    isEstadoSolicitudModalOpen: false,
    isEstadoSolicitudEditModalOpen: false,
    isEstadoSolicitudDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosSolicitud: [],
    loadingEstadosSolicitud: false,

    numbersEstadosSolicitud: [],
    currentPageEstadosSolicitud: 1,
    perPageEstadosSolicitud: 10,

    codigo: '',
    nombre: '',
    descripcion: '',
    es_final: false,
    orden: '',
    formEstadoSolicitud: { _touched: {} },
    formEditEstadoSolicitud: { _touched: {} },
    // Filtros
    filtroEstadoSolicitud: '',
    ordenarPor: 'nombre',
    ordenarDireccion: 'asc',
    get filteredEstadosSolicitud() {
        const term = String(this.filtroEstadoSolicitud || '').toLowerCase().trim();
        const sortKey = this.ordenarPor || 'nombre';
        const dir = this.ordenarDireccion === 'desc' ? -1 : 1;

        let items = Array.isArray(this.estadosSolicitud) ? [...this.estadosSolicitud] : [];

        if (term) {
            items = items.filter((e) => {
                const parts = [
                    e?.nombre,
                    e?.codigo,
                    e?.descripcion,
                    String(e?.orden),
                    e?.es_final ? 'si' : 'no',
                ].map((v) => String(v ?? '').toLowerCase());
                return parts.some((p) => p.includes(term));
            });
        }

        items.sort((a, b) => {
            let va = a?.[sortKey];
            let vb = b?.[sortKey];

            if (['nombre', 'codigo', 'descripcion'].includes(sortKey)) {
                va = String(va ?? '').toLowerCase();
                vb = String(vb ?? '').toLowerCase();
            } else if (['orden', 'id_estado_solicitud_pk'].includes(sortKey)) {
                va = Number(va ?? 0);
                vb = Number(vb ?? 0);
            } else if (sortKey === 'es_final') {
                va = !!va;
                vb = !!vb;
            }

            if (va < vb) return -1 * dir;
            if (va > vb) return 1 * dir;
            const sa = String(a?.nombre ?? '').toLowerCase();
            const sb = String(b?.nombre ?? '').toLowerCase();
            if (sa < sb) return -1 * dir;
            if (sa > sb) return 1 * dir;
            return 0;
        });
        return items;
    },

    paginatedEstadosSolicitud() {
        return this.filteredEstadosSolicitud.slice(
            (this.currentPageEstadosSolicitud - 1) * this.perPageEstadosSolicitud,
            this.currentPageEstadosSolicitud * this.perPageEstadosSolicitud
        );
    },
    totalPagesEstadosSolicitud() {
        return Math.max(1, Math.ceil((this.filteredEstadosSolicitud || []).length / this.perPageEstadosSolicitud));
    },
    nextPageEstadosSolicitud() {
        if (this.currentPageEstadosSolicitud < this.totalPagesEstadosSolicitud()) {
            this.currentPageEstadosSolicitud++;
        }
    },
    prevPageEstadosSolicitud() {
        if (this.currentPageEstadosSolicitud > 1) {
            this.currentPageEstadosSolicitud--;
        }
    },

    async fetchEstadosSolicitud() {
        await window.estadosSolicitudApiHandlers.fetchEstadosSolicitud(this);
        this.numbersEstadosSolicitud = this.estadosSolicitud; 
    },
    async submitEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.submitEstadoSolicitud(this);
        this.fetchEstadosSolicitud();
    },
    async updateEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.updateEstadoSolicitud(this);
        this.fetchEstadosSolicitud(); 
    },
    async deleteEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.deleteEstadoSolicitud(this);
        this.fetchEstadosSolicitud();
    },
    // Manejadores de eventos de los modales
    handleModalSubmit(event) {
        if (event.detail.formId === 'formEstadoSolicitud') this.submitEstadoSolicitud();
        if (event.detail.formId === 'formEditEstadoSolicitud') this.updateEstadoSolicitud();
    },
    handleDelete() {
        if (this.isEstadoSolicitudDeleteModalOpen) {
            this.deleteEstadoSolicitud();
        }
    }
}"
    x-init="fetchEstadosSolicitud()"
    x-effect="
    $watch('filtroEstadoSolicitud', () => currentPageEstadosSolicitud = 1);
    $watch('ordenarPor', () => currentPageEstadosSolicitud = 1);
    $watch('ordenarDireccion', () => currentPageEstadosSolicitud = 1);
"
    @keydown.escape.window="
    isEstadoSolicitudModalOpen = false;
    isEstadoSolicitudEditModalOpen = false;
    isEstadoSolicitudDeleteModalOpen = false;
" @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Catálogo de Estados de Solicitud
        </h1>
    </div>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'filtroEstadoSolicitud',
            'ordenarOptions' => [
            'nombre' => 'Nombre',
            'codigo' => 'Código',
            'es_final' => 'Es Final',
            'orden' => 'Orden'
            ]
            ])
        </x-slot>

        <x-slot name="actions">
            @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'insercion')
            <button @click="formEstadoSolicitud = { _touched: {} }; codigo=''; nombre=''; descripcion=''; es_final=false; orden=''; isEstadoSolicitudModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Estado
            </button>
            @else
            <button disabled title="No tiene permiso para crear Estados de Solicitud"
                class="bg-green-600 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm opacity-50 cursor-not-allowed">
                Nuevo Estado
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Nombre</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Código</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Descripción</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Es Final</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Orden</th>
                        <th class="py-2 px-4 text-left border-0 dark:text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingEstadosSolicitud">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando estados...
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosSolicitud && estadosSolicitud.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 nunito-regular">
                                No hay estados de solicitud registrados
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loadingEstadosSolicitud && filteredEstadosSolicitud.length > 0">
                        <template x-for="(estado, index) in paginatedEstadosSolicitud()"
                            :key="estado.id_estado_solicitud_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.nombre"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.codigo"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.descripcion"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200">
                                    <span x-text="estado.es_final ? 'Sí' : 'No'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="estado.orden"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'actualizacion')
                                    <a href="#"
                                        @click.prevent="formEditEstadoSolicitud = { _touched: {} }; isEstadoSolicitudEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(estado))"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-blue-300 cursor-not-allowed" title="No tiene permiso para editar Estados de Solicitud"><i class="fas fa-edit"></i></span>
                                    @endperm

                                    @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'eliminacion')
                                    <a href="#"
                                        @click.prevent="isEstadoSolicitudDeleteModalOpen = true; itemToDelete = { id_estado_solicitud_pk: estado.id_estado_solicitud_pk, nombre: estado.nombre }"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    @else
                                    <span class="text-red-300 cursor-not-allowed" title="No tiene permiso para eliminar Estados de Solicitud"><i class="fas fa-trash"></i></span>
                                    @endperm
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingEstadosSolicitud">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                </div>
            </template>
            <template x-if="!loadingEstadosSolicitud && estadosSolicitud.length === 0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border p-8 text-center text-gray-500 nunito-regular">
                    No hay estados registrados
                </div>
            </template>
            <template x-if="!loadingEstadosSolicitud && filteredEstadosSolicitud.length > 0">
                <template x-for="estado in paginatedEstadosSolicitud()" :key="estado.id_estado_solicitud_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border p-4 space-y-2 border-black dark:border-gray-800">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estado.nombre">
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Código: ' + estado.codigo"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="estado.descripcion"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400"
                            x-text="'Es Final: ' + (estado.es_final ? 'Sí' : 'No')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Orden: ' + estado.orden"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'actualizacion')
                            <button
                                @click.prevent="formEditEstadoSolicitud = { _touched: {} }; isEstadoSolicitudEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(estado))"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Editar</button>
                            @else
                            <button disabled title="No tiene permiso para editar Estados de Solicitud"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded opacity-50 cursor-not-allowed">Editar</button>
                            @endperm
                            @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'eliminacion')
                            <button
                                @click.prevent="isEstadoSolicitudDeleteModalOpen = true; itemToDelete = { id_estado_solicitud_pk: estado.id_estado_solicitud_pk, nombre: estado.nombre }"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                            @else
                            <button disabled title="No tiene permiso para eliminar Estados de Solicitud"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded opacity-50 cursor-not-allowed">Eliminar</button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="filteredEstadosSolicitud.length > perPageEstadosSolicitud" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageEstadosSolicitud - 1) * perPageEstadosSolicitud + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageEstadosSolicitud * perPageEstadosSolicitud, filteredEstadosSolicitud.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="filteredEstadosSolicitud.length"></strong>
                resultados
            </span>
        </div>

        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageEstadosSolicitud()" :disabled="currentPageEstadosSolicitud === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template x-for="page in Array.from({length: totalPagesEstadosSolicitud()}, (_, i) => i + 1).slice(Math.max(0, currentPageEstadosSolicitud - 3), currentPageEstadosSolicitud + 2)" :key="page">
                    <button @click="currentPageEstadosSolicitud = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageEstadosSolicitud ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageEstadosSolicitud()" :disabled="currentPageEstadosSolicitud === totalPagesEstadosSolicitud()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>


    <div>
        @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'insercion')
        <x-admin.form-modal modalName="isEstadoSolicitudModalOpen" title="Nuevo Estado de Solicitud"
            submitLabel="Guardar" formId="formEstadoSolicitud" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" maxlength="150" required @input="formEstadoSolicitud._touched.nombre = true" @blur="formEstadoSolicitud._touched.nombre = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                        :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.nombre && !nombre ? 'border-red-500' : (formEstadoSolicitud._touched && formEstadoSolicitud._touched.nombre && (nombre && nombre.length >= 150) ? 'border-red-500' : '')">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.nombre && !nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                </div>
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                    <input type="text" id="codigo" x-model="codigo" maxlength="10" required @input="formEstadoSolicitud._touched.codigo = true" @blur="formEstadoSolicitud._touched.codigo = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                        :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.codigo && (codigo === '' || (codigo && codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                </div>
                <div class="col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" maxlength="255" rows="2" @input="formEstadoSolicitud._touched.descripcion = true" @blur="formEstadoSolicitud._touched.descripcion = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                        :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.descripcion && (descripcion === '' || (descripcion && descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700">Orden</label>
                    <input type="number" id="orden" x-model="orden" required min="0" @input="formEstadoSolicitud._touched.orden = true" @blur="formEstadoSolicitud._touched.orden = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                        :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.orden && (orden === '' || orden < 0) ? 'border-red-500' : ''">
                    <small class="block mt-1 text-sm text-gray-500" :class="formEstadoSolicitud._touched && formEstadoSolicitud._touched.orden && (orden === '' || orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="rounded border-gray-500 text-blue-600 shadow-sm">
                    <label for="es_final" class="ml-2 block text-sm font-medium text-gray-700">Es Final</label>
                </div>
            </div>
        </x-admin.form-modal>
        @endperm

        @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'actualizacion')
        <x-admin.edit-modal modalName="isEstadoSolicitudEditModalOpen" title="Editar Estado de Solicitud"
            itemToEdit="itemToEdit" formId="formEditEstadoSolicitud" maxWidth="max-w-2xl">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" maxlength="150" required @input="formEditEstadoSolicitud._touched.nombre = true" @blur="formEditEstadoSolicitud._touched.nombre = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                            :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.nombre && !itemToEdit.nombre ? 'border-red-500' : (formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.nombre && (itemToEdit.nombre && itemToEdit.nombre.length >= 150) ? 'border-red-500' : '')">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.nombre && !itemToEdit.nombre ? 'text-red-500' : ''">Requerido. Máximo 150 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_codigo" class="block text-sm font-medium text-gray-700">Código</label>
                        <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" maxlength="10" required @input="formEditEstadoSolicitud._touched.codigo = true" @blur="formEditEstadoSolicitud._touched.codigo = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                            :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.codigo && (itemToEdit.codigo === '' || (itemToEdit.codigo && itemToEdit.codigo.length >= 10)) ? 'text-red-500' : ''">Requerido. Máximo 10 caracteres.</small>
                    </div>
                    <div class="col-span-2">
                        <label for="edit_descripcion"
                            class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" maxlength="255" rows="2" @input="formEditEstadoSolicitud._touched.descripcion = true" @blur="formEditEstadoSolicitud._touched.descripcion = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                            :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'border-red-500' : ''"></textarea>
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.descripcion && (itemToEdit.descripcion === '' || (itemToEdit.descripcion && itemToEdit.descripcion.length >= 255)) ? 'text-red-500' : ''">Máximo 255 caracteres.</small>
                    </div>
                    <div>
                        <label for="edit_orden" class="block text-sm font-medium text-gray-700">Orden</label>
                        <input type="number" id="edit_orden" x-model="itemToEdit.orden" required min="0" @input="formEditEstadoSolicitud._touched.orden = true" @blur="formEditEstadoSolicitud._touched.orden = true"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"
                            :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'border-red-500' : ''">
                        <small class="block mt-1 text-sm text-gray-500" :class="formEditEstadoSolicitud._touched && formEditEstadoSolicitud._touched.orden && (itemToEdit.orden === '' || itemToEdit.orden < 0) ? 'text-red-500' : ''">Requerido. Valor >= 0.</small>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                            class="rounded border-gray-500 text-blue-600 shadow-sm">
                        <label for="edit_es_final" class="ml-2 block text-sm font-medium text-gray-700">Es Final</label>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>
        @endperm

        @perm(['Catálogo','Estados de Solicitud','Estado de Solicitud'], 'eliminacion')
        <x-admin.confirmation-modal modalName="isEstadoSolicitudDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este estado?" />
        @endperm
    </div>
</div>