<div x-data="{
        isReporteModalOpen: false,
        isReporteEditModalOpen: false,
        isReporteDeleteModalOpen: false,
        reporteToEdit: null,
        reporteToDelete: null,
        reportes: [],
        loadingReportes: false,
        
        numbersReportes: [],
        currentPageReportes: 1,
        perPageReportes: 10,

        tiposVisita: [], serviciosRealizados: [], accionesRealizadas: [], ordenesServicio: [],
        searchReportes: '', ordenarPor: 'fecha', ordenarDirection: 'desc',
        filtroTipoVisita: '', filtroServicioRealizado: '', filtroAccionRealizada: '', filtroOrdenServicio: '',
        desde: '', hasta: '',
    formReporte: { _touched: {} }, new_fecha_reporte: '', new_observaciones: '', new_id_tipo_visita_fk: '', new_id_servicio_realizado_fk: '', new_id_accion_realizada_fk: '', new_id_orden_servicio_fk: '',
    formEditReporte: { _touched: {} }, edit_fecha_reporte: '', edit_observaciones: '', edit_id_tipo_visita_fk: '', edit_id_servicio_realizado_fk: '', edit_id_accion_realizada_fk: '', edit_id_orden_servicio_fk: '',
        
        paginatedReportes() {
            return this.reportes.slice(
                (this.currentPageReportes - 1) * this.perPageReportes, 
                this.currentPageReportes * this.perPageReportes
            );
        },
        totalPagesReportes() {
            return Math.ceil(this.reportes.length / this.perPageReportes);
        },
        nextPageReportes() {
            if (this.currentPageReportes < this.totalPagesReportes()) {
                this.currentPageReportes++;
            }
        },
        prevPageReportes() {
            if (this.currentPageReportes > 1) {
                this.currentPageReportes--;
            }
        },

        async fetchCatalogs(){ await window.reportesVisitaApiHandlers.fetchCatalogs(this); },
        
        async fetchReportes(){ 
            await window.reportesVisitaApiHandlers.fetchReportes(this); 
            this.numbersReportes = this.reportes; 
        },
        async storeReporte(){ 
            await window.reportesVisitaApiHandlers.storeReporte(this); 
            this.fetchReportes(); 
        },
        async updateReporte(){ 
            await window.reportesVisitaApiHandlers.updateReporte(this); 
            this.fetchReportes(); 
        },
        async deleteReporte(){ 
            await window.reportesVisitaApiHandlers.deleteReporte(this); 
            this.fetchReportes();
        },
        handleModalSubmit(e){ if(e.detail.formId==='form-reporte-visita-add') this.storeReporte(); if(e.detail.formId==='form-reporte-visita-edit') this.updateReporte(); },
        handleDelete(){ if(this.isReporteDeleteModalOpen) this.deleteReporte(); },
    openAdd(){ this.isReporteModalOpen = true; this.formReporte._touched = {}; this.new_fecha_reporte=''; this.new_observaciones=''; this.new_id_tipo_visita_fk=''; this.new_id_servicio_realizado_fk=''; this.new_id_accion_realizada_fk=''; this.new_id_orden_servicio_fk=''; },
        openEdit(rep){
        this.formEditReporte._touched = {};
        this.reporteToEdit = { ...rep };
                this.edit_fecha_reporte = rep.fecha_reporte ? rep.fecha_reporte.replace(' ', 'T').slice(0,16) : '';
                this.edit_observaciones = rep.observaciones || '';
                this.edit_id_tipo_visita_fk = rep.id_tipo_visita_fk || '';
                this.edit_id_servicio_realizado_fk = rep.id_servicio_realizado_fk || '';
                this.edit_id_accion_realizada_fk = rep.id_accion_realizada_fk || '';
                this.edit_id_orden_servicio_fk = rep.id_orden_servicio_fk || '';
                this.isReporteEditModalOpen = true;
        },
        openDelete(rep){ this.reporteToDelete = rep; this.isReporteDeleteModalOpen = true; }
}" x-init="(async()=>{ 
    await fetchCatalogs(); 
    await fetchReportes(); 
    $watch('searchReportes', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('filtroTipoVisita', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('filtroServicioRealizado', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('filtroAccionRealizada', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('filtroOrdenServicio', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('desde', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('hasta', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('ordenarPor', ()=> { fetchReportes(); currentPageReportes = 1; }); 
    $watch('ordenarDirection', ()=> { fetchReportes(); currentPageReportes = 1; }); 
})()" @keydown.escape.window="isReporteModalOpen=false; isReporteEditModalOpen=false; isReporteDeleteModalOpen=false;"
    @modal-submit.window="handleModalSubmit($event)" @confirm-delete.window="handleDelete()" class="overflow-x-auto">

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Gestión de Reportes">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
            'searchModel' => 'searchReportes',
            'ordenarOptions' => [ 'fecha' => 'Fecha' ]
            ])
            <select x-model="filtroTipoVisita"
                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todos los tipos de visita</option>
                <template x-for="tv in tiposVisita" :key="tv.id_tipo_visita_pk">
                    <option :value="tv.id_tipo_visita_pk" x-text="tv.nombre_tipo_visita || tv.nombre"></option>
                </template>
            </select>
            <select x-model="filtroServicioRealizado"
                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todos los servicios realizados</option>
                <template x-for="sr in serviciosRealizados" :key="sr.id_servicio_realizado_pk">
                    <option :value="sr.id_servicio_realizado_pk" x-text="sr.nombre_servicio || sr.nombre"></option>
                </template>
            </select>
            <select x-model="filtroAccionRealizada"
                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todas las acciones</option>
                <template x-for="ar in accionesRealizadas" :key="ar.id_accion_realizada_pk">
                    <option :value="ar.id_accion_realizada_pk" x-text="ar.nombre_accion || ar.nombre"></option>
                </template>
            </select>
            <select x-model="filtroOrdenServicio"
                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todas las órdenes</option>
                <template x-for="os in ordenesServicio" :key="os.id_orden_servicio_pk">
                    <option :value="os.id_orden_servicio_pk"
                        x-text="os.numero_orden_servicio ? (os.numero_orden_servicio) : ('OS '+os.id_orden_servicio_pk)">
                    </option>
                </template>
            </select>
            <input type="date" x-model="desde"
                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
            <input type="date" x-model="hasta"
                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
        </x-slot>

        <x-slot name="actions">
            @perm(['Gestión de reportes','Gestion de reportes','Reportes'], 'insercion')
            <button @click="openAdd()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo
                reporte</button>
            @else
            <button type="button" disabled title="Sin permiso para crear reportes"
                class="bg-green-400 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm opacity-60 cursor-not-allowed">Nuevo
                reporte</button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left">Fecha</th>
                        <th class="py-2 px-4 text-left">Observaciones</th>
                        <th class="py-2 px-4 text-left">Tipo de Visita</th>
                        <th class="py-2 px-4 text-left">Servicio Realizado</th>
                        <th class="py-2 px-4 text-left">Acción Realizada</th>
                        <th class="py-2 px-4 text-left">Orden de Servicio</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loadingReportes">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular"><i
                                    class="fas fa-spinner fa-spin mr-2"></i> Cargando reportes...</td>
                        </tr>
                    </template>
                    <template x-if="!loadingReportes && reportes.length===0">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">No hay reportes
                                registrados</td>
                        </tr>
                    </template>
                    <template x-if="!loadingReportes && reportes.length>0">
                        <template x-for="rep in paginatedReportes()" :key="rep.id_reportes_pk">
                            <tr class="border-b dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="rep.fecha_reporte"></td>
                                <td class="py-2 px-4" x-text="rep.observaciones"></td>
                                <td class="py-2 px-4" x-text="rep.tipo_visita"></td>
                                <td class="py-2 px-4" x-text="rep.servicio_realizado"></td>
                                <td class="py-2 px-4" x-text="rep.accion_realizada"></td>
                                <td class="py-2 px-4" x-text="rep.orden_servicio"></td>
                                <td class="py-2 px-4 flex items-center gap-2">
                                    <a :href="`{{ route('admin.formato-reporte') }}?id_reporte=${rep.id_reportes_pk}&fecha_reporte=${encodeURIComponent(rep.fecha_reporte||'')}&observaciones=${encodeURIComponent(rep.observaciones||'')}&tipo_visita=${encodeURIComponent(rep.tipo_visita||'')}&servicio_realizado=${encodeURIComponent(rep.servicio_realizado||'')}&accion_realizada=${encodeURIComponent(rep.accion_realizada||'')}&orden_servicio=${encodeURIComponent(rep.orden_servicio||'')}`"
                                        target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center justify-center text-xs h-9 px-3 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 nunito-regular">
                                        <i class="fas fa-eye mr-1"></i> Ver detalles
                                    </a>
                                    @perm(['Gestión de reportes','Gestion de reportes','Reportes'], 'actualizacion')
                                    <a href="#" @click.prevent="openEdit(rep)"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    @else
                                    <span class="text-blue-300 cursor-not-allowed" title="Sin permiso para editar"><i
                                            class="fas fa-edit"></i></span>
                                    @endperm
                                    @perm(['Gestión de reportes','Gestion de reportes','Reportes'], 'eliminacion')
                                    <a href="#" @click.prevent="openDelete(rep)"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
            <template x-if="loadingReportes">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando reportes...
                </div>
            </template>
            <template x-if="!loadingReportes && reportes.length===0">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular">
                    No hay reportes registrados</div>
            </template>
            <template x-if="!loadingReportes && reportes.length>0">
                <template x-for="rep in paginatedReportes()" :key="rep.id_reportes_pk">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-black dark:border-gray-800 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="space-y-1">
                                <div class="text-sm text-gray-600 dark:text-gray-300 nunito-bold">Fecha</div>
                                <div class="nunito-regular text-gray-900 dark:text-gray-100" x-text="rep.fecha_reporte">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span
                                    class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Observaciones:</span>
                                <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="rep.observaciones"></span>
                            </div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Tipo de
                                    Visita:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="rep.tipo_visita"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Servicio
                                    Realizado:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="rep.servicio_realizado"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Acción
                                    Realizada:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="rep.accion_realizada"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Orden de
                                    Servicio:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="rep.orden_servicio"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a :href="`{{ route('admin.formato-reporte') }}?id_reporte=${rep.id_reportes_pk}&fecha_reporte=${encodeURIComponent(rep.fecha_reporte||'')}&observaciones=${encodeURIComponent(rep.observaciones||'')}&tipo_visita=${encodeURIComponent(rep.tipo_visita||'')}&servicio_realizado=${encodeURIComponent(rep.servicio_realizado||'')}&accion_realizada=${encodeURIComponent(rep.accion_realizada||'')}&orden_servicio=${encodeURIComponent(rep.orden_servicio||'')}`"
                                target="_blank" rel="noopener noreferrer"
                                class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-eye"></i> Ver detalles
                            </a>
                            @perm(['Gestión de reportes','Gestion de reportes','Reportes'], 'actualizacion')
                            <button @click="openEdit(rep)"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para editar"
                                class="px-3 py-1 text-xs bg-blue-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Gestión de reportes','Gestion de reportes','Reportes'], 'eliminacion')
                            <button @click="openDelete(rep)"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button type="button" disabled title="Sin permiso para eliminar"
                                class="px-3 py-1 text-xs bg-red-400 text-white rounded opacity-60 cursor-not-allowed flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <div x-show="reportes.length > perPageReportes"
        class="mt-6 flex flex-col items-center w-full text-gray-700 dark:text-gray-200">
        <div class="mb-2">
            <span
                class="inline-block text-sm text-gray-700 dark:text-gray-200 bg-white/90 dark:bg-gray-800/60 px-4 py-1 rounded-full shadow-sm">
                Mostrando
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="(currentPageReportes - 1) * perPageReportes + 1"></strong>
                a
                <strong class="font-medium mx-1 text-gray-900 dark:text-white"
                    x-text="Math.min(currentPageReportes * perPageReportes, reportes.length)"></strong>
                de
                <strong class="font-medium mx-1 text-gray-900 dark:text-white" x-text="reportes.length"></strong>
                resultados
            </span>
        </div>

        <div
            class="flex items-center gap-3 bg-white border border-gray-200 p-2 rounded-lg shadow-sm dark:bg-gray-900/80 dark:border-gray-800">
            <button @click="prevPageReportes()" :disabled="currentPageReportes === 1"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors disabled:opacity-50 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Anterior</span>
            </button>

            <div class="flex items-center gap-1">
                <template
                    x-for="page in Array.from({length: totalPagesReportes()}, (_, i) => i + 1).slice(Math.max(0, currentPageReportes - 3), currentPageReportes + 2)"
                    :key="page">
                    <button @click="currentPageReportes = page"
                        class="px-3 py-1 rounded-md text-sm font-medium transition transform text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        :class="page === currentPageReportes ? 'bg-blue-600 text-white' : ''">
                        <span x-text="page"></span>
                    </button>
                </template>
            </div>

            <button @click="nextPageReportes()" :disabled="currentPageReportes === totalPagesReportes()"
                class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span>Siguiente</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <x-admin.form-modal class="nunito-bold" modalName="isReporteModalOpen" title="Nuevo Reporte"
        submitLabel="Guardar Reporte" maxWidth="max-w-2xl" formId="form-reporte-visita-add">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium nunito-bold">Fecha de Reporte</label>
                <input type="datetime-local" x-model="new_fecha_reporte" x-ref="new_fecha_reporte"
                    @input="formReporte._touched.fecha = true" @blur="formReporte._touched.fecha = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formReporte._touched && formReporte._touched.fecha && !new_fecha_reporte ? 'border-red-500' : ''" />
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formReporte._touched && formReporte._touched.fecha && !new_fecha_reporte ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div></div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium nunito-bold">Observaciones</label>
                <textarea x-model="new_observaciones" maxlength="255" rows="2"
                    @input="formReporte._touched.observaciones = true" @blur="formReporte._touched.observaciones = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formReporte._touched && formReporte._touched.observaciones && (new_observaciones === '' || (new_observaciones && new_observaciones.length >= 255)) ? 'border-red-500' : ''"></textarea>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formReporte._touched && formReporte._touched.observaciones && (new_observaciones === '' || (new_observaciones && new_observaciones.length >= 255)) ? 'text-red-500' : ''">Requerido.
                    Máximo 255 caracteres.</small>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Tipo de Visita</label>
                <select x-model="new_id_tipo_visita_fk" @change="formReporte._touched.tipo = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formReporte._touched && formReporte._touched.tipo && !new_id_tipo_visita_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="tv in tiposVisita" :key="tv.id_tipo_visita_pk">
                        <option :value="tv.id_tipo_visita_pk" x-text="tv.nombre_tipo_visita || tv.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formReporte._touched && formReporte._touched.tipo && !new_id_tipo_visita_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Servicio Realizado</label>
                <select x-model="new_id_servicio_realizado_fk" @change="formReporte._touched.servicio = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formReporte._touched && formReporte._touched.servicio && !new_id_servicio_realizado_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="sr in serviciosRealizados" :key="sr.id_servicio_realizado_pk">
                        <option :value="sr.id_servicio_realizado_pk" x-text="sr.nombre_servicio || sr.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formReporte._touched && formReporte._touched.servicio && !new_id_servicio_realizado_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Acción Realizada</label>
                <select x-model="new_id_accion_realizada_fk" @change="formReporte._touched.accion = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formReporte._touched && formReporte._touched.accion && !new_id_accion_realizada_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="ar in accionesRealizadas" :key="ar.id_accion_realizada_pk">
                        <option :value="ar.id_accion_realizada_pk" x-text="ar.nombre_accion || ar.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formReporte._touched && formReporte._touched.accion && !new_id_accion_realizada_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label class="block text-sm font medium nunito-bold">Orden de Servicio</label>
                <select x-model="new_id_orden_servicio_fk" @change="formReporte._touched.orden = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formReporte._touched && formReporte._touched.orden && !new_id_orden_servicio_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="os in ordenesServicio" :key="os.id_orden_servicio_pk">
                        <option :value="os.id_orden_servicio_pk"
                            x-text="os.numero_orden_servicio ? (os.numero_orden_servicio) : ('OS '+os.id_orden_servicio_pk)">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formReporte._touched && formReporte._touched.orden && !new_id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isReporteEditModalOpen" title="Editar Reporte"
        itemToEdit="reporteToEdit" maxWidth="max-w-2xl" formId="form-reporte-visita-edit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium nunito-bold">Fecha de Reporte</label>
                <input type="datetime-local" x-model="edit_fecha_reporte" x-ref="edit_fecha_reporte"
                    @input="formEditReporte._touched.fecha = true" @blur="formEditReporte._touched.fecha = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formEditReporte._touched && formEditReporte._touched.fecha && !edit_fecha_reporte ? 'border-red-500' : ''" />
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formEditReporte._touched && formEditReporte._touched.fecha && !edit_fecha_reporte ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div></div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium nunito-bold">Observaciones</label>
                <textarea x-model="edit_observaciones" maxlength="500" rows="2"
                    @input="formEditReporte._touched.observaciones = true"
                    @blur="formEditReporte._touched.observaciones = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formEditReporte._touched && formEditReporte._touched.observaciones && (edit_observaciones === '' || (edit_observaciones && edit_observaciones.length >= 500)) ? 'border-red-500' : ''"></textarea>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formEditReporte._touched && formEditReporte._touched.observaciones && (edit_observaciones === '' || (edit_observaciones && edit_observaciones.length >= 500)) ? 'text-red-500' : ''">Requerido.
                    Máximo 500 caracteres.</small>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Tipo de Visita</label>
                <select x-model="edit_id_tipo_visita_fk" @change="formEditReporte._touched.tipo = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formEditReporte._touched && formEditReporte._touched.tipo && !edit_id_tipo_visita_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="tv in tiposVisita" :key="tv.id_tipo_visita_pk">
                        <option :value="tv.id_tipo_visita_pk" x-text="tv.nombre_tipo_visita || tv.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formEditReporte._touched && formEditReporte._touched.tipo && !edit_id_tipo_visita_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Servicio Realizado</label>
                <select x-model="edit_id_servicio_realizado_fk" @change="formEditReporte._touched.servicio = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formEditReporte._touched && formEditReporte._touched.servicio && !edit_id_servicio_realizado_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="sr in serviciosRealizados" :key="sr.id_servicio_realizado_pk">
                        <option :value="sr.id_servicio_realizado_pk" x-text="sr.nombre_servicio || sr.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formEditReporte._touched && formEditReporte._touched.servicio && !edit_id_servicio_realizado_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Acción Realizada</label>
                <select x-model="edit_id_accion_realizada_fk" @change="formEditReporte._touched.accion = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formEditReporte._touched && formEditReporte._touched.accion && !edit_id_accion_realizada_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="ar in accionesRealizadas" :key="ar.id_accion_realizada_pk">
                        <option :value="ar.id_accion_realizada_pk" x-text="ar.nombre_accion || ar.nombre"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formEditReporte._touched && formEditReporte._touched.accion && !edit_id_accion_realizada_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label class="block text-sm font medium nunito-bold">Orden de Servicio</label>
                <select x-model="edit_id_orden_servicio_fk" @change="formEditReporte._touched.orden = true"
                    class="w-full border rounded px-3 py-2 nunito-regular"
                    :class="formEditReporte._touched && formEditReporte._touched.orden && !edit_id_orden_servicio_fk ? 'border-red-500' : ''">
                    <option value="">Seleccione...</option>
                    <template x-for="os in ordenesServicio" :key="os.id_orden_servicio_pk">
                        <option :value="os.id_orden_servicio_pk"
                            x-text="os.numero_orden_servicio ? (os.numero_orden_servicio) : ('OS '+os.id_orden_servicio_pk)">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formEditReporte._touched && formEditReporte._touched.orden && !edit_id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isReporteDeleteModalOpen" itemToDelete="reporteToDelete"
        message="¿Estás seguro de que quieres eliminar el reporte?" />

</div>