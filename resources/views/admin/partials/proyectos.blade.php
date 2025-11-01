<div x-data="{
    // --- Control de Pestañas ---
    tab: 'proyectos',

    // --- Estado para PROYECTOS ---
    isProyectoModalOpen: false,
    isProyectoEditModalOpen: false,
    isProyectoDeleteModalOpen: false,
    itemToEdit: null,
    itemToDelete: null,
    proyectos: [],
    numbersProyectos: [], // Alias para paginación
    loadingProyectos: false,
    nombre_proyecto: '',
    fecha_inicio_proyecto: '',
    fecha_estimada_fin_proyecto: '',
    fecha_finalizacion_proyecto: '',
    descripcion_proyecto: '',
    id_orden_servicio_fk: '',
    id_estado_proyecto_fk: '',
    filtroProyecto: '',
    ordenarPorProyecto: '',
    currentPageProyectos: 1,
    perPageProyectos: 10,
    formProyecto: { _touched: {} },

    // --- Estado para INGRESOS ---
    isIngresoModalOpen: false,
    isIngresoEditModalOpen: false,
    isIngresoDeleteModalOpen: false,
    ingresoToEdit: null,
    ingresoToDelete: null,
    ingresos: [],
    numbersIngresos: [], // Alias para paginación
    loadingIngresos: false,
    nombre_ingreso: '',
    fecha_ingreso: '',
    monto_ingreso: '',
    descripcion_ingreso: '',
    id_proyecto_fk_ingreso: '',
    id_categoria_fk_ingreso: '',
    filtroIngreso: '',
    ordenarPorIngreso: '',
    currentPageIngresos: 1,
    perPageIngresos: 10,
    formIngreso: { _touched: {} },

    // --- Estado para GASTOS ---
    isGastoModalOpen: false,
    isGastoEditModalOpen: false,
    isGastoDeleteModalOpen: false,
    gastoToEdit: null,
    gastoToDelete: null,
    gastos: [],
    numbersGastos: [], // Alias para paginación
    loadingGastos: false,
    nombre_gasto: '',
    fecha_gasto: '',
    monto_gasto: '',
    descripcion_gasto: '',
    id_proyecto_fk_gasto: '',
    id_categoria_fk_gasto: '',
    filtroGasto: '',
    ordenarPorGasto: '',
    currentPageGastos: 1,
    perPageGastos: 10,
    formGasto: { _touched: {} },

    // --- Errores ---
    errors: {},
    
    // --- Catálogos (para los <select>) ---
    catalogoEstadosProyecto: [],
    catalogoOrdenesServicio: [],
    catalogoProyectos: [],
    catalogoCategorias: [],

    // --- Métodos de Paginación para PROYECTOS ---
    paginatedProyectos() {
        return this.proyectos.slice((this.currentPageProyectos - 1) * this.perPageProyectos, this.currentPageProyectos * this.perPageProyectos);
    },
    totalPagesProyectos() {
        return Math.ceil(this.proyectos.length / this.perPageProyectos);
    },
    nextPageProyectos() {
        if (this.currentPageProyectos < this.totalPagesProyectos()) {
            this.currentPageProyectos++;
        }
    },
    prevPageProyectos() {
        if (this.currentPageProyectos > 1) {
            this.currentPageProyectos--;
        }
    },

    // --- Métodos de Paginación para INGRESOS ---
    paginatedIngresos() {
        return this.ingresos.slice((this.currentPageIngresos - 1) * this.perPageIngresos, this.currentPageIngresos * this.perPageIngresos);
    },
    totalPagesIngresos() {
        return Math.ceil(this.ingresos.length / this.perPageIngresos);
    },
    nextPageIngresos() {
        if (this.currentPageIngresos < this.totalPagesIngresos()) {
            this.currentPageIngresos++;
        }
    },
    prevPageIngresos() {
        if (this.currentPageIngresos > 1) {
            this.currentPageIngresos--;
        }
    },

    // --- Métodos de Paginación para GASTOS ---
    paginatedGastos() {
        return this.gastos.slice((this.currentPageGastos - 1) * this.perPageGastos, this.currentPageGastos * this.perPageGastos);
    },
    totalPagesGastos() {
        return Math.ceil(this.gastos.length / this.perPageGastos);
    },
    nextPageGastos() {
        if (this.currentPageGastos < this.totalPagesGastos()) {
            this.currentPageGastos++;
        }
    },
    prevPageGastos() {
        if (this.currentPageGastos > 1) {
            this.currentPageGastos--;
        }
    },

    // --- Lógica de la API ---
    async fetchProyectos() { 
        await window.proyectosApiHandlers.fetchProyectos(this); 
        this.numbersProyectos = this.proyectos;
    },
    async submitProyecto() { 
        await window.proyectosApiHandlers.submitProyecto(this); 
        this.numbersProyectos = this.proyectos;
    },
    async updateProyecto() { 
        await window.proyectosApiHandlers.updateProyecto(this); 
        this.numbersProyectos = this.proyectos;
    },
    async deleteProyecto() { 
        await window.proyectosApiHandlers.deleteProyecto(this); 
        this.numbersProyectos = this.proyectos;
    },
    async fetchIngresos() { 
        await window.ingresosApiHandlers.fetchIngresos(this); 
        this.numbersIngresos = this.ingresos;
    },
    async submitIngreso() { 
        await window.ingresosApiHandlers.submitIngreso(this); 
        this.numbersIngresos = this.ingresos;
    },
    async updateIngreso() { 
        await window.ingresosApiHandlers.updateIngreso(this); 
        this.numbersIngresos = this.ingresos;
    },
    async deleteIngreso() { 
        await window.ingresosApiHandlers.deleteIngreso(this); 
        this.numbersIngresos = this.ingresos;
    },
    async fetchGastos() { 
        await window.gastosApiHandlers.fetchGastos(this); 
        this.numbersGastos = this.gastos;
    },
    async submitGasto() { 
        await window.gastosApiHandlers.submitGasto(this); 
        this.numbersGastos = this.gastos;
    },
    async updateGasto() { 
        await window.gastosApiHandlers.updateGasto(this); 
        this.numbersGastos = this.gastos;
    },
    async deleteGasto() { 
        await window.gastosApiHandlers.deleteGasto(this); 
        this.numbersGastos = this.gastos;
    },
    async fetchCatalogos() {
        await window.catalogosApiHandlers.fetchEstadosProyecto(this);
        await window.catalogosApiHandlers.fetchOrdenesServicio(this);
        await window.catalogosApiHandlers.fetchProyectos(this);
        await window.catalogosApiHandlers.fetchCategorias(this);
    },

    async openNuevoProyecto() {
        try {
            if (!this.catalogoOrdenesServicio || this.catalogoOrdenesServicio.length === 0) {
                await window.catalogosApiHandlers.fetchOrdenesServicio(this);
            }
        } catch (e) {}
        this.isProyectoModalOpen = true;
    },

    openReporte() {
        const params = new URLSearchParams({
            q: this.filtroProyecto || '',
            sort: this.ordenarPorProyecto || '',
            direction: 'asc',
            all: '1'
        });
        window.open('/admin/reportes-header?modulo=proyectos&' + params.toString(), '_blank');
    },

    openReporteMovimientos() {
        const params = new URLSearchParams({
            q_ingreso: this.filtroIngreso || '',
            sort_ingreso: this.ordenarPorIngreso || '',
            q_gasto: this.filtroGasto || '',
            sort_gasto: this.ordenarPorGasto || '',
            direction: 'asc'
        });
        window.open('/admin/reportes-header?modulo=movimientos-proyecto&' + params.toString(), '_blank');
    },

    formatDate(date) {
        if (!date) return 'N/A';
        try {
            if (typeof date === 'string' && date.indexOf('/') !== -1) {
                const parts = date.split('/').map(s => s.trim());
                if (parts.length === 3) return `${parts[2]}-${parts[1].padStart(2,'0')}-${parts[0].padStart(2,'0')}`;
            }
            const d = new Date(date);
            if (isNaN(d.getTime())) return date;
            return d.toISOString().slice(0,10);
        } catch (e) {
            return date;
        }
    },

    handleModalSubmit(event) {
        if (event.detail.formId === 'formProyecto') this.submitProyecto();
        if (event.detail.formId === 'formEditProyecto') this.updateProyecto();
        if (event.detail.formId === 'formIngreso') this.submitIngreso();
        if (event.detail.formId === 'formEditIngreso') this.updateIngreso();
        if (event.detail.formId === 'formGasto') this.submitGasto();
        if (event.detail.formId === 'formEditGasto') this.updateGasto();
    },
    handleDelete() {
        if (this.isProyectoDeleteModalOpen) this.deleteProyecto();
        if (this.isIngresoDeleteModalOpen) this.deleteIngreso();
        if (this.isGastoDeleteModalOpen) this.deleteGasto();
    }
}"
x-init="fetchProyectos(); fetchIngresos(); fetchGastos(); fetchCatalogos();"
x-effect="
$watch('filtroProyecto', () => { fetchProyectos(); currentPageProyectos = 1; });
$watch('ordenarPorProyecto', () => { fetchProyectos(); currentPageProyectos = 1; });
$watch('filtroIngreso', () => { fetchIngresos(); currentPageIngresos = 1; });
$watch('ordenarPorIngreso', () => { fetchIngresos(); currentPageIngresos = 1; });
$watch('filtroGasto', () => { fetchGastos(); currentPageGastos = 1; });
$watch('ordenarPorGasto', () => { fetchGastos(); currentPageGastos = 1; });
"
@keydown.escape.window="
    isProyectoModalOpen = false;
    isProyectoEditModalOpen = false;
    isProyectoDeleteModalOpen = false;
    isIngresoModalOpen = false;
    isIngresoEditModalOpen = false;
    isIngresoDeleteModalOpen = false;
    isGastoModalOpen = false;
    isGastoEditModalOpen = false;
    isGastoDeleteModalOpen = false;
