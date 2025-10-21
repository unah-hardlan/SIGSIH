<div x-data="{
        // estado
        isReporteModalOpen: false,
        isReporteEditModalOpen: false,
        isReporteDeleteModalOpen: false,
        reporteToEdit: null,
        reporteToDelete: null,
        reportes: [],
        loadingReportes: false,
        // catálogos
        tiposVisita: [], serviciosRealizados: [], accionesRealizadas: [], ordenesServicio: [],
        // filtros
        searchReportes: '', ordenarPor: 'fecha', ordenarDirection: 'desc',
        filtroTipoVisita: '', filtroServicioRealizado: '', filtroAccionRealizada: '', filtroOrdenServicio: '',
        desde: '', hasta: '',
        // crear
        new_fecha_reporte: '', new_observaciones: '', new_id_tipo_visita_fk: '', new_id_servicio_realizado_fk: '', new_id_accion_realizada_fk: '', new_id_orden_servicio_fk: '',
        // editar (campos locales)
        edit_fecha_reporte: '', edit_observaciones: '', edit_id_tipo_visita_fk: '', edit_id_servicio_realizado_fk: '', edit_id_accion_realizada_fk: '', edit_id_orden_servicio_fk: '',
        // métodos
        async fetchCatalogs(){ await window.reportesVisitaApiHandlers.fetchCatalogs(this); },
        async fetchReportes(){ await window.reportesVisitaApiHandlers.fetchReportes(this); },
        async storeReporte(){ await window.reportesVisitaApiHandlers.storeReporte(this); },
        async updateReporte(){ await window.reportesVisitaApiHandlers.updateReporte(this); },
        async deleteReporte(){ await window.reportesVisitaApiHandlers.deleteReporte(this); },
        handleModalSubmit(e){ if(e.detail.formId==='form-reporte-visita-add') this.storeReporte(); if(e.detail.formId==='form-reporte-visita-edit') this.updateReporte(); },
        handleDelete(){ if(this.isReporteDeleteModalOpen) this.deleteReporte(); },
        openAdd(){ this.isReporteModalOpen = true; },
        openEdit(rep){
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
}"
x-init="(async()=>{ await fetchCatalogs(); await fetchReportes(); $watch('searchReportes', ()=> fetchReportes()); $watch('filtroTipoVisita', ()=> fetchReportes()); $watch('filtroServicioRealizado', ()=> fetchReportes()); $watch('filtroAccionRealizada', ()=> fetchReportes()); $watch('filtroOrdenServicio', ()=> fetchReportes()); $watch('desde', ()=> fetchReportes()); $watch('hasta', ()=> fetchReportes()); $watch('ordenarPor', ()=> fetchReportes()); $watch('ordenarDirection', ()=> fetchReportes()); })()"
@keydown.escape.window="isReporteModalOpen=false; isReporteEditModalOpen=false; isReporteDeleteModalOpen=false;"
@modal-submit.window="handleModalSubmit($event)"
@confirm-delete.window="handleDelete()"
class="overflow-x-auto">

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4" title="Gestión de Reportes">
        <x-slot name="filters">
            @include('partials.filtros-generales', [
                'searchModel' => 'searchReportes',
                'ordenarOptions' => [ 'fecha' => 'Fecha' ]
            ])
            <select x-model="filtroTipoVisita" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todos los tipos de visita</option>
                <template x-for="tv in tiposVisita" :key="tv.id_tipo_visita_pk">
                    <option :value="tv.id_tipo_visita_pk" x-text="tv.nombre_tipo_visita || tv.nombre"></option>
                </template>
            </select>
            <select x-model="filtroServicioRealizado" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todos los servicios realizados</option>
                <template x-for="sr in serviciosRealizados" :key="sr.id_servicio_realizado_pk">
                    <option :value="sr.id_servicio_realizado_pk" x-text="sr.nombre_servicio || sr.nombre"></option>
                </template>
            </select>
            <select x-model="filtroAccionRealizada" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todas las acciones</option>
                <template x-for="ar in accionesRealizadas" :key="ar.id_accion_realizada_pk">
                    <option :value="ar.id_accion_realizada_pk" x-text="ar.nombre_accion || ar.nombre"></option>
                </template>
            </select>
                    <select x-model="filtroOrdenServicio" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                <option value="">Todas las órdenes</option>
                                <template x-for="os in ordenesServicio" :key="os.id_orden_servicio_pk">
                                    <option :value="os.id_orden_servicio_pk" x-text="os.numero_orden_servicio ? (os.numero_orden_servicio) : ('OS '+os.id_orden_servicio_pk)"></option>
                </template>
            </select>
            <input type="date" x-model="desde" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
            <input type="date" x-model="hasta" class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
        </x-slot>

        <x-slot name="actions">
            <button @click="openAdd()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nuevo reporte</button>
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
                        <tr><td colspan="7" class="py-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando reportes...</td></tr>
                    </template>
                    <template x-if="!loadingReportes && reportes.length===0">
                        <tr><td colspan="7" class="py-8 text-center text-gray-500 nunito-regular">No hay reportes registrados</td></tr>
                    </template>
                    <template x-if="!loadingReportes && reportes.length>0">
                        <template x-for="rep in reportes" :key="rep.id_reportes_pk">
                            <tr class="border-b dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4" x-text="rep.fecha_reporte"></td>
                                <td class="py-2 px-4" x-text="rep.observaciones"></td>
                                <td class="py-2 px-4" x-text="rep.tipo_visita"></td>
                                <td class="py-2 px-4" x-text="rep.servicio_realizado"></td>
                                <td class="py-2 px-4" x-text="rep.accion_realizada"></td>
                                <td class="py-2 px-4" x-text="rep.orden_servicio"></td>
                                                <td class="py-2 px-4 flex items-center gap-2">
                                                            <a :href="`{{ route('admin.formato-reporte') }}?id_reporte=${rep.id_reportes_pk}&fecha_reporte=${encodeURIComponent(rep.fecha_reporte||'')}&observaciones=${encodeURIComponent(rep.observaciones||'')}&tipo_visita=${encodeURIComponent(rep.tipo_visita||'')}&servicio_realizado=${encodeURIComponent(rep.servicio_realizado||'')}&accion_realizada=${encodeURIComponent(rep.accion_realizada||'')}&orden_servicio=${encodeURIComponent(rep.orden_servicio||'')}`"
                                                         target="_blank"
                                                         class="inline-flex items-center justify-center text-xs h-9 px-3 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 nunito-regular">
                                                        <i class="fas fa-eye mr-1"></i> Ver detalles
                                                    </a>
                                                    <a href="#" @click.prevent="openEdit(rep)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                                    <a href="#" @click.prevent="openDelete(rep)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </x-slot>

        <x-slot name="cards">
            <template x-if="loadingReportes">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando reportes...</div>
            </template>
            <template x-if="!loadingReportes && reportes.length===0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center text-gray-500 nunito-regular">No hay reportes registrados</div>
            </template>
            <template x-if="!loadingReportes && reportes.length>0">
                <template x-for="rep in reportes" :key="rep.id_reportes_pk">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="space-y-1">
                                <div class="text-sm text-gray-600 dark:text-gray-300 nunito-bold">Fecha</div>
                                <div class="nunito-regular text-gray-900 dark:text-gray-100" x-text="rep.fecha_reporte"></div>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Observaciones:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="rep.observaciones"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Tipo de Visita:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="rep.tipo_visita"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Servicio Realizado:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="rep.servicio_realizado"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Acción Realizada:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="rep.accion_realizada"></span></div>
                            <div><span class="font-medium text-gray-600 dark:text-gray-300 nunito-bold">Orden de Servicio:</span> <span class="text-gray-900 dark:text-gray-200 nunito-regular" x-text="rep.orden_servicio"></span></div>
                        </div>
                        <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                              <a :href="`{{ route('admin.formato-reporte') }}?id_reporte=${rep.id_reportes_pk}&fecha_reporte=${encodeURIComponent(rep.fecha_reporte||'')}&observaciones=${encodeURIComponent(rep.observaciones||'')}&tipo_visita=${encodeURIComponent(rep.tipo_visita||'')}&servicio_realizado=${encodeURIComponent(rep.servicio_realizado||'')}&accion_realizada=${encodeURIComponent(rep.accion_realizada||'')}&orden_servicio=${encodeURIComponent(rep.orden_servicio||'')}`"
                                             target="_blank"
                                             class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 flex items-center gap-1 nunito-regular">
                                            <i class="fas fa-eye"></i> Ver detalles
                                        </a>
                            <button @click="openEdit(rep)" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click="openDelete(rep)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1 nunito-regular">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </template>
        </x-slot>
    </x-responsive-table>

    <!-- Modal Nuevo Reporte -->
    <x-admin.form-modal class="nunito-bold" modalName="isReporteModalOpen" title="Nuevo Reporte" submitLabel="Guardar Reporte" maxWidth="max-w-2xl" formId="form-reporte-visita-add">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium nunito-bold">Fecha de Reporte</label>
                <input type="datetime-local" x-model="new_fecha_reporte" class="w-full border rounded px-3 py-2 nunito-regular" />
            </div>
            <div></div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium nunito-bold">Observaciones</label>
                <textarea x-model="new_observaciones" rows="2" class="w-full border rounded px-3 py-2 nunito-regular"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Tipo de Visita</label>
                <select x-model="new_id_tipo_visita_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="tv in tiposVisita" :key="tv.id_tipo_visita_pk">
                        <option :value="tv.id_tipo_visita_pk" x-text="tv.nombre_tipo_visita || tv.nombre"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Servicio Realizado</label>
                <select x-model="new_id_servicio_realizado_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="sr in serviciosRealizados" :key="sr.id_servicio_realizado_pk">
                        <option :value="sr.id_servicio_realizado_pk" x-text="sr.nombre_servicio || sr.nombre"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Acción Realizada</label>
                <select x-model="new_id_accion_realizada_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="ar in accionesRealizadas" :key="ar.id_accion_realizada_pk">
                        <option :value="ar.id_accion_realizada_pk" x-text="ar.nombre_accion || ar.nombre"></option>
                    </template>
                </select>
            </div>
            <div>
        <label class="block text-sm font-medium nunito-bold">Orden de Servicio</label>
            <select x-model="new_id_orden_servicio_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="os in ordenesServicio" :key="os.id_orden_servicio_pk">
                <option :value="os.id_orden_servicio_pk" x-text="os.numero_orden_servicio ? (os.numero_orden_servicio) : ('OS '+os.id_orden_servicio_pk)"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Reporte -->
    <x-admin.edit-modal class="nunito-bold" modalName="isReporteEditModalOpen" title="Editar Reporte" itemToEdit="reporteToEdit" maxWidth="max-w-2xl" formId="form-reporte-visita-edit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium nunito-bold">Fecha de Reporte</label>
                <input type="datetime-local" x-model="edit_fecha_reporte" class="w-full border rounded px-3 py-2 nunito-regular" />
            </div>
            <div></div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium nunito-bold">Observaciones</label>
                <textarea x-model="edit_observaciones" rows="2" class="w-full border rounded px-3 py-2 nunito-regular"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Tipo de Visita</label>
                <select x-model="edit_id_tipo_visita_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="tv in tiposVisita" :key="tv.id_tipo_visita_pk">
                        <option :value="tv.id_tipo_visita_pk" x-text="tv.nombre_tipo_visita || tv.nombre"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Servicio Realizado</label>
                <select x-model="edit_id_servicio_realizado_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="sr in serviciosRealizados" :key="sr.id_servicio_realizado_pk">
                        <option :value="sr.id_servicio_realizado_pk" x-text="sr.nombre_servicio || sr.nombre"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium nunito-bold">Acción Realizada</label>
                <select x-model="edit_id_accion_realizada_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="ar in accionesRealizadas" :key="ar.id_accion_realizada_pk">
                        <option :value="ar.id_accion_realizada_pk" x-text="ar.nombre_accion || ar.nombre"></option>
                    </template>
                </select>
            </div>
            <div>
        <label class="block text-sm font-medium nunito-bold">Orden de Servicio</label>
            <select x-model="edit_id_orden_servicio_fk" class="w-full border rounded px-3 py-2 nunito-regular">
                    <option value="">Seleccione...</option>
                    <template x-for="os in ordenesServicio" :key="os.id_orden_servicio_pk">
                <option :value="os.id_orden_servicio_pk" x-text="os.numero_orden_servicio ? (os.numero_orden_servicio) : ('OS '+os.id_orden_servicio_pk)"></option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación -->
    <x-admin.confirmation-modal class="nunito-bold" modalName="isReporteDeleteModalOpen" itemToDelete="reporteToDelete" message="¿Estás seguro de que quieres eliminar el reporte?" />

</div>