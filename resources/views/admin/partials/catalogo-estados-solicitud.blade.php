<div x-data="{
    isEstadoSolicitudModalOpen: false,
    isEstadoSolicitudEditModalOpen: false,
    isEstadoSolicitudDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    estadosSolicitud: [],
    loadingEstadosSolicitud: false,

    // 1️⃣ Variables de Paginación
    numbersEstadosSolicitud: [],
    currentPageEstadosSolicitud: 1,
    perPageEstadosSolicitud: 10,

    // Campos para el formulario de 'Nuevo'
    codigo: '',
    nombre: '',
    descripcion: '',
    es_final: false,
    orden: '',
    // Filtros
    filtroEstadoSolicitud: '',
    ordenarPor: 'nombre',
    ordenarDireccion: 'asc',
    // Colección filtrada y ordenada
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

    // 2️⃣ Métodos de Paginación (operan sobre la lista filtrada)
    paginatedEstadosSolicitud() {
        return this.filteredEstadosSolicitud.slice(
            (this.currentPageEstadosSolicitud - 1) * this.perPageEstadosSolicitud,
            this.currentPageEstadosSolicitud * this.perPageEstadosSolicitud
        );
    },
    totalPagesEstadosSolicitud() {
        return Math.ceil(this.filteredEstadosSolicitud.length / this.perPageEstadosSolicitud);
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

    // 3️⃣ Sincronizar Alias en cada operación CRUD
    async fetchEstadosSolicitud() {
        await window.estadosSolicitudApiHandlers.fetchEstadosSolicitud(this);
        this.numbersEstadosSolicitud = this.estadosSolicitud; // ← LÍNEA AGREGADA
    },
    async submitEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.submitEstadoSolicitud(this);
        this.fetchEstadosSolicitud(); // Refrescar datos
    },
    async updateEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.updateEstadoSolicitud(this);
        this.fetchEstadosSolicitud(); // Refrescar datos
    },
    async deleteEstadoSolicitud() {
        await window.estadosSolicitudApiHandlers.deleteEstadoSolicitud(this);
        this.fetchEstadosSolicitud(); // Refrescar datos
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
    // 4️⃣ Reset de página en filtros
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
            <button @click="isEstadoSolicitudModalOpen = true"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                Nuevo Estado
            </button>
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
                        <!-- 5️⃣ Usar paginatedEstadosSolicitud() en el template -->
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
                                    <a href="#"
                                        @click.prevent="isEstadoSolicitudEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(estado))"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#"
                                        @click.prevent="isEstadoSolicitudDeleteModalOpen = true; itemToDelete = { id_estado_solicitud_pk: estado.id_estado_solicitud_pk, nombre: estado.nombre }"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border p-4 space-y-2">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="estado.nombre">
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Código: ' + estado.codigo"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="estado.descripcion"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400"
                            x-text="'Es Final: ' + (estado.es_final ? 'Sí' : 'No')"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Orden: ' + estado.orden"></p>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button
                                @click.prevent="isEstadoSolicitudEditModalOpen = true; itemToEdit = JSON.parse(JSON.stringify(estado))"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Editar</button>
                            <button
                                @click.prevent="isEstadoSolicitudDeleteModalOpen = true; itemToDelete = { id_estado_solicitud_pk: estado.id_estado_solicitud_pk, nombre: estado.nombre }"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- 6️⃣ Componente de Paginación -->
    <div x-show="filteredEstadosSolicitud.length > perPageEstadosSolicitud" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <!-- Mostrando (centered, supports light/dark) -->
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

        <!-- Controls (light/dark) -->
        <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageEstadosSolicitud()" :disabled="currentPageEstadosSolicitud === 1"
                    class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
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
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>


    <div>
        <x-admin.form-modal modalName="isEstadoSolicitudModalOpen" title="Nuevo Estado de Solicitud"
            submitLabel="Guardar" formId="formEstadoSolicitud" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nombre" x-model="nombre" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                    <input type="text" id="codigo" x-model="codigo" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div class="col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="descripcion" x-model="descripcion" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"></textarea>
                </div>
                <div>
                    <label for="orden" class="block text-sm font-medium text-gray-700">Orden</label>
                    <input type="number" id="orden" x-model="orden" required
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="es_final" x-model="es_final"
                        class="rounded border-gray-500 text-blue-600 shadow-sm">
                    <label for="es_final" class="ml-2 block text-sm font-medium text-gray-700">Es Final</label>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal modalName="isEstadoSolicitudEditModalOpen" title="Editar Estado de Solicitud"
            itemToEdit="itemToEdit" formId="formEditEstadoSolicitud" maxWidth="max-w-2xl">
            <template x-if="itemToEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" id="edit_nombre" x-model="itemToEdit.nombre" required
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                    </div>
                    <div>
                        <label for="edit_codigo" class="block text-sm font-medium text-gray-700">Código</label>
                        <input type="text" id="edit_codigo" x-model="itemToEdit.codigo" required
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                    </div>
                    <div class="col-span-2">
                        <label for="edit_descripcion"
                            class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea id="edit_descripcion" x-model="itemToEdit.descripcion" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2"></textarea>
                    </div>
                    <div>
                        <label for="edit_orden" class="block text-sm font-medium text-gray-700">Orden</label>
                        <input type="number" id="edit_orden" x-model="itemToEdit.orden" required
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border px-2">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="edit_es_final" x-model="itemToEdit.es_final"
                            class="rounded border-gray-500 text-blue-600 shadow-sm">
                        <label for="edit_es_final" class="ml-2 block text-sm font-medium text-gray-700">Es Final</label>
                    </div>
                </div>
            </template>
        </x-admin.edit-modal>

        <x-admin.confirmation-modal modalName="isEstadoSolicitudDeleteModalOpen" itemToDelete="itemToDelete"
            message="¿Estás seguro de que quieres eliminar este estado?" />
    </div>
</div>