"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()"
x-effect="if (ingresoToEdit && isIngresoEditModalOpen) {
    $nextTick(() => {
        ingresoToEdit.id_proyecto_fk = ingresoToEdit.proyecto?.id_proyecto_pk || '';
        ingresoToEdit.id_categoria_fk = ingresoToEdit.categoria?.id_categoria_pk || '';
    });
}">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white nunito-bold mb-8">Gestión de Proyectos</h1>
    </div>

    <ul class="flex border-b border-gray-200 dark:border-gray-700 nunito-bold mb-6">
        <li @click="tab='proyectos'" :class="tab==='proyectos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-200 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Proyectos</li>
        <li @click="tab='movimientos'" :class="tab==='movimientos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-200 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Movimientos</li>
    </ul>

    {{-- ==================== PESTAÑA DE PROYECTOS ==================== --}}
    <div x-show="tab==='proyectos'">
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                @include('partials.filtros-generales', [
                    'searchModel' => 'filtroProyecto',
                    'ordenarModel' => 'ordenarPorProyecto',
                    'ordenarOptions' => [
                        'nombre' => 'Nombre',
                        'fecha_inicio' => 'Fecha Inicio'
                    ]
                ])
            </x-slot>
            <x-slot name="actions">
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <button @click.prevent="openNuevoProyecto()" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Proyecto
                    </button>
                    <button @click="openReporte()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </button>
                </div>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha Inicio</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha Estimada</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha Fin Real</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Orden de Servicio</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Descripción</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Estado</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingProyectos">
                            <tr>
                                <td colspan="8" class="text-center p-4 text-gray-500 nunito-regular">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando proyectos...
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingProyectos && proyectos.length === 0">
                            <tr>
                                <td colspan="8" class="text-center p-4 text-gray-500 nunito-regular">
                                    No hay proyectos registrados
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingProyectos && proyectos.length > 0">
                            <template x-for="(proyecto, index) in paginatedProyectos()" :key="proyecto.id_proyecto_pk">
                                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                    :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedProyectos().length - 1 }">
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="proyecto.nombre_proyecto"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="formatDate(proyecto.fecha_inicio_proyecto)"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="formatDate(proyecto.fecha_estimada_fin_proyecto)"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="formatDate(proyecto.fecha_finalizacion_proyecto)"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="proyecto.orden_servicio ? proyecto.orden_servicio.numero_orden_servicio : 'N/A'"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="proyecto.descripcion_proyecto"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200">
                                        <span x-text="(proyecto.estado_proyecto?.nombre) || proyecto.estado_proyecto?.nombre_estado || 'Sin Estado'"></span>
                                    </td>
                                    <td class="py-2 px-4 flex gap-2">
                                        <a href="#" @click.prevent="itemToEdit = JSON.parse(JSON.stringify(proyecto)); $nextTick(() => { isProyectoEditModalOpen = true; })" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                        <a href="#" @click.prevent="isProyectoDeleteModalOpen = true; itemToDelete = { id_proyecto_pk: proyecto.id_proyecto_pk, nombre: proyecto.nombre_proyecto }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </x-slot>

            <x-slot name="cards">
                <template x-if="loadingProyectos">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando proyectos...
                    </div>
                </template>
                <template x-if="!loadingProyectos && proyectos.length === 0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                        No hay proyectos registrados
                    </div>
                </template>
                <template x-if="!loadingProyectos && proyectos.length > 0">
                    <template x-for="proyecto in paginatedProyectos()" :key="proyecto.id_proyecto_pk">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="proyecto.nombre_proyecto"></h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="proyecto.descripcion_proyecto"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Fecha inicio: ' + formatDate(proyecto.fecha_inicio_proyecto)"></p>
                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <button @click.prevent="itemToEdit = JSON.parse(JSON.stringify(proyecto)); $nextTick(() => { isProyectoEditModalOpen = true; })" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button @click.prevent="isProyectoDeleteModalOpen = true; itemToDelete = { id_proyecto_pk: proyecto.id_proyecto_pk, nombre: proyecto.nombre_proyecto }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </template>
                </template>
            </x-slot>
        </x-responsive-table>

        <!-- Paginación para Proyectos -->
        <div x-show="numbersProyectos.length > perPageProyectos" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
            <div class="mb-2">
                <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                    Mostrando
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageProyectos - 1) * perPageProyectos + 1"></strong>
                    a
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageProyectos * perPageProyectos, numbersProyectos.length)"></strong>
                    de
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="numbersProyectos.length"></strong>
                    resultados
                </span>
            </div>
            <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
                <button @click="prevPageProyectos()" :disabled="currentPageProyectos === 1"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span>Anterior</span>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="page in Array.from({length: totalPagesProyectos()}, (_, i) => i + 1).slice(Math.max(0, currentPageProyectos - 3), currentPageProyectos + 2)" :key="page">
                        <button @click="currentPageProyectos = page"
                                class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                :class="page === currentPageProyectos ? 'bg-blue-600 text-white' : ''">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>
                <button @click="nextPageProyectos()" :disabled="currentPageProyectos === totalPagesProyectos()"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span>Siguiente</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== PESTAÑA DE MOVIMIENTOS ==================== --}}
    <div x-show="tab==='movimientos'" x-cloak class="space-y-8">
        <!-- Botón de reporte para movimientos -->
        <div class="flex justify-end mb-4">
            <button @click="openReporteMovimientos()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center gap-2">
                <i class="fas fa-file-alt"></i> Generar Reporte de Movimientos
            </button>
        </div>

        <!-- CRUD de Ingresos -->
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                @include('partials.filtros-generales', [
                    'searchModel' => 'filtroIngreso',
                    'ordenarModel' => 'ordenarPorIngreso',
                    'ordenarOptions' => [
                        'proyecto' => 'Proyecto',
                        'fecha' => 'Fecha',
                        'monto' => 'Monto'
                    ]
                ])
            </x-slot>
            <x-slot name="actions">
                <button @click="isIngresoModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular text-sm">
                    Agregar Ingreso
                </button>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Proyecto</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Monto</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingIngresos">
                            <tr>
                                <td colspan="5" class="text-center p-4 text-gray-500 nunito-regular">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando ingresos...
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingIngresos && ingresos.length === 0">
                            <tr>
                                <td colspan="5" class="text-center p-4 text-gray-500 nunito-regular">
                                    No hay ingresos registrados
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingIngresos && ingresos.length > 0">
                            <template x-for="(ingreso, index) in paginatedIngresos()" :key="ingreso.id_ingresos_pk">
                                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                    :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedIngresos().length - 1 }">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ingreso.nombre_ingreso"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="ingreso.proyecto ? ingreso.proyecto.nombre_proyecto : 'N/A'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="formatDate(ingreso.fecha_ingreso)"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(ingreso.monto_ingreso)"></td>
                                    <td class="py-2 px-4 flex gap-2">
                                        <a href="#" @click.prevent="ingresoToEdit = JSON.parse(JSON.stringify(ingreso)); $nextTick(() => { isIngresoEditModalOpen = true; })" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                        <a href="#" @click.prevent="isIngresoDeleteModalOpen = true; ingresoToDelete = { id_ingresos_pk: ingreso.id_ingresos_pk, nombre: ingreso.nombre_ingreso }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </x-slot>

            <x-slot name="cards">
                <template x-if="loadingIngresos">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando ingresos...
                    </div>
                </template>
                <template x-if="!loadingIngresos && ingresos.length === 0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                        No hay ingresos registrados
                    </div>
                </template>
                <template x-if="!loadingIngresos && ingresos.length > 0">
                    <template x-for="ingreso in paginatedIngresos()" :key="ingreso.id_ingresos_pk">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="ingreso.nombre_ingreso"></h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Proyecto: ' + (ingreso.proyecto ? ingreso.proyecto.nombre_proyecto : 'N/A')"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Fecha: ' + formatDate(ingreso.fecha_ingreso)"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Monto: ' + new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(ingreso.monto_ingreso)"></p>
                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <button @click.prevent="ingresoToEdit = JSON.parse(JSON.stringify(ingreso)); $nextTick(() => { isIngresoEditModalOpen = true; })" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button @click.prevent="isIngresoDeleteModalOpen = true; ingresoToDelete = { id_ingresos_pk: ingreso.id_ingresos_pk, nombre: ingreso.nombre_ingreso }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </template>
                </template>
            </x-slot>
        </x-responsive-table>

        <!-- Paginación para Ingresos -->
        <div x-show="numbersIngresos.length > perPageIngresos" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
            <div class="mb-2">
                <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                    Mostrando
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageIngresos - 1) * perPageIngresos + 1"></strong>
                    a
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageIngresos * perPageIngresos, numbersIngresos.length)"></strong>
                    de
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="numbersIngresos.length"></strong>
                    resultados
                </span>
            </div>
            <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
                <button @click="prevPageIngresos()" :disabled="currentPageIngresos === 1"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span>Anterior</span>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="page in Array.from({length: totalPagesIngresos()}, (_, i) => i + 1).slice(Math.max(0, currentPageIngresos - 3), currentPageIngresos + 2)" :key="page">
                        <button @click="currentPageIngresos = page"
                                class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                :class="page === currentPageIngresos ? 'bg-blue-600 text-white' : ''">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>
                <button @click="nextPageIngresos()" :disabled="currentPageIngresos === totalPagesIngresos()"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span>Siguiente</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
        
        <!-- CRUD de Gastos -->
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                @include('partials.filtros-generales', [
                    'searchModel' => 'filtroGasto',
                    'ordenarModel' => 'ordenarPorGasto',
                    'ordenarOptions' => [
                        'nombre' => 'Nombre',
                        'fecha' => 'Fecha',
                        'monto' => 'Monto'
                    ]
                ])
            </x-slot>
            <x-slot name="actions">
                <button @click="isGastoModalOpen = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg nunito-regular text-sm">
                    Agregar Gasto
                </button>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Nombre</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Proyecto</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Fecha</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Monto</th>
                            <th class="py-2 px-4 text-left border-0 first:rounded-tl-lg last:rounded-tr-lg dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingGastos">
                            <tr>
                                <td colspan="5" class="text-center p-4 text-gray-500 nunito-regular">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando gastos...
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingGastos && gastos.length === 0">
                            <tr>
                                <td colspan="5" class="text-center p-4 text-gray-500 nunito-regular">
                                    No hay gastos registrados
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingGastos && gastos.length > 0">
                            <template x-for="(gasto, index) in paginatedGastos()" :key="gasto.id_gasto_pk">
                                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                    :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === paginatedGastos().length - 1 }">
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="gasto.nombre"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="gasto.proyecto ? gasto.proyecto.nombre_proyecto : 'N/A'"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="formatDate(gasto.fecha)"></td>
                                    <td class="py-2 px-4 text-gray-900 dark:text-gray-200" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(gasto.monto)"></td>
                                    <td class="py-2 px-4 flex gap-2">
                                        <a href="#" @click.prevent="gastoToEdit = JSON.parse(JSON.stringify(gasto)); $nextTick(() => { isGastoEditModalOpen = true; })" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                        <a href="#" @click.prevent="isGastoDeleteModalOpen = true; gastoToDelete = { id_gasto_pk: gasto.id_gasto_pk, nombre: gasto.nombre }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </x-slot>

            <x-slot name="cards">
                <template x-if="loadingGastos">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando gastos...
                    </div>
                </template>
                <template x-if="!loadingGastos && gastos.length === 0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-8 text-center text-gray-500 nunito-regular">
                        No hay gastos registrados
                    </div>
                </template>
                <template x-if="!loadingGastos && gastos.length > 0">
                    <template x-for="gasto in paginatedGastos()" :key="gasto.id_gasto_pk">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-black p-4 space-y-2">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-200 nunito-bold" x-text="gasto.nombre"></h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Proyecto: ' + (gasto.proyecto ? gasto.proyecto.nombre_proyecto : 'N/A')"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Fecha: ' + formatDate(gasto.fecha)"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 nunito-regular" x-text="'Monto: ' + new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(gasto.monto)"></p>
                            <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <button @click.prevent="gastoToEdit = JSON.parse(JSON.stringify(gasto)); $nextTick(() => { isGastoEditModalOpen = true; })" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button @click.prevent="isGastoDeleteModalOpen = true; gastoToDelete = { id_gasto_pk: gasto.id_gasto_pk, nombre: gasto.nombre }" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </template>
                </template>
            </x-slot>
        </x-responsive-table>

        <!-- Paginación para Gastos -->
        <div x-show="numbersGastos.length > perPageGastos" class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
            <div class="mb-2">
                <span class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                    Mostrando
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="(currentPageGastos - 1) * perPageGastos + 1"></strong>
                    a
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="Math.min(currentPageGastos * perPageGastos, numbersGastos.length)"></strong>
                    de
                    <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="numbersGastos.length"></strong>
                    resultados
                </span>
            </div>
            <div class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
                <button @click="prevPageGastos()" :disabled="currentPageGastos === 1"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span>Anterior</span>
                </button>
                <div class="flex items-center gap-1">
                    <template x-for="page in Array.from({length: totalPagesGastos()}, (_, i) => i + 1).slice(Math.max(0, currentPageGastos - 3), currentPageGastos + 2)" :key="page">
                        <button @click="currentPageGastos = page"
                                class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                :class="page === currentPageGastos ? 'bg-blue-600 text-white' : ''">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>
                <button @click="nextPageGastos()" :disabled="currentPageGastos === totalPagesGastos()"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span>Siguiente</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== MODALES ==================== --}}
    <div>
        <!-- Modal Nuevo Proyecto -->
        <x-admin.form-modal modalName="isProyectoModalOpen" title="Nuevo Proyecto" submitLabel="Guardar Proyecto" formId="formProyecto" maxWidth="max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="nombre_proyecto" required maxlength="100"
                        @input="formProyecto._touched.nombre_proyecto = true"
                        @blur="formProyecto._touched.nombre_proyecto = true"
                        :class="formProyecto._touched.nombre_proyecto && !nombre_proyecto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.nombre_proyecto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.nombre_proyecto[0]"></p>
                    </template>
                    <small x-show="!errors.nombre_proyecto" class="text-xs text-gray-500 block mt-1" :class="formProyecto._touched && formProyecto._touched.nombre_proyecto && (nombre_proyecto === '' || nombre_proyecto.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Orden de Servicio</label>
                    <select x-model="id_orden_servicio_fk" required
                        @change="formProyecto._touched.id_orden_servicio_fk = true"
                        :class="formProyecto._touched.id_orden_servicio_fk && !id_orden_servicio_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="os in catalogoOrdenesServicio" :key="os.id_orden_servicio_pk">
                            <option :value="os.id_orden_servicio_pk" x-text="os.codigo_orden || os.numero_orden_servicio || os.nombre_orden || 'OS-' + os.id_orden_servicio_pk"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_orden_servicio_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_orden_servicio_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_orden_servicio_fk" class="text-xs text-gray-500 block mt-1" :class="formProyecto._touched.id_orden_servicio_fk && !id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Inicio</label>
                    <input type="date" x-model="fecha_inicio_proyecto" required
                        @change="formProyecto._touched.fecha_inicio_proyecto = true"
                        :class="formProyecto._touched.fecha_inicio_proyecto && !fecha_inicio_proyecto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.fecha_inicio_proyecto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_inicio_proyecto[0]"></p>
                    </template>
                    <small x-show="!errors.fecha_inicio_proyecto" class="text-xs text-gray-500 block mt-1" :class="formProyecto._touched.fecha_inicio_proyecto && !fecha_inicio_proyecto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Estimada</label>
                    <input type="date" x-model="fecha_estimada_fin_proyecto" required
                        @change="formProyecto._touched.fecha_estimada_fin_proyecto = true"
                        :class="formProyecto._touched.fecha_estimada_fin_proyecto && !fecha_estimada_fin_proyecto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <small class="text-xs text-gray-500 block mt-1" :class="formProyecto._touched.fecha_estimada_fin_proyecto && !fecha_estimada_fin_proyecto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Real</label>
                    <input type="date" x-model="fecha_finalizacion_proyecto"
                        @change="formProyecto._touched.fecha_finalizacion_proyecto = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Estado del Proyecto</label>
                    <select x-model="id_estado_proyecto_fk" required
                        @change="formProyecto._touched.id_estado_proyecto_fk = true"
                        :class="formProyecto._touched.id_estado_proyecto_fk && !id_estado_proyecto_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="estado in catalogoEstadosProyecto" :key="estado.id_estado_proyecto_pk">
                            <option :value="estado.id_estado_proyecto_pk" x-text="estado.nombre"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_estado_proyecto_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_estado_proyecto_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_estado_proyecto_fk" class="text-xs text-gray-500 block mt-1" :class="formProyecto._touched.id_estado_proyecto_fk && !id_estado_proyecto_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="descripcion_proyecto" rows="3" maxlength="500"
                        @input="formProyecto._touched.descripcion_proyecto = true"
                        @blur="formProyecto._touched.descripcion_proyecto = true"
                        :class="formProyecto._touched.descripcion_proyecto && descripcion_proyecto.length > 500 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"></textarea>
                    <template x-if="errors.descripcion_proyecto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion_proyecto[0]"></p>
                    </template>
                    <small x-show="!errors.descripcion_proyecto" class="text-xs text-gray-500 block mt-1" :class="formProyecto._touched && formProyecto._touched.descripcion_proyecto && descripcion_proyecto.length >= 500 ? 'text-red-500' : ''">Máximo 500 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Proyecto -->
        <x-admin.edit-modal modalName="isProyectoEditModalOpen" title="Editar Proyecto" formId="formEditProyecto" itemToEdit="itemToEdit" maxWidth="max-w-4xl">
            <template x-if="itemToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-effect="if (itemToEdit && isProyectoEditModalOpen) { $nextTick(() => {
                    // populate fk ids from loaded relations if missing
                    itemToEdit.id_orden_servicio_fk = itemToEdit.id_orden_servicio_fk || itemToEdit.orden_servicio?.id_orden_servicio_pk || '';
                    itemToEdit.id_estado_proyecto_fk = itemToEdit.id_estado_proyecto_fk || itemToEdit.estado_proyecto?.id_estado_proyecto_pk || '';
                    // normalize date fields to YYYY-MM-DD for <input type=date>
                    (function normalize(field){
                        try {
                            var raw = itemToEdit[field];
                            if(!raw) { itemToEdit[field] = ''; return; }
                            if (typeof raw === 'string' && raw.indexOf('/') !== -1) {
                                var parts = raw.split('/').map(s => s.trim());
                                if (parts.length === 3) { itemToEdit[field] = parts[2] + '-' + parts[1].padStart(2,'0') + '-' + parts[0].padStart(2,'0'); return; }
                            }
                            var d = new Date(raw);
                            if (!isNaN(d.getTime())) { itemToEdit[field] = d.toISOString().slice(0,10); }
                        } catch(e) { /* noop */ }
                    })( 'fecha_inicio_proyecto');
                    (function(){ try { var f='fecha_estimada_fin_proyecto'; var raw=itemToEdit[f]; if(!raw){ itemToEdit[f] = ''; } else if(typeof raw === 'string' && raw.indexOf('/') !== -1){ var parts=raw.split('/').map(s=>s.trim()); if(parts.length===3) itemToEdit[f]=parts[2]+'-'+parts[1].padStart(2,'0')+'-'+parts[0].padStart(2,'0'); } else { var d=new Date(raw); if(!isNaN(d.getTime())) itemToEdit[f]=d.toISOString().slice(0,10); } } catch(e){} })();
                    (function(){ try { var f='fecha_finalizacion_proyecto'; var raw=itemToEdit[f]; if(!raw){ itemToEdit[f] = ''; } else if(typeof raw === 'string' && raw.indexOf('/') !== -1){ var parts=raw.split('/').map(s=>s.trim()); if(parts.length===3) itemToEdit[f]=parts[2]+'-'+parts[1].padStart(2,'0')+'-'+parts[0].padStart(2,'0'); } else { var d=new Date(raw); if(!isNaN(d.getTime())) itemToEdit[f]=d.toISOString().slice(0,10); } } catch(e){} })();
                }); }">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="itemToEdit.nombre_proyecto" required maxlength="100"
                        @input="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.nombre_proyecto = true"
                        @blur="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.nombre_proyecto = true"
                        :class="itemToEdit._touched && itemToEdit._touched.nombre_proyecto && !itemToEdit.nombre_proyecto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.nombre_proyecto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.nombre_proyecto[0]"></p>
                    </template>
                    <small x-show="!errors.nombre_proyecto" class="text-xs text-gray-500 block mt-1" :class="itemToEdit._touched && itemToEdit._touched.nombre_proyecto && (itemToEdit.nombre_proyecto === '' || itemToEdit.nombre_proyecto.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Orden de Servicio</label>
                    <select x-model="itemToEdit.id_orden_servicio_fk" required
                        @change="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.id_orden_servicio_fk = true"
                        :class="itemToEdit._touched && itemToEdit._touched.id_orden_servicio_fk && !itemToEdit.id_orden_servicio_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="os in catalogoOrdenesServicio" :key="os.id_orden_servicio_pk">
                            <option :value="os.id_orden_servicio_pk" x-text="os.codigo_orden || os.numero_orden_servicio || os.nombre_orden || 'OS-' + os.id_orden_servicio_pk" :selected="itemToEdit && (os.id_orden_servicio_pk == (itemToEdit.id_orden_servicio_fk || itemToEdit.orden_servicio?.id_orden_servicio_pk))"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_orden_servicio_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_orden_servicio_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_orden_servicio_fk" class="text-xs text-gray-500 block mt-1" :class="itemToEdit._touched && itemToEdit._touched.id_orden_servicio_fk && !itemToEdit.id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Inicio</label>
                    <input type="date" x-model="itemToEdit.fecha_inicio_proyecto" required
                        @change="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.fecha_inicio_proyecto = true"
                        :class="itemToEdit._touched && itemToEdit._touched.fecha_inicio_proyecto && !itemToEdit.fecha_inicio_proyecto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.fecha_inicio_proyecto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_inicio_proyecto[0]"></p>
                    </template>
                    <small x-show="!errors.fecha_inicio_proyecto" class="text-xs text-gray-500 block mt-1" :class="itemToEdit._touched && itemToEdit._touched.fecha_inicio_proyecto && !itemToEdit.fecha_inicio_proyecto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Estimada</label>
                    <input type="date" x-model="itemToEdit.fecha_estimada_fin_proyecto" required
                        @change="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.fecha_estimada_fin_proyecto = true"
                        :class="itemToEdit._touched && itemToEdit._touched.fecha_estimada_fin_proyecto && !itemToEdit.fecha_estimada_fin_proyecto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <small class="text-xs text-gray-500 block mt-1" :class="itemToEdit._touched && itemToEdit._touched.fecha_estimada_fin_proyecto && !itemToEdit.fecha_estimada_fin_proyecto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Real</label>
                    <input type="date" x-model="itemToEdit.fecha_finalizacion_proyecto"
                        @change="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.fecha_finalizacion_proyecto = true"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Estado del Proyecto</label>
                    <select x-model="itemToEdit.id_estado_proyecto_fk" required
                        @change="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.id_estado_proyecto_fk = true"
                        :class="itemToEdit._touched && itemToEdit._touched.id_estado_proyecto_fk && !itemToEdit.id_estado_proyecto_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="estado in catalogoEstadosProyecto" :key="estado.id_estado_proyecto_pk">
                            <option :value="estado.id_estado_proyecto_pk" x-text="estado.nombre" :selected="itemToEdit && (estado.id_estado_proyecto_pk == (itemToEdit.id_estado_proyecto_fk || itemToEdit.estado_proyecto?.id_estado_proyecto_pk))"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_estado_proyecto_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_estado_proyecto_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_estado_proyecto_fk" class="text-xs text-gray-500 block mt-1" :class="itemToEdit._touched && itemToEdit._touched.id_estado_proyecto_fk && !itemToEdit.id_estado_proyecto_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="itemToEdit.descripcion_proyecto" rows="3" maxlength="500"
                        @input="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.descripcion_proyecto = true"
                        @blur="itemToEdit._touched = itemToEdit._touched || {}; itemToEdit._touched.descripcion_proyecto = true"
                        :class="itemToEdit._touched && itemToEdit._touched.descripcion_proyecto && itemToEdit.descripcion_proyecto.length > 500 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"></textarea>
                    <template x-if="errors.descripcion_proyecto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion_proyecto[0]"></p>
                    </template>
                    <small x-show="!errors.descripcion_proyecto" class="text-xs text-gray-500 block mt-1" :class="itemToEdit._touched && itemToEdit._touched.descripcion_proyecto && itemToEdit.descripcion_proyecto.length > 500 ? 'text-red-500' : ''">Máximo 500 caracteres.</small>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>
        
        <x-admin.confirmation-modal modalName="isProyectoDeleteModalOpen" itemToDelete="itemToDelete" message="¿Seguro que quieres eliminar este proyecto?"/>

        <!-- Modal Nuevo Ingreso -->
        <x-admin.form-modal modalName="isIngresoModalOpen" title="Nuevo Ingreso" submitLabel="Guardar Ingreso" formId="formIngreso" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                    <select x-model="id_proyecto_fk_ingreso" required
                        @change="formIngreso._touched.id_proyecto_fk_ingreso = true"
                        :class="formIngreso._touched.id_proyecto_fk_ingreso && !id_proyecto_fk_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_proyecto_fk_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_proyecto_fk_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.id_proyecto_fk_ingreso" class="text-xs text-gray-500 block mt-1" :class="formIngreso._touched.id_proyecto_fk_ingreso && !id_proyecto_fk_ingreso ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="nombre_ingreso" required maxlength="100"
                        @input="formIngreso._touched.nombre_ingreso = true"
                        @blur="formIngreso._touched.nombre_ingreso = true"
                        :class="formIngreso._touched.nombre_ingreso && !nombre_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.nombre_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.nombre_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.nombre_ingreso" class="text-xs text-gray-500 block mt-1" :class="formIngreso._touched && formIngreso._touched.nombre_ingreso && (nombre_ingreso === '' || nombre_ingreso.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="fecha_ingreso" required
                        @change="formIngreso._touched.fecha_ingreso = true"
                        :class="formIngreso._touched.fecha_ingreso && !fecha_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.fecha_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.fecha_ingreso" class="text-xs text-gray-500 block mt-1" :class="formIngreso._touched.fecha_ingreso && !fecha_ingreso ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="text" inputmode="decimal" autocomplete="off" required
                        x-init="$nextTick(() => { if (monto_ingreso) { try { var v = monto_ingreso.toString(); var parts = v.split('.'); var int = (parts[0]||'').replace(/\B(?=(\d{3})+(?!\d))/g, ','); var dec = parts[1] ? parts[1].slice(0,2) : ''; if(int === '' && dec){ int = '0'; } $el.value = int + (dec ? '.'+dec : ''); } catch(e){} } })"
                        @input="formIngreso._touched.monto_ingreso = true; let raw=$event.target.value; let hasDot = raw.includes('.'); let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && (dec !== '' || hasDot)){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : ''); $event.target.value = formatted; monto_ingreso = int + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : '');"
                        @blur="formIngreso._touched.monto_ingreso = true; let raw=$event.target.value; let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && dec !== undefined && dec !== ''){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + ((dec !== undefined && dec !== '') ? '.'+dec : ''); $event.target.value = formatted; monto_ingreso = int + ((dec !== undefined && dec !== '') ? '.'+dec : '');"
                        :class="formIngreso._touched.monto_ingreso && !monto_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.monto_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.monto_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.monto_ingreso" class="text-xs text-gray-500 block mt-1" :class="formIngreso._touched.monto_ingreso && !monto_ingreso ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="id_categoria_fk_ingreso" required
                        @change="formIngreso._touched.id_categoria_fk_ingreso = true"
                        :class="formIngreso._touched.id_categoria_fk_ingreso && !id_categoria_fk_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => !c.tipo_categoria || c.tipo_categoria.toLowerCase() === 'ingreso')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_categoria_fk_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_categoria_fk_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.id_categoria_fk_ingreso" class="text-xs text-gray-500 block mt-1" :class="formIngreso._touched.id_categoria_fk_ingreso && !id_categoria_fk_ingreso ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="descripcion_ingreso" rows="3" maxlength="255"
                        @input="formIngreso._touched.descripcion_ingreso = true"
                        @blur="formIngreso._touched.descripcion_ingreso = true"
                        :class="formIngreso._touched.descripcion_ingreso && descripcion_ingreso.length > 250 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"></textarea>
                    <template x-if="errors.descripcion_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.descripcion_ingreso" class="text-xs text-gray-500 block mt-1" :class="formIngreso._touched && formIngreso._touched.descripcion_ingreso && descripcion_ingreso.length >= 250 ? 'text-red-500' : ''">Máximo 250 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Ingreso -->
        <x-admin.edit-modal modalName="isIngresoEditModalOpen" title="Editar Ingreso" formId="formEditIngreso" itemToEdit="ingresoToEdit" maxWidth="max-w-2xl">
            <template x-if="ingresoToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                    <select x-model="ingresoToEdit.id_proyecto_fk" x-bind:value="ingresoToEdit.id_proyecto_fk" required
                        @change="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.id_proyecto_fk = true"
                        :class="ingresoToEdit._touched && ingresoToEdit._touched.id_proyecto_fk && !ingresoToEdit.id_proyecto_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto" :selected="ingresoToEdit && (proyecto.id_proyecto_pk == (ingresoToEdit.id_proyecto_fk || ingresoToEdit.proyecto?.id_proyecto_pk))"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_proyecto_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_proyecto_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_proyecto_fk" class="text-xs text-gray-500 block mt-1" :class="ingresoToEdit._touched && ingresoToEdit._touched.id_proyecto_fk && !ingresoToEdit.id_proyecto_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="ingresoToEdit.nombre_ingreso" required maxlength="100"
                        @input="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.nombre_ingreso = true"
                        @blur="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.nombre_ingreso = true"
                        :class="ingresoToEdit._touched && ingresoToEdit._touched.nombre_ingreso && !ingresoToEdit.nombre_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.nombre_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.nombre_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.nombre_ingreso" class="text-xs text-gray-500 block mt-1" :class="ingresoToEdit._touched && ingresoToEdit._touched.nombre_ingreso && (ingresoToEdit.nombre_ingreso === '' || ingresoToEdit.nombre_ingreso.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="ingresoToEdit.fecha_ingreso" required
                        @change="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.fecha_ingreso = true"
                        :class="ingresoToEdit._touched && ingresoToEdit._touched.fecha_ingreso && !ingresoToEdit.fecha_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.fecha_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.fecha_ingreso" class="text-xs text-gray-500 block mt-1" :class="ingresoToEdit._touched && ingresoToEdit._touched.fecha_ingreso && !ingresoToEdit.fecha_ingreso ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="text" inputmode="decimal" autocomplete="off" required
                        x-init="$nextTick(() => { if (ingresoToEdit && ingresoToEdit.monto_ingreso) { try { var v = ingresoToEdit.monto_ingreso.toString(); var parts = v.split('.'); var int = (parts[0]||'').replace(/\B(?=(\d{3})+(?!\d))/g, ','); var dec = parts[1] ? parts[1].slice(0,2) : ''; if(int === '' && dec){ int = '0'; } $el.value = int + (dec ? '.'+dec : ''); } catch(e){} } })"
                        @input="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.monto_ingreso = true; let raw=$event.target.value; let hasDot = raw.includes('.'); let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && (dec !== '' || hasDot)){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : ''); $event.target.value = formatted; ingresoToEdit.monto_ingreso = int + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : '');"
                        @blur="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.monto_ingreso = true; let raw=$event.target.value; let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && dec !== undefined && dec !== ''){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + ((dec !== undefined && dec !== '') ? '.'+dec : ''); $event.target.value = formatted; ingresoToEdit.monto_ingreso = int + ((dec !== undefined && dec !== '') ? '.'+dec : '');"
                        :class="ingresoToEdit._touched && ingresoToEdit._touched.monto_ingreso && !ingresoToEdit.monto_ingreso ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.monto_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.monto_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.monto_ingreso" class="text-xs text-gray-500 block mt-1" :class="ingresoToEdit._touched && ingresoToEdit._touched.monto_ingreso && !ingresoToEdit.monto_ingreso ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="ingresoToEdit.id_categoria_fk" x-bind:value="ingresoToEdit.id_categoria_fk" required
                        @change="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.id_categoria_fk = true"
                        :class="ingresoToEdit._touched && ingresoToEdit._touched.id_categoria_fk && !ingresoToEdit.id_categoria_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => !c.tipo_categoria || c.tipo_categoria.toLowerCase() === 'ingreso')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria" :selected="ingresoToEdit && (cat.id_categoria_pk == (ingresoToEdit.id_categoria_fk || ingresoToEdit.categoria?.id_categoria_pk))"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_categoria_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_categoria_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_categoria_fk" class="text-xs text-gray-500 block mt-1" :class="ingresoToEdit._touched && ingresoToEdit._touched.id_categoria_fk && !ingresoToEdit.id_categoria_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="ingresoToEdit.descripcion_ingreso" rows="3" maxlength="255"
                        @input="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.descripcion_ingreso = true"
                        @blur="ingresoToEdit._touched = ingresoToEdit._touched || {}; ingresoToEdit._touched.descripcion_ingreso = true"
                        :class="ingresoToEdit._touched && ingresoToEdit._touched.descripcion_ingreso && ingresoToEdit.descripcion_ingreso.length > 250 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"></textarea>
                    <template x-if="errors.descripcion_ingreso">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion_ingreso[0]"></p>
                    </template>
                    <small x-show="!errors.descripcion_ingreso" class="text-xs text-gray-500 block mt-1" :class="ingresoToEdit._touched && ingresoToEdit._touched.descripcion_ingreso && ingresoToEdit.descripcion_ingreso.length >= 250 ? 'text-red-500' : ''">Máximo 250 caracteres.</small>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>
        
        <x-admin.confirmation-modal modalName="isIngresoDeleteModalOpen" itemToDelete="ingresoToDelete" message="¿Seguro que quieres eliminar este ingreso?"/>

        <!-- Modal Nuevo Gasto -->
        <x-admin.form-modal modalName="isGastoModalOpen" title="Nuevo Gasto" submitLabel="Guardar Gasto" formId="formGasto" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                    <select x-model="id_proyecto_fk_gasto" required
                        @change="formGasto._touched.id_proyecto_fk_gasto = true"
                        :class="formGasto._touched.id_proyecto_fk_gasto && !id_proyecto_fk_gasto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_proyecto_fk_gasto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_proyecto_fk_gasto[0]"></p>
                    </template>
                    <small x-show="!errors.id_proyecto_fk_gasto" class="text-xs text-gray-500 block mt-1" :class="formGasto._touched.id_proyecto_fk_gasto && !id_proyecto_fk_gasto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="nombre_gasto" required maxlength="100"
                        @input="formGasto._touched.nombre_gasto = true"
                        @blur="formGasto._touched.nombre_gasto = true"
                        :class="formGasto._touched.nombre_gasto && !nombre_gasto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.nombre_gasto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.nombre_gasto[0]"></p>
                    </template>
                    <small x-show="!errors.nombre_gasto" class="text-xs text-gray-500 block mt-1" :class="formGasto._touched && formGasto._touched.nombre_gasto && (nombre_gasto === '' || nombre_gasto.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="fecha_gasto" required
                        @change="formGasto._touched.fecha_gasto = true"
                        :class="formGasto._touched.fecha_gasto && !fecha_gasto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.fecha_gasto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_gasto[0]"></p>
                    </template>
                    <small x-show="!errors.fecha_gasto" class="text-xs text-gray-500 block mt-1" :class="formGasto._touched.fecha_gasto && !fecha_gasto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="text" inputmode="decimal" autocomplete="off" required
                        x-init="$nextTick(() => { if (monto_gasto) { try { var v = monto_gasto.toString(); var parts = v.split('.'); var int = (parts[0]||'').replace(/\B(?=(\d{3})+(?!\d))/g, ','); var dec = parts[1] ? parts[1].slice(0,2) : ''; if(int === '' && dec){ int = '0'; } $el.value = int + (dec ? '.'+dec : ''); } catch(e){} } })"
                        @input="formGasto._touched.monto_gasto = true; let raw=$event.target.value; let hasDot = raw.includes('.'); let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && (dec !== '' || hasDot)){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : ''); $event.target.value = formatted; monto_gasto = int + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : '');"
                        @blur="formGasto._touched.monto_gasto = true; let raw=$event.target.value; let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && dec !== undefined && dec !== ''){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + ((dec !== undefined && dec !== '') ? '.'+dec : ''); $event.target.value = formatted; monto_gasto = int + ((dec !== undefined && dec !== '') ? '.'+dec : '');"
                        :class="formGasto._touched.monto_gasto && !monto_gasto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.monto_gasto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.monto_gasto[0]"></p>
                    </template>
                    <small x-show="!errors.monto_gasto" class="text-xs text-gray-500 block mt-1" :class="formGasto._touched.monto_gasto && !monto_gasto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="id_categoria_fk_gasto" required
                        @change="formGasto._touched.id_categoria_fk_gasto = true"
                        :class="formGasto._touched.id_categoria_fk_gasto && !id_categoria_fk_gasto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => c.tipo_categoria && c.tipo_categoria.toLowerCase() === 'gasto')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_categoria_fk_gasto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_categoria_fk_gasto[0]"></p>
                    </template>
                    <small x-show="!errors.id_categoria_fk_gasto" class="text-xs text-gray-500 block mt-1" :class="formGasto._touched.id_categoria_fk_gasto && !id_categoria_fk_gasto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="descripcion_gasto" rows="3" maxlength="255"
                        @input="formGasto._touched.descripcion_gasto = true"
                        @blur="formGasto._touched.descripcion_gasto = true"
                        :class="formGasto._touched.descripcion_gasto && descripcion_gasto.length > 250 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"></textarea>
                    <template x-if="errors.descripcion_gasto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion_gasto[0]"></p>
                    </template>
                    <small x-show="!errors.descripcion_gasto" class="text-xs text-gray-500 block mt-1" :class="formGasto._touched && formGasto._touched.descripcion_gasto && descripcion_gasto.length >= 250 ? 'text-red-500' : ''">Máximo 250 caracteres.</small>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Gasto -->
        <x-admin.edit-modal modalName="isGastoEditModalOpen" title="Editar Gasto" formId="formEditGasto" itemToEdit="gastoToEdit" maxWidth="max-w-2xl">
            <template x-if="gastoToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-effect="if (gastoToEdit && isGastoEditModalOpen) { $nextTick(() => { gastoToEdit.id_proyecto_fk = gastoToEdit.proyecto?.id_proyecto_pk || ''; gastoToEdit.id_categoria_fk = gastoToEdit.categoria?.id_categoria_pk || ''; // Normalizar fecha para <input type=date>
                    (function(){
                        try {
                            var raw = gastoToEdit.fecha || gastoToEdit.fecha_gasto || '';
                            if(!raw) return;
                            if (typeof raw === 'string' && raw.indexOf('/') !== -1) {
                                // esperar dd/mm/yyyy
                                var parts = raw.split('/').map(s => s.trim());
                                if (parts.length === 3) {
                                    var day = parts[0].padStart(2, '0');
                                    var month = parts[1].padStart(2, '0');
                                    var year = parts[2];
                                    gastoToEdit.fecha = year + '-' + month + '-' + day;
                                }
                            } else {
                                var d = new Date(raw);
                                if (!isNaN(d.getTime())) {
                                    gastoToEdit.fecha = d.toISOString().slice(0,10);
                                }
                            }
                        } catch(e) { /* noop */ }
                    })(); }); }">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                    <select x-model="gastoToEdit.id_proyecto_fk" required
                        @change="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.id_proyecto_fk = true"
                        :class="gastoToEdit._touched && gastoToEdit._touched.id_proyecto_fk && !gastoToEdit.id_proyecto_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto" :selected="gastoToEdit && (proyecto.id_proyecto_pk == (gastoToEdit.id_proyecto_fk || gastoToEdit.proyecto?.id_proyecto_pk))"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_proyecto_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_proyecto_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_proyecto_fk" class="text-xs text-gray-500 block mt-1" :class="gastoToEdit._touched && gastoToEdit._touched.id_proyecto_fk && !gastoToEdit.id_proyecto_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="gastoToEdit.nombre" required maxlength="100"
                        @input="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.nombre = true"
                        @blur="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.nombre = true"
                        :class="gastoToEdit._touched && gastoToEdit._touched.nombre && !gastoToEdit.nombre ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.nombre">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.nombre[0]"></p>
                    </template>
                    <small x-show="!errors.nombre" class="text-xs text-gray-500 block mt-1" :class="gastoToEdit._touched && gastoToEdit._touched.nombre && (gastoToEdit.nombre === '' || gastoToEdit.nombre.length >= 100) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="gastoToEdit.fecha" required
                        @change="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.fecha = true"
                        :class="gastoToEdit._touched && gastoToEdit._touched.fecha && !gastoToEdit.fecha ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.fecha">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.fecha[0]"></p>
                    </template>
                    <small x-show="!errors.fecha" class="text-xs text-gray-500 block mt-1" :class="gastoToEdit._touched && gastoToEdit._touched.fecha && !gastoToEdit.fecha ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="text" inputmode="decimal" autocomplete="off" required
                        x-init="$nextTick(() => { if (gastoToEdit && gastoToEdit.monto) { try { var v = gastoToEdit.monto.toString(); var parts = v.split('.'); var int = (parts[0]||'').replace(/\B(?=(\d{3})+(?!\d))/g, ','); var dec = parts[1] ? parts[1].slice(0,2) : ''; if(int === '' && dec){ int = '0'; } $el.value = int + (dec ? '.'+dec : ''); } catch(e){} } })"
                    @input="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.monto = true; let raw=$event.target.value; let hasDot = raw.includes('.'); let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && (dec !== '' || hasDot)){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : ''); $event.target.value = formatted; gastoToEdit.monto = int + (dec !== undefined ? (dec !== '' ? '.'+dec : (hasDot ? '.' : '')) : '');"
                     @blur="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.monto = true; let raw=$event.target.value; let v = raw.replace(/,/g,'').replace(/[^\d.]/g,''); const parts = v.split('.'); let int = parts[0] || ''; let dec = (parts[1] !== undefined) ? parts[1].slice(0,2) : undefined; int = int.replace(/^0+(?=\d)/,''); if(int === '' && dec !== undefined && dec !== ''){ int = '0'; } const formatted = (int ? int.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '') + ((dec !== undefined && dec !== '') ? '.'+dec : ''); $event.target.value = formatted; gastoToEdit.monto = int + ((dec !== undefined && dec !== '') ? '.'+dec : '');"
                        :class="gastoToEdit._touched && gastoToEdit._touched.monto && !gastoToEdit.monto ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <template x-if="errors.monto">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.monto[0]"></p>
                    </template>
                    <small x-show="!errors.monto" class="text-xs text-gray-500 block mt-1" :class="gastoToEdit._touched && gastoToEdit._touched.monto && !gastoToEdit.monto ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="gastoToEdit.id_categoria_fk" required
                        @change="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.id_categoria_fk = true"
                        :class="gastoToEdit._touched && gastoToEdit._touched.id_categoria_fk && !gastoToEdit.id_categoria_fk ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => c.tipo_categoria && c.tipo_categoria.toLowerCase() === 'gasto')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria" :selected="gastoToEdit && (cat.id_categoria_pk == (gastoToEdit.id_categoria_fk || gastoToEdit.categoria?.id_categoria_pk))"></option>
                        </template>
                    </select>
                    <template x-if="errors.id_categoria_fk">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.id_categoria_fk[0]"></p>
                    </template>
                    <small x-show="!errors.id_categoria_fk" class="text-xs text-gray-500 block mt-1" :class="gastoToEdit._touched && gastoToEdit._touched.id_categoria_fk && !gastoToEdit.id_categoria_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="gastoToEdit.descripcion" rows="3" maxlength="255"
                        @input="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.descripcion = true"
                        @blur="gastoToEdit._touched = gastoToEdit._touched || {}; gastoToEdit._touched.descripcion = true"
                        :class="gastoToEdit._touched && gastoToEdit._touched.descripcion && gastoToEdit.descripcion.length > 250 ? 'border-red-500' : ''"
                        class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"></textarea>
                    <template x-if="errors.descripcion">
                        <p class="text-xs text-red-600 mt-1" x-text="errors.descripcion[0]"></p>
                    </template>
                    <small x-show="!errors.descripcion" class="text-xs text-gray-500 block mt-1" :class="gastoToEdit._touched && gastoToEdit._touched.descripcion && gastoToEdit.descripcion.length >= 250 ? 'text-red-500' : ''">Máximo 250 caracteres.</small>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>
        
        <x-admin.confirmation-modal modalName="isGastoDeleteModalOpen" itemToDelete="gastoToDelete" message="¿Seguro que quieres eliminar este gasto?"/>
    </div>
</div>