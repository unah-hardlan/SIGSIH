<div x-data="{
    // --- Control de Pestañas ---
    tab: 'proyectos',

    // --- Estado para PROYECTOS ---
    isProyectoModalOpen: false,
    isProyectoEditModalOpen: false,
    isProyectoDeleteModalOpen: false,
    proyectoToEdit: null,
    proyectoToDelete: null,
    proyectos: [],
    loadingProyectos: false,
    newProyecto: { nombre_proyecto: '', fecha_inicio_proyecto: '', fecha_estimada_fin_proyecto: '', fecha_finalizacion_proyecto: null, descripcion_proyecto: '', id_orden_servicio_fk: null, id_estado_proyecto_fk: null },
    filtroProyecto: '',
    ordenarPorProyecto: '',

    // --- Estado para INGRESOS ---
    isIngresoModalOpen: false,
    isIngresoEditModalOpen: false,
    isIngresoDeleteModalOpen: false,
    ingresoToEdit: null,
    ingresoToDelete: null,
    ingresos: [],
    loadingIngresos: false,
    newIngreso: { id_proyecto_fk: null, nombre: '', fecha: '', monto: '', id_categoria_fk: null, descripcion: '' },
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
    newGasto: { id_proyecto_fk: null, nombre: '', fecha: '', monto: '', id_categoria_fk: null, descripcion: '' },
    filtroGasto: '',
    ordenarPorGasto: '',
    
    // --- Catálogos (para los <select>) ---
    catalogoEstadosProyecto: [],
    catalogoOrdenesServicio: [],
    catalogoProyectos: [],
    catalogoCategoriasIngreso: [],
    catalogoCategoriasGasto: [],

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
        await window.catalogosApiHandlers.fetchCategoriasIngreso(this);
        await window.catalogosApiHandlers.fetchCategoriasGasto(this);
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
@include('partials.persist-tab', ['tabKey' => 'admin-proyectos-tab'])
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()">

    <ul class="flex border-b border-gray-200 dark:border-gray-700 nunito-bold mb-6">
        <li @click="tab='proyectos'" :class="tab==='proyectos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-200 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Proyectos</li>
        <li @click="tab='movimientos'" :class="tab==='movimientos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-200 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Movimientos</li>
    </ul>

    {{-- ==================== PESTAÑA DE PROYECTOS ==================== --}}
    <div x-show="tab==='proyectos'">
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Gestión de Proyectos">
            <x-slot name="filters">@include('partials.filtros-generales', [ 'searchModel' => 'filtroProyecto', 'ordenarOptions' => [ 'nombre_proyecto' => 'Nombre', 'fecha_inicio_proyecto' => 'Fecha Inicio' ] ])</x-slot>
            <x-slot name="actions">
                 <div class="flex flex-col sm:flex-row gap-2">
                    <button @click="isProyectoModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo Proyecto</button>
                    <a href="{{ url('/admin/reportes-header?modulo=Proyectos') }}" target="_blank" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center justify-center gap-2 text-sm"><i class="fas fa-file-alt"></i> Generar Reporte</a>
                </div>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left">Nombre</th>
                            <th class="py-2 px-4 text-left">Fecha Inicio</th>
                            <th class="py-2 px-4 text-left">Fecha Fin Estimada</th>
                            <th class="py-2 px-4 text-left">Fecha Fin Real</th>
                            <th class="py-2 px-4 text-left">Descripción</th>
                            <th class="py-2 px-4 text-left">Orden Servicio</th>
                            <th class="py-2 px-4 text-left">Estado</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingProyectos"><tr><td colspan="8" class="text-center p-4 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando...</td></tr></template>
                        <template x-if="!loadingProyectos && proyectos.length === 0"><tr><td colspan="8" class="text-center p-4 text-gray-500">No hay registros.</td></tr></template>
                        <template x-for="proyecto in proyectos" :key="proyecto.id_proyecto_pk">
                            <tr class="border-b dark:border-gray-700">
                                <td class="py-2 px-4" x-text="proyecto.nombre_proyecto"></td>
                                <td class="py-2 px-4" x-text="proyecto.fecha_inicio_proyecto"></td>
                                <td class="py-2 px-4" x-text="proyecto.fecha_estimada_fin_proyecto"></td>
                                <td class="py-2 px-4" x-text="proyecto.fecha_finalizacion_proyecto"></td>
                                <td class="py-2 px-4" x-text="proyecto.descripcion_proyecto"></td>
                                <td class="py-2 px-4" x-text="proyecto.orden_servicio ? proyecto.orden_servicio.codigo : 'N/A'"></td>
                                <td class="py-2 px-4"><span class="px-2 py-1 rounded text-xs" x-text="proyecto.estado ? proyecto.estado.nombre_estado_proyecto : 'Sin Estado'"></span></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="isProyectoEditModalOpen = true; proyectoToEdit = JSON.parse(JSON.stringify(proyecto))" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isProyectoDeleteModalOpen = true; proyectoToDelete = { id_proyecto_pk: proyecto.id_proyecto_pk, nombre_proyecto: proyecto.nombre_proyecto }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-slot>
        </x-responsive-table>
    </div>

    {{-- ==================== PESTAÑA DE MOVIMIENTOS ==================== --}}
    <div x-show="tab==='movimientos'" x-cloak class="space-y-8">
        <!-- CRUD de Ingresos -->
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Ingresos">
            <x-slot name="filters">@include('partials.filtros-generales', [ 'searchModel' => 'filtroIngreso', 'ordenarOptions' => [ 'nombre' => 'Nombre', 'fecha' => 'Fecha', 'monto' => 'Monto' ] ])</x-slot>
            <x-slot name="actions"><button @click="isIngresoModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular text-sm">Agregar Ingreso</button></x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left">Nombre</th>
                            <th class="py-2 px-4 text-left">Proyecto</th>
                            <th class="py-2 px-4 text-left">Fecha</th>
                            <th class="py-2 px-4 text-left">Monto</th>
                            <th class="py-2 px-4 text-left">Categoría</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingIngresos"><tr><td colspan="6" class="text-center p-4 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando ingresos...</td></tr></template>
                        <template x-if="!loadingIngresos && ingresos.length === 0"><tr><td colspan="6" class="text-center p-4 text-gray-500">No hay ingresos registrados.</td></tr></template>
                        <template x-for="ingreso in ingresos" :key="ingreso.id_ingreso_pk">
                            <tr class="border-b dark:border-gray-700">
                                <td class="py-2 px-4" x-text="ingreso.nombre"></td>
                                <td class="py-2 px-4" x-text="ingreso.proyecto ? ingreso.proyecto.nombre_proyecto : 'N/A'"></td>
                                <td class="py-2 px-4" x-text="ingreso.fecha"></td>
                                <td class="py-2 px-4" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(ingreso.monto)"></td>
                                <td class="py-2 px-4" x-text="ingreso.categoria ? ingreso.categoria.nombre_categoria : 'N/A'"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="isIngresoEditModalOpen = true; ingresoToEdit = JSON.parse(JSON.stringify(ingreso))" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isIngresoDeleteModalOpen = true; ingresoToDelete = { id_ingreso_pk: ingreso.id_ingreso_pk, nombre: ingreso.nombre }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-slot>
        </x-responsive-table>
        
        <!-- CRUD de Gastos -->
        <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Gastos">
            <x-slot name="filters">@include('partials.filtros-generales', [ 'searchModel' => 'filtroGasto', 'ordenarOptions' => [ 'nombre' => 'Nombre', 'fecha' => 'Fecha', 'monto' => 'Monto' ] ])</x-slot>
            <x-slot name="actions"><button @click="isGastoModalOpen = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg nunito-regular text-sm">Agregar Gasto</button></x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left">Nombre</th>
                            <th class="py-2 px-4 text-left">Proyecto</th>
                            <th class="py-2 px-4 text-left">Fecha</th>
                            <th class="py-2 px-4 text-left">Monto</th>
                            <th class="py-2 px-4 text-left">Categoría</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingGastos"><tr><td colspan="6" class="text-center p-4 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando gastos...</td></tr></template>
                        <template x-if="!loadingGastos && gastos.length === 0"><tr><td colspan="6" class="text-center p-4 text-gray-500">No hay gastos registrados.</td></tr></template>
                        <template x-for="gasto in gastos" :key="gasto.id_gasto_pk">
                            <tr class="border-b dark:border-gray-700">
                                <td class="py-2 px-4" x-text="gasto.nombre"></td>
                                <td class="py-2 px-4" x-text="gasto.proyecto ? gasto.proyecto.nombre_proyecto : 'N/A'"></td>
                                <td class="py-2 px-4" x-text="gasto.fecha"></td>
                                <td class="py-2 px-4" x-text="new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(gasto.monto)"></td>
                                <td class="py-2 px-4" x-text="gasto.categoria ? gasto.categoria.nombre_categoria : 'N/A'"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a href="#" @click.prevent="isGastoEditModalOpen = true; gastoToEdit = JSON.parse(JSON.stringify(gasto))" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="#" @click.prevent="isGastoDeleteModalOpen = true; gastoToDelete = { id_gasto_pk: gasto.id_gasto_pk, nombre: gasto.nombre }" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-slot>
        </x-responsive-table>
    </div>

    {{-- ==================== TODOS LOS MODALES DE LA PÁGINA ==================== --}}
    <div>
        <!-- Modales para Proyectos -->
        <x-admin.form-modal modalName="isProyectoModalOpen" title="Nuevo Proyecto" submitLabel="Guardar Proyecto" formId="formProyecto" maxWidth="max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium">Nombre</label><input type="text" x-model="newProyecto.nombre_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Orden de Servicio</label><select x-model="newProyecto.id_orden_servicio_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="os in catalogoOrdenesServicio" :key="os.id"><option :value="os.id" x-text="os.codigo"></option></template></select></div>
                <div><label class="block text-sm font-medium">Fecha de Inicio</label><input type="date" x-model="newProyecto.fecha_inicio_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha Fin Estimada</label><input type="date" x-model="newProyecto.fecha_estimada_fin_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha Fin Real</label><input type="date" x-model="newProyecto.fecha_finalizacion_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Estado del Proyecto</label><select x-model="newProyecto.id_estado_proyecto_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="estado in catalogoEstadosProyecto" :key="estado.id"><option :value="estado.id" x-text="estado.nombre"></option></template></select></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Descripción</label><textarea x-model="newProyecto.descripcion_proyecto" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
            </div>
        </x-admin.form-modal>
        <x-admin.edit-modal modalName="isProyectoEditModalOpen" title="Editar Proyecto" formId="formEditProyecto" itemToEdit="proyectoToEdit" maxWidth="max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-if="proyectoToEdit">
                <div><label class="block text-sm font-medium">Nombre</label><input type="text" x-model="proyectoToEdit.nombre_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Orden de Servicio</label><select x-model="proyectoToEdit.id_orden_servicio_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="os in catalogoOrdenesServicio" :key="os.id"><option :value="os.id" x-text="os.codigo"></option></template></select></div>
                <div><label class="block text-sm font-medium">Fecha de Inicio</label><input type="date" x-model="proyectoToEdit.fecha_inicio_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha Fin Estimada</label><input type="date" x-model="proyectoToEdit.fecha_estimada_fin_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha Fin Real</label><input type="date" x-model="proyectoToEdit.fecha_finalizacion_proyecto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Estado del Proyecto</label><select x-model="proyectoToEdit.id_estado_proyecto_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="estado in catalogoEstadosProyecto" :key="estado.id"><option :value="estado.id" x-text="estado.nombre"></option></template></select></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Descripción</label><textarea x-model="proyectoToEdit.descripcion_proyecto" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
            </div>
        </x-admin.edit-modal>
        <x-admin.confirmation-modal modalName="isProyectoDeleteModalOpen" itemToDelete="proyectoToDelete" message="¿Seguro que quieres eliminar este proyecto?"/>

        <!-- Modales para Ingresos -->
        <x-admin.form-modal modalName="isIngresoModalOpen" title="Nuevo Ingreso" submitLabel="Guardar Ingreso" formId="formIngreso" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium">Proyecto</label><select x-model="newIngreso.id_proyecto_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk"><option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option></template></select></div>
                <div><label class="block text-sm font-medium">Nombre</label><input type="text" x-model="newIngreso.nombre" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha</label><input type="date" x-model="newIngreso.fecha" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Monto</label><input type="number" step="0.01" x-model="newIngreso.monto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Categoría</label><select x-model="newIngreso.id_categoria_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="cat in catalogoCategoriasIngreso" :key="cat.id_categoria_pk"><option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option></template></select></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Descripción</label><textarea x-model="newIngreso.descripcion" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
            </div>
        </x-admin.form-modal>
        <x-admin.edit-modal modalName="isIngresoEditModalOpen" title="Editar Ingreso" formId="formEditIngreso" itemToEdit="ingresoToEdit" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-if="ingresoToEdit">
                <div><label class="block text-sm font-medium">Proyecto</label><select x-model="ingresoToEdit.id_proyecto_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk"><option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option></template></select></div>
                <div><label class="block text-sm font-medium">Nombre</label><input type="text" x-model="ingresoToEdit.nombre" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha</label><input type="date" x-model="ingresoToEdit.fecha" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Monto</label><input type="number" step="0.01" x-model="ingresoToEdit.monto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Categoría</label><select x-model="ingresoToEdit.id_categoria_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="cat in catalogoCategoriasIngreso" :key="cat.id_categoria_pk"><option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option></template></select></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Descripción</label><textarea x-model="ingresoToEdit.descripcion" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
            </div>
        </x-admin.edit-modal>
        <x-admin.confirmation-modal modalName="isIngresoDeleteModalOpen" itemToDelete="ingresoToDelete" message="¿Seguro que quieres eliminar este ingreso?"/>

        <!-- Modales para Gastos -->
        <x-admin.form-modal modalName="isGastoModalOpen" title="Nuevo Gasto" submitLabel="Guardar Gasto" formId="formGasto" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium">Proyecto</label><select x-model="newGasto.id_proyecto_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk"><option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option></template></select></div>
                <div><label class="block text-sm font-medium">Nombre</label><input type="text" x-model="newGasto.nombre" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha</label><input type="date" x-model="newGasto.fecha" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Monto</label><input type="number" step="0.01" x-model="newGasto.monto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Categoría</label><select x-model="newGasto.id_categoria_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="cat in catalogoCategoriasGasto" :key="cat.id_categoria_pk"><option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option></template></select></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Descripción</label><textarea x-model="newGasto.descripcion" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
            </div>
        </x-admin.form-modal>
        <x-admin.edit-modal modalName="isGastoEditModalOpen" title="Editar Gasto" formId="formEditGasto" itemToEdit="gastoToEdit" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-if="gastoToEdit">
                <div><label class="block text-sm font-medium">Proyecto</label><select x-model="gastoToEdit.id_proyecto_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="proyecto in catalogoProyectos" :key="proyecto.id_proyecto_pk"><option :value="proyecto.id_proyecto_pk" x-text="proyecto.nombre_proyecto"></option></template></select></div>
                <div><label class="block text-sm font-medium">Nombre</label><input type="text" x-model="gastoToEdit.nombre" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Fecha</label><input type="date" x-model="gastoToEdit.fecha" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div><label class="block text-sm font-medium">Monto</label><input type="number" step="0.01" x-model="gastoToEdit.monto" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Categoría</label><select x-model="gastoToEdit.id_categoria_fk" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"><option value="">Seleccione...</option><template x-for="cat in catalogoCategoriasGasto" :key="cat.id_categoria_pk"><option :value="cat.id_categoria_pk" x-text="cat.nombre_categoria"></option></template></select></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium">Descripción</label><textarea x-model="gastoToEdit.descripcion" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300"></textarea></div>
            </div>
        </x-admin.edit-modal>
        <x-admin.confirmation-modal modalName="isGastoDeleteModalOpen" itemToDelete="gastoToDelete" message="¿Seguro que quieres eliminar este gasto?"/>
    </div>