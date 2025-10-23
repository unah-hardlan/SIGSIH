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
    ordenarPor: '',

    // --- Estado para INGRESOS ---
    isIngresoModalOpen: false,
    isIngresoEditModalOpen: false,
    isIngresoDeleteModalOpen: false,
    ingresoToEdit: null,
    ingresoToDelete: null,
    ingresos: [],
    loadingIngresos: false,
    nombre_ingreso: '',
    fecha_ingreso: '',
    monto_ingreso: '',
    descripcion_ingreso: '',
    id_proyecto_fk_ingreso: '',
    id_categoria_fk_ingreso: '',
    filtroIngreso: '',
    ordenarPorIngreso: '',

    // --- Estado para GASTOS ---
    isGastoModalOpen: false,
    isGastoEditModalOpen: false,
    isGastoDeleteModalOpen: false,
    gastoToEdit: null,
    gastoToDelete: null,
    gastos: [],
    loadingGastos: false,
    nombre_gasto: '',
    fecha_gasto: '',
    monto_gasto: '',
    descripcion_gasto: '',
    id_proyecto_fk_gasto: '',
    id_categoria_fk_gasto: '',
    filtroGasto: '',
    ordenarPorGasto: '',
    
    // --- Catálogos (para los <select>) ---
    catalogoEstadosProyecto: [],
    catalogoOrdenesServicio: [],
    catalogoProyectos: [],
    catalogoCategorias: [],

    // --- Lógica de la API ---
    async fetchProyectos() { await window.proyectosApiHandlers.fetchProyectos(this); },
    async submitProyecto() { await window.proyectosApiHandlers.submitProyecto(this); },
    async updateProyecto() { await window.proyectosApiHandlers.updateProyecto(this); },
    async deleteProyecto() { await window.proyectosApiHandlers.deleteProyecto(this); },
    async fetchIngresos() { await window.ingresosApiHandlers.fetchIngresos(this); },
    async submitIngreso() { await window.ingresosApiHandlers.submitIngreso(this); },
    async updateIngreso() { await window.ingresosApiHandlers.updateIngreso(this); },
    async deleteIngreso() { await window.ingresosApiHandlers.deleteIngreso(this); },
    async fetchGastos() { await window.gastosApiHandlers.fetchGastos(this); },
    async submitGasto() { await window.gastosApiHandlers.submitGasto(this); },
    async updateGasto() { await window.gastosApiHandlers.updateGasto(this); },
    async deleteGasto() { await window.gastosApiHandlers.deleteGasto(this); },
    async fetchCatalogos() {
        await window.catalogosApiHandlers.fetchEstadosProyecto(this);
        await window.catalogosApiHandlers.fetchOrdenesServicio(this);
        await window.catalogosApiHandlers.fetchProyectos(this);
        await window.catalogosApiHandlers.fetchCategorias(this);
    },

    // Open Nuevo Proyecto modal ensuring catalogs (ordenes) are loaded first
    async openNuevoProyecto() {
        try {
            if (!this.catalogoOrdenesServicio || this.catalogoOrdenesServicio.length === 0) {
                await window.catalogosApiHandlers.fetchOrdenesServicio(this);
            }
        } catch (e) {
            // ignore - modal will still open
        }
        this.isProyectoModalOpen = true;
    },

    // Utility: formato de fecha legible para tablas y campos (YYYY-MM-DD)
    formatDate(date) {
        if (!date) return 'N/A';
        try {
            if (typeof date === 'string' && date.indexOf('/') !== -1) {
                // formato dd/mm/yyyy
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

    // --- Manejadores de Eventos Globales ---
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
$watch('filtroProyecto', () => fetchProyectos());
$watch('ordenarPorProyecto', () => fetchProyectos());
$watch('filtroIngreso', () => fetchIngresos());
$watch('ordenarPorIngreso', () => fetchIngresos());
$watch('filtroGasto', () => fetchGastos());
$watch('ordenarPorGasto', () => fetchGastos());
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
                <button @click.prevent="openNuevoProyecto()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">
                    Nuevo Proyecto
                </button>
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
                                <td colspan="5" class="text-center p-4 text-gray-500 nunito-regular">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando proyectos...
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingProyectos && proyectos.length === 0">
                            <tr>
                                <td colspan="5" class="text-center p-4 text-gray-500 nunito-regular">
                                    No hay proyectos registrados
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingProyectos && proyectos.length > 0">
                            <template x-for="(proyecto, index) in proyectos" :key="proyecto.id_proyecto_pk">
                                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                    :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === proyectos.length - 1 }">
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
                    <template x-for="proyecto in proyectos" :key="proyecto.id_proyecto_pk">
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
    </div>

    {{-- ==================== PESTAÑA DE MOVIMIENTOS ==================== --}}
    <div x-show="tab==='movimientos'" x-cloak class="space-y-8">
        <!-- CRUD de Ingresos -->
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                @include('partials.filtros-generales', [
                    'searchModel' => 'filtroIngreso',
                    'ordenarModel' => 'ordenarPorIngreso',
                    'ordenarOptions' => [
                        'nombre' => 'Nombre',
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
                            <template x-for="(ingreso, index) in ingresos" :key="ingreso.id_ingresos_pk">
                                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                    :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === ingresos.length - 1 }">
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
                    <template x-for="ingreso in ingresos" :key="ingreso.id_ingresos_pk">
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
                            <template x-for="(gasto, index) in gastos" :key="gasto.id_gasto_pk">
                                <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular"
                                    :class="{ 'border-t-0': index === 0, 'last:border-b-0': index === gastos.length - 1 }">
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
                    <template x-for="gasto in gastos" :key="gasto.id_gasto_pk">
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
    </div>

    {{-- ==================== MODALES ==================== --}}
    <div>
        <!-- Modal Nuevo Proyecto -->
        <x-admin.form-modal modalName="isProyectoModalOpen" title="Nuevo Proyecto" submitLabel="Guardar Proyecto" formId="formProyecto" maxWidth="max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="nombre_proyecto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Orden de Servicio</label>
                    <select x-model="id_orden_servicio_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="os in catalogoOrdenesServicio" :key="os.id_orden_servicio_pk">
                            <option :value="os.id_orden_servicio_pk" x-text="os.codigo_orden || os.numero_orden_servicio || os.nombre_orden || 'OS-' + os.id_orden_servicio_pk"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Inicio</label>
                    <input type="date" x-model="fecha_inicio_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Estimada</label>
                    <input type="date" x-model="fecha_estimada_fin_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Real</label>
                    <input type="date" x-model="fecha_finalizacion_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Estado del Proyecto</label>
                    <select x-model="id_estado_proyecto_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="estado in catalogoEstadosProyecto" :key="estado.id_estado_proyecto_pk">
                            <option :value="estado.id_estado_proyecto_pk" x-text="estado.nombre"></option>
                        </template>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="descripcion_proyecto" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
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
                    <input type="text" x-model="itemToEdit.nombre_proyecto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Orden de Servicio</label>
                    <select x-model="itemToEdit.id_orden_servicio_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="os in catalogoOrdenesServicio" :key="os.id_orden_servicio_pk">
                            <option :value="os.id_orden_servicio_pk" x-text="os.codigo_orden || os.numero_orden_servicio || os.nombre_orden || 'OS-' + os.id_orden_servicio_pk" :selected="itemToEdit && (os.id_orden_servicio_pk == (itemToEdit.id_orden_servicio_fk || itemToEdit.orden_servicio?.id_orden_servicio_pk))"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha de Inicio</label>
                    <input type="date" x-model="itemToEdit.fecha_inicio_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Estimada</label>
                    <input type="date" x-model="itemToEdit.fecha_estimada_fin_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Fin Real</label>
                    <input type="date" x-model="itemToEdit.fecha_finalizacion_proyecto" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Estado del Proyecto</label>
                    <select x-model="itemToEdit.id_estado_proyecto_fk" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="estado in catalogoEstadosProyecto" :key="estado.id_estado_proyecto_pk">
                            <option :value="estado.id_estado_proyecto_pk" x-text="estado.nombre" :selected="itemToEdit && (estado.id_estado_proyecto_pk == (itemToEdit.id_estado_proyecto_fk || itemToEdit.estado_proyecto?.id_estado_proyecto_pk))"></option>
                        </template>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="itemToEdit.descripcion_proyecto" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
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
                    <select x-model="id_proyecto_fk_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="nombre_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="fecha_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="number" step="0.01" x-model="monto_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="id_categoria_fk_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => !c.tipo_categoria || c.tipo_categoria.toLowerCase() === 'ingreso')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option>
                        </template>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="descripcion_ingreso" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                </div>
            </div>
        </x-admin.form-modal>

        <!-- Modal Editar Ingreso -->
        <x-admin.edit-modal modalName="isIngresoEditModalOpen" title="Editar Ingreso" formId="formEditIngreso" itemToEdit="ingresoToEdit" maxWidth="max-w-2xl">
            <template x-if="ingresoToEdit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Proyecto</label>
                    <select x-model="ingresoToEdit.id_proyecto_fk" x-bind:value="ingresoToEdit.id_proyecto_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto" :selected="ingresoToEdit && (proyecto.id_proyecto_pk == (ingresoToEdit.id_proyecto_fk || ingresoToEdit.proyecto?.id_proyecto_pk))"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="ingresoToEdit.nombre_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="ingresoToEdit.fecha_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="number" step="0.01" x-model="ingresoToEdit.monto_ingreso" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="ingresoToEdit.id_categoria_fk" x-bind:value="ingresoToEdit.id_categoria_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => !c.tipo_categoria || c.tipo_categoria.toLowerCase() === 'ingreso')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria" :selected="ingresoToEdit && (cat.id_categoria_pk == (ingresoToEdit.id_categoria_fk || ingresoToEdit.categoria?.id_categoria_pk))"></option>
                        </template>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="ingresoToEdit.descripcion_ingreso" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
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
                    <select x-model="id_proyecto_fk_gasto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="nombre_gasto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="fecha_gasto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="number" step="0.01" x-model="monto_gasto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="id_categoria_fk_gasto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => c.tipo_categoria && c.tipo_categoria.toLowerCase() === 'gasto')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option>
                        </template>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="descripcion_gasto" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
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
                    <select x-model="gastoToEdit.id_proyecto_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk">
                            <option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto" :selected="gastoToEdit && (proyecto.id_proyecto_pk == (gastoToEdit.id_proyecto_fk || gastoToEdit.proyecto?.id_proyecto_pk))"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Nombre</label>
                    <input type="text" x-model="gastoToEdit.nombre" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                    <input type="date" x-model="gastoToEdit.fecha" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Monto</label>
                    <input type="number" step="0.01" x-model="gastoToEdit.monto" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Categoría</label>
                    <select x-model="gastoToEdit.id_categoria_fk" required class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                        <option value="">Seleccione...</option>
                        <template x-for="cat in catalogoCategorias.filter(c => c.tipo_categoria && c.tipo_categoria.toLowerCase() === 'gasto')" :key="cat.id_categoria_pk">
                            <option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria" :selected="gastoToEdit && (cat.id_categoria_pk == (gastoToEdit.id_categoria_fk || gastoToEdit.categoria?.id_categoria_pk))"></option>
                        </template>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                    <textarea x-model="gastoToEdit.descripcion" rows="3" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"></textarea>
                </div>
            </div>
            </template>
        </x-admin.edit-modal>
        
        <x-admin.confirmation-modal modalName="isGastoDeleteModalOpen" itemToDelete="gastoToDelete" message="¿Seguro que quieres eliminar este gasto?"/>
    </div>
</div>