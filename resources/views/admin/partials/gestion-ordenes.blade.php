<div x-data="gestionOrdenes('{{ route('admin.detalle-orden') }}')" x-init="init()" @modal-submit.window="
        if ($event.detail.formId === 'orden-form' || $event.detail.formId === 'orden-edit-form') {
            submitOrden();
        }
    " @keydown.window.escape="isModalOpen = false; isEditModalOpen = false; isDeleteModalOpen = false; isVerMasModalOpen = false; ordenSeleccionada = null"
    @confirm-delete.window="
        if (isDeleteModalOpen) {
            performDeleteOrden();
        }
    ">
    <h2 class="text-2xl text-gray-800 dark:text-gray-200 nunito-bold mb-4">Lista de Órdenes de Servicio</h2>

    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            <div class="flex flex-wrap items-center gap-3">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchOrden',
                'filtrosSelect' => [],
                'ordenarOptions' => [
                'fecha_recepcion' => 'Fecha Recepción',
                'id' => 'Orden',
                'fecha_inicio' => 'Fecha Inicio',
                'fecha_finalizacion' => 'Fecha Finalización',
                ]
                ])

                <select x-model="tecnicoOrden"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-auto dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="">Todos los técnicos</option>
                    <template x-for="tecnico in tecnicosDisponibles" :key="tecnico.value">
                        <option :value="tecnico.value" x-text="tecnico.label"></option>
                    </template>
                </select>
            </div>
        </x-slot>

        <x-slot name="actions">
            @perm(['Órdenes de Servicios','Ordenes de Servicios','Ordenes de Servicio'], 'insercion')
            <button @click="openCreateOrden()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm w-full sm:w-auto"
                :disabled="saving">
                <span x-show="!saving">Nueva Orden</span>
                <span x-show="saving" class="flex items-center justify-center gap-2"><i
                        class="fas fa-spinner fa-spin"></i>
                    Guardando...</span>
            </button>
            @else
            <button disabled
                class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm w-full sm:w-auto cursor-not-allowed"
                title="Sin permiso para crear">
                Nueva Orden
            </button>
            @endperm
        </x-slot>

        <x-slot name="table">
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Orden</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">N° Solicitud ACF</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Técnico</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Estado</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Cliente</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Recepción</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Inicio</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Fin</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingOrdenes">
                            <tr>
                                <td colspan="9" class="py-2 text-center text-gray-600 dark:text-gray-300"><i
                                        class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td>
                            </tr>
                        </template>
                        <template x-if="!loadingOrdenes && paginatedOrdenes().length === 0">
                            <tr>
                                <td colspan="9" class="py-2 text-center text-gray-600 dark:text-gray-300">No se
                                    encontraron órdenes.</td>
                            </tr>
                        </template>
                        <template x-for="orden in paginatedOrdenes()" :key="orden.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200" x-text="orden.numero || '—'">
                                </td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200"
                                    x-text="orden.numero_solicitud || '—'"></td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200"
                                    x-text="orden.tecnico_nombre || '—'"></td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200" x-text="orden.estado || '—'">
                                </td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200"
                                    x-text="orden.cliente_nombre || '—'"></td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200"
                                    x-text="orden.fecha_recepcion || '—'"></td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200"
                                    x-text="orden.fecha_inicio || '—'"></td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200"
                                    x-text="orden.fecha_finalizacion || '—'"></td>


                                <td class="py-1 px-2">
                                    <div class="flex gap-2 items-center">
                                        <a :href="detalleUrl(orden.id)" target="_blank"
                                            class="inline-flex items-center justify-center text-xs px-2 py-1 rounded bg-emerald-500 text-white hover:bg-emerald-600" title="Ver Detalle Completo">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @perm(['Órdenes de Servicios','Ordenes de Servicios','Ordenes de Servicio'], 'actualizacion')
                                        <a href="#" @click.prevent="openEditOrden(orden)"
                                            class="text-blue-500 hover:text-blue-700" title="Editar Orden"><i class="fas fa-edit"></i></a>
                                        @else
                                        <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para editar">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                        @endperm
                                        @perm(['Órdenes de Servicios','Ordenes de Servicios','Ordenes de Servicio'], 'eliminacion')
                                        <a href="#" @click.prevent="openDeleteOrden(orden)"
                                            class="text-red-500 hover:text-red-700" title="Eliminar Orden"><i class="fas fa-trash"></i></a>
                                        @else
                                        <span class="text-gray-400 cursor-not-allowed" title="Sin permiso para eliminar">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        @endperm

                                        <button @click.prevent="openVerMasModal(orden)"
                                            class="px-3 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center gap-1">
                                            <i class="fas fa-info-circle"></i> Ver más
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-slot>

        <x-slot name="cards">
            <div class="space-y-4 px-2 sm:px-0">
                <template x-if="loadingOrdenes">
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400"><i
                            class="fas fa-spinner fa-spin mr-2"></i> Cargando...</div>
                </template>
                <template x-if="!loadingOrdenes && paginatedOrdenes().length === 0">
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">No se encontraron órdenes.</div>
                </template>
                <template x-for="orden in paginatedOrdenes()" :key="orden.id">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-black dark:border-gray-600">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Orden #<span
                                        x-text="orden.numero || '—'"></span></h3>
                                <span
                                    class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300"
                                    x-text="orden.estado || '—'"></span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 pt-2"><b>Cliente:</b> <span
                                    x-text="orden.cliente_nombre || '—'"></span></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300"><b>Técnico:</b> <span
                                    x-text="orden.tecnico_nombre || '—'"></span></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300"><b>Recepción:</b> <span
                                    x-text="orden.fecha_recepcion || '—'"></span></p>
                        </div>
                        <div
                            class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700 flex-wrap">
                            <a :href="detalleUrl(orden.id)" target="_blank"
                                class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700 flex items-center gap-1">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            @perm(['Órdenes de Servicios','Ordenes de Servicios','Ordenes de Servicio'], 'actualizacion')
                            <button @click.prevent="openEditOrden(orden)"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @else
                            <button disabled
                                class="px-3 py-1 text-xs bg-gray-300 text-gray-600 rounded cursor-not-allowed flex items-center gap-1"
                                title="Sin permiso para editar">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            @endperm
                            @perm(['Órdenes de Servicios','Ordenes de Servicios','Ordenes de Servicio'], 'eliminacion')
                            <button @click.prevent="openDeleteOrden(orden)"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @else
                            <button disabled
                                class="px-3 py-1 text-xs bg-gray-300 text-gray-600 rounded cursor-not-allowed flex items-center gap-1"
                                title="Sin permiso para eliminar">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            @endperm
                            <button @click.prevent="openVerMasModal(orden)"
                                class="px-3 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i> Ver más
                            </button>

                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-responsive-table>

    <x-pagination />

    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nueva Orden" submitLabel="Guardar Orden"
        formId="orden-form" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="id_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">N° Solicitud
                    ACF</label>
                <select id="id_solicitud" name="id_solicitud" x-model="formOrden.id_solicitud_servicio_fk"
                    @change="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.id_solicitud_servicio_fk = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.id_solicitud_servicio_fk = true)"
                    :class="(formOrdenAdd && formOrdenAdd._touched && !formOrden.id_solicitud_servicio_fk) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    :disabled="loadingCatalogos.solicitudes">
                    <option value="" disabled selected hidden>Seleccione...</option>
                    <template x-for="sol in solicitudesOptions" :key="sol.value">
                        <option :value="sol.value" x-text="sol.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.solicitudes">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando solicitudes...</span>
                </div>
                <template x-if="errors.id_solicitud_servicio_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_solicitud_servicio_fk[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenAdd._touched && formOrdenAdd._touched.id_solicitud_servicio_fk && !formOrden.id_solicitud_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="id_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold"> Técnico</label>
                <select id="id_tecnico" name="id_tecnico" x-model="formOrden.id_tecnico_fk"
                    @change="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.id_tecnico_fk = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.id_tecnico_fk = true)"
                    :class="(formOrdenAdd && formOrdenAdd._touched && !formOrden.id_tecnico_fk) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    :disabled="loadingCatalogos.tecnicos">
                    <option value="" disabled selected hidden>Seleccione...</option>
                    <template x-for="tec in tecnicosOptions" :key="tec.value">
                        <option :value="tec.value" x-text="tec.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.tecnicos">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando técnicos...</span>
                </div>
                <template x-if="errors.id_tecnico_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_tecnico_fk[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenAdd._touched && formOrdenAdd._touched.id_tecnico_fk && !formOrden.id_tecnico_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="fecha_recepcion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Recepción</label>
                <input type="datetime-local" id="fecha_recepcion" name="fecha_recepcion"
                    x-model="formOrden.fecha_recepcion"
                    @input="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.fecha_recepcion = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.fecha_recepcion = true)"
                    @blur="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.fecha_recepcion = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.fecha_recepcion = true)"
                    :class="(formOrdenAdd && formOrdenAdd._touched && !formOrden.fecha_recepcion) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                <template x-if="errors.fecha_recepcion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_recepcion[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenAdd._touched && formOrdenAdd._touched.fecha_recepcion && !formOrden.fecha_recepcion ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="id_estado_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
                <select id="id_estado_orden" name="id_estado_orden" x-model="formOrden.id_estado_orden_servicio_fk"
                    :disabled="loadingCatalogos.estadosOrden"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="">Estado por defecto</option>
                    <template x-for="opt in estadosOrdenOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.estadosOrden">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando estados...</span>
                </div>
                <template x-if="errors.id_estado_orden_servicio_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_estado_orden_servicio_fk[0]"></p>
                </template>
            </div>
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Inicio</label>
                <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" x-model="formOrden.fecha_inicio"
                    @input="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.fecha_inicio = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.fecha_inicio = true)"
                    @blur="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.fecha_inicio = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.fecha_inicio = true)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                <template x-if="errors.fecha_inicio">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_inicio[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional.</small>
            </div>
            <div>
                <label for="fecha_finalizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Finalización</label>
                <input type="datetime-local" id="fecha_finalizacion" name="fecha_finalizacion"
                    x-model="formOrden.fecha_finalizacion"
                    @input="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.fecha_finalizacion = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.fecha_finalizacion = true)"
                    @blur="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.fecha_finalizacion = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.fecha_finalizacion = true)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                <template x-if="errors.fecha_finalizacion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_finalizacion[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional.</small>
            </div>
            <div class="col-span-2">
                <label for="observaciones"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="2" x-model="formOrden.observaciones"
                    @input="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.observaciones = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.observaciones = true); (typeof validateTexto === 'function') ? formOrden.observaciones = validateTexto($event.target.value, 'observaciones', 500) : formOrden.observaciones = $event.target.value"
                    @blur="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.observaciones = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.observaciones = true)"
                    maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </textarea>
                <template x-if="errors.observaciones">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.observaciones[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional. Máximo 500 caracteres.</small>
                <small class="block mt-1 text-sm text-gray-500" x-text="(formOrden.observaciones || '').length + ' / 500'"></small>
            </div>
            <div class="col-span-2">
                <label for="diagnostico_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico
                    del Técnico</label>
                <textarea id="diagnostico_tecnico" name="diagnostico_tecnico" rows="2"
                    x-model="formOrden.diagnostico_tecnico"
                    @input="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.diagnostico_tecnico = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.diagnostico_tecnico = true); (typeof validateTexto === 'function') ? formOrden.diagnostico_tecnico = validateTexto($event.target.value, 'diagnostico_tecnico', 500) : formOrden.diagnostico_tecnico = $event.target.value"
                    @blur="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.diagnostico_tecnico = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.diagnostico_tecnico = true)"
                    maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </textarea>
                <template x-if="errors.diagnostico_tecnico">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_tecnico[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional. Máximo 500 caracteres.</small>
                <small class="block mt-1 text-sm text-gray-500" x-text="(formOrden.diagnostico_tecnico || '').length + ' / 500'"></small>
            </div>
            <div class="col-span-2">
                <label for="diagnostico_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico
                    del Cliente</label>
                <textarea id="diagnostico_cliente" name="diagnostico_cliente" rows="2"
                    x-model="formOrden.diagnostico_cliente"
                    @input="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.diagnostico_cliente = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.diagnostico_cliente = true); (typeof validateTexto === 'function') ? formOrden.diagnostico_cliente = validateTexto($event.target.value, 'diagnostico_cliente', 500) : formOrden.diagnostico_cliente = $event.target.value"
                    @blur="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.diagnostico_cliente = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.diagnostico_cliente = true)"
                    maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </textarea>
                <template x-if="errors.diagnostico_cliente">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_cliente[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional. Máximo 500 caracteres.</small>
                <small class="block mt-1 text-sm text-gray-500" x-text="(formOrden.diagnostico_cliente || '').length + ' / 500'"></small>
            </div>

            <div>
                <label for="id_cotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">
                    Codigo de Cotización</label>
                <select id="id_cotizacion" name="id_cotizacion" x-model="formOrden.id_cotizacion_fk"
                    @change="(formOrdenAdd && formOrdenAdd._touched) ? formOrdenAdd._touched.id_cotizacion_fk = true : (formOrdenAdd = formOrdenAdd || { _touched: {} }, formOrdenAdd._touched.id_cotizacion_fk = true)"
                    :class="(formOrdenAdd && formOrdenAdd._touched && !formOrden.id_cotizacion_fk) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    :disabled="loadingCatalogos.cotizaciones || !formOrden.id_solicitud_servicio_fk || ((cotizacionesOptions || []).length === 0)">
                    <option value="" disabled selected hidden>Seleccione...</option>
                    <template x-for="cot in cotizacionesOptions" :key="cot.value">
                        <option :value="cot.value" x-text="cot.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="!loadingCatalogos.cotizaciones && (!formOrden.id_solicitud_servicio_fk || ((cotizacionesOptions || []).length === 0))">
                    <i class="fas fa-info-circle"></i>
                    <span
                        x-text="!formOrden.id_solicitud_servicio_fk ? 'Seleccione una solicitud para cargar cotizaciones' : 'No hay cotizaciones disponibles para este cliente'"></span>
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.cotizaciones">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando cotizaciones...</span>
                </div>
                <template x-if="errors.id_cotizacion_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_cotizacion_fk[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenAdd._touched && formOrdenAdd._touched.id_cotizacion_fk && !formOrden.id_cotizacion_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="calificacion_servicio"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Calificación del Servicio</label>
                <select id="calificacion_servicio" name="calificacion_servicio"
                    x-model="formOrden.calificacion_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <option value="" selected>Sin calificar</option>
                    <option value="excelente">Excelente</option>
                    <option value="bueno">Bueno</option>
                    <option value="regular">Regular</option>
                    <option value="deficiente">Deficiente</option>
                </select>
                <template x-if="errors.calificacion_servicio">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.calificacion_servicio[0]"></p>
                </template>
            </div>
            <div class="col-span-2 border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Repuestos</label>
                <div class="flex gap-2 items-end mt-2">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Producto</label>
                        <select x-model="repuestosForm.id_producto_fk" @focus="fetchProducts()"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                            <option value="" disabled selected hidden>Seleccione...</option>
                            <template x-for="p in productsOptions" :key="p.value">
                                <option :value="p.value" x-text="p.label"></option>
                            </template>
                        </select>
                    </div>
                    <div class="w-28">
                        <label class="text-xs text-gray-500">Cantidad</label>
                        <input type="number" min="1" x-model.number="repuestosForm.cantidad"
                            @input="(repuestosForm && repuestosForm._touched) ? repuestosForm._touched.cantidad = true : (repuestosForm = repuestosForm || { _touched: {} }, repuestosForm._touched.cantidad = true); (typeof validateNumero === 'function') ? repuestosForm.cantidad = validateNumero($event.target.value, 'repuestos_cantidad', 6) : repuestosForm.cantidad = $event.target.value"
                            :class="(repuestosForm && repuestosForm._touched && (!repuestosForm.cantidad || repuestosForm.cantidad === '')) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    </div>
                    <div class="flex items-center gap-2">
                        <small class="text-sm text-gray-500"
                            :class="(repuestosForm && repuestosForm._touched && (!repuestosForm.cantidad || repuestosForm.cantidad === '')) ? 'text-red-600' : ''">Requerido.</small>
                        <button type="button" @click.prevent="addRepuestoToForm()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Agregar</button>
                    </div>
                </div>
                <div class="mt-3">
                    <template x-if="(formOrden.repuestos || []).length === 0">
                        <p class="text-xs text-gray-500">No hay repuestos agregados.</p>
                    </template>
                    <template x-for="(r, idx) in formOrden.repuestos || []" :key="idx">
                        <div
                            class="flex items-center justify-between gap-3 mt-2 bg-gray-50 dark:bg-gray-800 p-2 rounded">
                            <div class="text-sm">
                                <div x-text="r.producto_nombre || ('#' + (r.id_producto_fk || ''))"></div>
                                <div class="text-xs text-gray-500">Cantidad: <span x-text="r.cantidad"></span></div>
                            </div>
                            <div>
                                <button type="button" @click.prevent="removeRepuestoFromForm(idx)"
                                    class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Orden" itemToEdit="ordenToEdit"
        formId="orden-edit-form" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl"
        minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_id_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">N° Solicitud
                    ACF</label>
                <select id="edit_id_solicitud" name="edit_id_solicitud" x-model="formOrden.id_solicitud_servicio_fk"
                    @change="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.id_solicitud_servicio_fk = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.id_solicitud_servicio_fk = true)"
                    :class="(formOrdenEdit && formOrdenEdit._touched && !formOrden.id_solicitud_servicio_fk) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    :disabled="loadingCatalogos.solicitudes">
                    <option value="">Seleccione...</option>
                    <template x-for="sol in solicitudesOptions" :key="sol.value">
                        <option :value="sol.value" x-text="sol.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.solicitudes">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando solicitudes...</span>
                </div>
                <template x-if="errors.id_solicitud_servicio_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_solicitud_servicio_fk[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenEdit._touched && formOrdenEdit._touched.id_solicitud_servicio_fk && !formOrden.id_solicitud_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_id_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold">
                    Técnico</label>
                <select id="edit_id_tecnico" name="edit_id_tecnico" x-model="formOrden.id_tecnico_fk"
                    @change="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.id_tecnico_fk = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.id_tecnico_fk = true)"
                    :class="(formOrdenEdit && formOrdenEdit._touched && !formOrden.id_tecnico_fk) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    :disabled="loadingCatalogos.tecnicos">
                    <option value="">Seleccione...</option>
                    <template x-for="tec in tecnicosOptions" :key="tec.value">
                        <option :value="tec.value" x-text="tec.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.tecnicos">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando técnicos...</span>
                </div>
                <template x-if="errors.id_tecnico_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_tecnico_fk[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenEdit._touched && formOrdenEdit._touched.id_tecnico_fk && !formOrden.id_tecnico_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_fecha_recepcion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Recepción</label>
                <input type="datetime-local" id="edit_fecha_recepcion" name="edit_fecha_recepcion"
                    x-model="formOrden.fecha_recepcion"
                    @input="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.fecha_recepcion = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.fecha_recepcion = true)"
                    @blur="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.fecha_recepcion = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.fecha_recepcion = true)"
                    :class="(formOrdenEdit && formOrdenEdit._touched && !formOrden.fecha_recepcion) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                <template x-if="errors.fecha_recepcion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_recepcion[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenEdit._touched && formOrdenEdit._touched.fecha_recepcion && !formOrden.fecha_recepcion ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_id_estado_orden"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
                <select id="edit_id_estado_orden" name="edit_id_estado_orden"
                    x-model="formOrden.id_estado_orden_servicio_fk" :disabled="loadingCatalogos.estadosOrden"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="">Estado por defecto</option>
                    <template x-for="opt in estadosOrdenOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.estadosOrden">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando estados...</span>
                </div>
                <template x-if="errors.id_estado_orden_servicio_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_estado_orden_servicio_fk[0]"></p>
                </template>
            </div>
            <div>
                <label for="edit_fecha_inicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Inicio</label>
                <input type="datetime-local" id="edit_fecha_inicio" name="edit_fecha_inicio"
                    x-model="formOrden.fecha_inicio"
                    @input="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.fecha_inicio = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.fecha_inicio = true)"
                    @blur="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.fecha_inicio = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.fecha_inicio = true)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                <template x-if="errors.fecha_inicio">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_inicio[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional.</small>
            </div>
            <div>
                <label for="edit_fecha_finalizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Finalización</label>
                <input type="datetime-local" id="edit_fecha_finalizacion" name="edit_fecha_finalizacion"
                    x-model="formOrden.fecha_finalizacion"
                    @input="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.fecha_finalizacion = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.fecha_finalizacion = true)"
                    @blur="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.fecha_finalizacion = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.fecha_finalizacion = true)"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                <template x-if="errors.fecha_finalizacion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_finalizacion[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional.</small>
            </div>
            <div class="col-span-2">
                <label for="edit_observaciones"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Observaciones</label>
                <textarea id="edit_observaciones" name="edit_observaciones" rows="2" x-model="formOrden.observaciones"
                    @input="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.observaciones = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.observaciones = true); (typeof validateTexto === 'function') ? formOrden.observaciones = validateTexto($event.target.value, 'observaciones', 500) : formOrden.observaciones = $event.target.value"
                    @blur="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.observaciones = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.observaciones = true)"
                    maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </textarea>
                <template x-if="errors.observaciones">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.observaciones[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional. Máximo 500 caracteres.</small>
                <small class="block mt-1 text-sm text-gray-500" x-text="(formOrden.observaciones || '').length + ' / 500'"></small>
            </div>
            <div class="col-span-2">
                <label for="edit_diagnostico_tecnico"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico del Técnico</label>
                <textarea id="edit_diagnostico_tecnico" name="edit_diagnostico_tecnico" rows="2"
                    x-model="formOrden.diagnostico_tecnico"
                    @input="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.diagnostico_tecnico = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.diagnostico_tecnico = true); (typeof validateTexto === 'function') ? formOrden.diagnostico_tecnico = validateTexto($event.target.value, 'diagnostico_tecnico', 500) : formOrden.diagnostico_tecnico = $event.target.value"
                    @blur="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.diagnostico_tecnico = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.diagnostico_tecnico = true)"
                    maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </textarea>
                <template x-if="errors.diagnostico_tecnico">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_tecnico[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional. Máximo 500 caracteres.</small>
                <small class="block mt-1 text-sm text-gray-500" x-text="(formOrden.diagnostico_tecnico || '').length + ' / 500'"></small>
            </div>
            <div class="col-span-2">
                <label for="edit_diagnostico_cliente"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico del Cliente</label>
                <textarea id="edit_diagnostico_cliente" name="edit_diagnostico_cliente" rows="2"
                    x-model="formOrden.diagnostico_cliente"
                    @input="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.diagnostico_cliente = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.diagnostico_cliente = true); (typeof validateTexto === 'function') ? formOrden.diagnostico_cliente = validateTexto($event.target.value, 'diagnostico_cliente', 500) : formOrden.diagnostico_cliente = $event.target.value"
                    @blur="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.diagnostico_cliente = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.diagnostico_cliente = true)"
                    maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                </textarea>
                <template x-if="errors.diagnostico_cliente">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_cliente[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500">Opcional. Máximo 500 caracteres.</small>
                <small class="block mt-1 text-sm text-gray-500" x-text="(formOrden.diagnostico_cliente || '').length + ' / 500'"></small>
            </div>

            <div>
                <label for="edit_id_cotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">
                    Codigo de Cotización</label>
                <select id="edit_id_cotizacion" name="edit_id_cotizacion" x-model="formOrden.id_cotizacion_fk"
                    @change="(formOrdenEdit && formOrdenEdit._touched) ? formOrdenEdit._touched.id_cotizacion_fk = true : (formOrdenEdit = formOrdenEdit || { _touched: {} }, formOrdenEdit._touched.id_cotizacion_fk = true)"
                    :class="(formOrdenEdit && formOrdenEdit._touched && !formOrden.id_cotizacion_fk) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                    :disabled="loadingCatalogos.cotizaciones || !formOrden.id_solicitud_servicio_fk || ((cotizacionesOptions || []).length === 0)">
                    <option value="" disabled selected hidden>Seleccione...</option>
                    <template x-for="cot in cotizacionesOptions" :key="cot.value">
                        <option :value="cot.value" x-text="cot.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="!loadingCatalogos.cotizaciones && (!formOrden.id_solicitud_servicio_fk || ((cotizacionesOptions || []).length === 0))">
                    <i class="fas fa-info-circle"></i>
                    <span
                        x-text="!formOrden.id_solicitud_servicio_fk ? 'Seleccione una solicitud para cargar cotizaciones' : 'No hay cotizaciones disponibles para este cliente'"></span>
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.cotizaciones">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando cotizaciones...</span>
                </div>
                <template x-if="errors.id_cotizacion_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_cotizacion_fk[0]"></p>
                </template>
                <small class="block mt-1 text-sm text-gray-500"
                    :class="formOrdenEdit._touched && formOrdenEdit._touched.id_cotizacion_fk && !formOrden.id_cotizacion_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_calificacion_servicio"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Calificación del Servicio</label>
                <select id="edit_calificacion_servicio" name="edit_calificacion_servicio"
                    x-model="formOrden.calificacion_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    <option value="" selected>Sin calificar</option>
                    <option value="excelente">Excelente</option>
                    <option value="bueno">Bueno</option>
                    <option value="regular">Regular</option>
                    <option value="deficiente">Deficiente</option>
                </select>
                <template x-if="errors.calificacion_servicio">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.calificacion_servicio[0]"></p>
                </template>
            </div>
            <div class="col-span-2 border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 nunito-bold">Repuestos</label>
                <div class="flex gap-2 items-end mt-2">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Producto</label>
                        <select x-model="repuestosForm.id_producto_fk" @focus="fetchProducts()"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                            <option value="" disabled selected hidden>Seleccione...</option>
                            <template x-for="p in productsOptions" :key="p.value">
                                <option :value="p.value" x-text="p.label"></option>
                            </template>
                        </select>
                    </div>
                    <div class="w-28">
                        <label class="text-xs text-gray-500">Cantidad</label>
                        <input type="number" min="1" x-model.number="repuestosForm.cantidad"
                            @input="(repuestosForm && repuestosForm._touched) ? repuestosForm._touched.cantidad = true : (repuestosForm = repuestosForm || { _touched: {} }, repuestosForm._touched.cantidad = true); (typeof validateNumero === 'function') ? repuestosForm.cantidad = validateNumero($event.target.value, 'repuestos_cantidad', 6) : repuestosForm.cantidad = $event.target.value"
                            :class="(repuestosForm && repuestosForm._touched && (!repuestosForm.cantidad || repuestosForm.cantidad === '')) ? 'mt-1 block w-full rounded-md border-red-500 shadow-sm border focus:border-red-500 nunito-regular px-2' : 'mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2'"
                            class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2">
                    </div>
                    <div class="flex items-center gap-2">
                        <small class="text-sm text-gray-500"
                            :class="(repuestosForm && repuestosForm._touched && (!repuestosForm.cantidad || repuestosForm.cantidad === '')) ? 'text-red-600' : ''">Requerido.</small>
                        <button type="button" @click.prevent="addRepuestoToForm()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Agregar</button>
                    </div>
                </div>
                <div class="mt-3">
                    <template x-if="(formOrden.repuestos || []).length === 0">
                        <p class="text-xs text-gray-500">No hay repuestos agregados.</p>
                    </template>
                    <template x-for="(r, idx) in formOrden.repuestos || []" :key="idx">
                        <div
                            class="flex items-center justify-between gap-3 mt-2 bg-gray-50 dark:bg-gray-800 p-2 rounded">
                            <div class="text-sm">
                                <div x-text="r.producto_nombre || ('#' + (r.id_producto_fk || ''))"></div>
                                <div class="text-xs text-gray-500">Cantidad: <span x-text="r.cantidad"></span></div>
                            </div>
                            <div>
                                <button type="button" @click.prevent="removeRepuestoFromForm(idx)"
                                    class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal modal-name="isDeleteModalOpen" title="Eliminar Orden de Servicio"
        item-to-delete="ordenToDelete" item-name-property="id"
        message="¿Estás seguro de que deseas eliminar la orden ID" />


    <div x-show="isVerMasModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
        style="display: none;"
        x-cloak>

        <div x-show="isVerMasModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 backdrop-blur-none"
            x-transition:enter-end="opacity-100 backdrop-blur-md"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 backdrop-blur-md"
            x-transition:leave-end="opacity-0 backdrop-blur-none"

            class="fixed inset-0 bg-gray-900/75 backdrop-blur transition-all"

            @click="isVerMasModalOpen = false; ordenSeleccionada = null">
        </div>

        <div x-show="isVerMasModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full m-4"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">

            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 nunito-bold" id="modal-headline">
                            Detalles Adicionales (Orden #<span x-text="ordenSeleccionada?.numero || ''"></span>)
                        </h3>
                        <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                            <template x-if="ordenSeleccionada">
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <dt class="font-medium text-gray-900 dark:text-white">Código de Cotización</dt>
                                        <dd class="mt-1" x-text="(ordenSeleccionada && ordenSeleccionada.id_cotizacion) ? formatCotLabel(ordenSeleccionada.raw?.cotizacion || { id: ordenSeleccionada.id_cotizacion, fecha_cotizacion: ordenSeleccionada.raw?.fecha_cotizacion || '' }) : '—'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="font-medium text-gray-900 dark:text-white">Repuestos</dt>
                                        <dd class="mt-1" x-text="(ordenSeleccionada && ordenSeleccionada.repuestos_summary) || '—'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="font-medium text-gray-900 dark:text-white">Observaciones</dt>
                                        <dd class="mt-1" x-text="(ordenSeleccionada && ordenSeleccionada.observaciones) || '—'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="font-medium text-gray-900 dark:text-white">Diagnóstico Cliente</dt>
                                        <dd class="mt-1" x-text="(ordenSeleccionada && ordenSeleccionada.diagnostico_cliente) || '—'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="font-medium text-gray-900 dark:text-white">Diagnóstico Técnico</dt>
                                        <dd class="mt-1" x-text="(ordenSeleccionada && ordenSeleccionada.diagnostico_tecnico) || '—'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="font-medium text-gray-900 dark:text-white">Calificación</dt>
                                        <dd class="mt-1 capitalize" x-text="(ordenSeleccionada && ordenSeleccionada.calificacion_servicio) || 'Sin calificar'"></dd>
                                    </div>
                                </dl>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="isVerMasModalOpen = false; ordenSeleccionada = null" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>