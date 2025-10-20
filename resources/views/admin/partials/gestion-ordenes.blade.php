<div x-data="gestionOrdenes('{{ route('admin.detalle-orden') }}')" x-init="init()" @modal-submit.window="
        if ($event.detail.formId === 'orden-form' || $event.detail.formId === 'orden-edit-form') {
            submitOrden();
        }
    " @keydown.window.escape="isModalOpen = false; isEditModalOpen = false; isDeleteModalOpen = false"
    @confirm-delete.window="
        if (isDeleteModalOpen) {
            performDeleteOrden();
        }
    ">
    <!-- Título de la página, ahora fuera del componente de tabla -->
    <h2 class="text-2xl text-gray-800 dark:text-gray-200 nunito-bold mb-4">Lista de Órdenes de Servicio</h2>

    <!-- Componente responsive que manejará la tabla y las tarjetas -->
    <x-responsive-table class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
        <x-slot name="filters">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Reutilizamos el partial de filtros generales para mantener consistencia con gestión de usuarios --}}
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

                {{-- Select dinámico para filtrar por técnico (las opciones provienen de Alpine: tecnicosDisponibles) --}}
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
            <button @click="openCreateOrden()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm w-full sm:w-auto"
                :disabled="saving">
                <span x-show="!saving">Nueva Orden</span>
                <span x-show="saving" class="flex items-center justify-center gap-2"><i
                        class="fas fa-spinner fa-spin"></i>
                    Guardando...</span>
            </button>
        </x-slot>

        <x-slot name="table">
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Orden</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Solicitud</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Técnico</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Estado</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Cliente</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Recepción</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Inicio</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Fin</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Cotización</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Observaciones</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Diag. Cliente</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Diag. Técnico</th>
                            <th class="py-2 px-3 text-left text-gray-900 dark:text-gray-200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingOrdenes">
                            <tr>
                                <td colspan="13" class="py-2 text-center text-gray-600 dark:text-gray-300"><i
                                        class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td>
                            </tr>
                        </template>
                        <template x-if="!loadingOrdenes && filteredOrdenes().length === 0">
                            <tr>
                                <td colspan="13" class="py-2 text-center text-gray-600 dark:text-gray-300">No se
                                    encontraron órdenes.</td>
                            </tr>
                        </template>
                        <template x-for="orden in filteredOrdenes()" :key="orden.id">
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
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200"
                                    x-text="orden.id_cotizacion ? orden.id_cotizacion : '—'"></td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200 max-w-[12rem] truncate"
                                    :title="orden.observaciones || ''" x-text="orden.observaciones || '—'"></td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200 max-w-[10rem] truncate"
                                    :title="orden.diagnostico_cliente || ''" x-text="orden.diagnostico_cliente || '—'">
                                </td>
                                <td class="py-1 px-2 text-gray-900 dark:text-gray-200 max-w-[10rem] truncate"
                                    :title="orden.diagnostico_tecnico || ''" x-text="orden.diagnostico_tecnico || '—'">
                                </td>
                                <td class="py-1 px-2">
                                    <div class="flex gap-2 items-center">
                                        <a :href="detalleUrl(orden.id)" target="_blank"
                                            class="inline-flex items-center justify-center text-xs px-2 py-1 rounded bg-emerald-500 text-white hover:bg-emerald-600">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                        <a href="#" @click.prevent="openEditOrden(orden)"
                                            class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                        <a href="#" @click.prevent="openDeleteOrden(orden)"
                                            class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
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
                <template x-if="!loadingOrdenes && filteredOrdenes().length === 0">
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">No se encontraron órdenes.</div>
                </template>
                <template x-for="orden in filteredOrdenes()" :key="orden.id">
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
                            <button @click.prevent="openEditOrden(orden)"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button @click.prevent="openDeleteOrden(orden)"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot>
    </x-responsive-table>

    <!-- Modal Nueva Orden -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nueva Orden" submitLabel="Guardar Orden"
        formId="orden-form" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="id_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">Solicitud</label>
                <select id="id_solicitud" name="id_solicitud" x-model="formOrden.id_solicitud_servicio_fk"
                    :disabled="loadingCatalogos.solicitudes"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
            </div>
            <div>
                <label for="id_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold"> Técnico</label>
                <select id="id_tecnico" name="id_tecnico" x-model="formOrden.id_tecnico_fk"
                    :disabled="loadingCatalogos.tecnicos"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
            </div>
            <div>
                <label for="fecha_recepcion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Recepción</label>
                <input type="date" id="fecha_recepcion" name="fecha_recepcion" x-model="formOrden.fecha_recepcion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <template x-if="errors.fecha_recepcion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_recepcion[0]"></p>
                </template>
            </div>
            <div>
                <label for="id_estado_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
                <select id="id_estado_orden" name="id_estado_orden" x-model="formOrden.id_estado_orden_servicio_fk"
                    :disabled="loadingCatalogos.estadosOrden"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
                <input type="date" id="fecha_inicio" name="fecha_inicio" x-model="formOrden.fecha_inicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <template x-if="errors.fecha_inicio">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_inicio[0]"></p>
                </template>
            </div>
            <div>
                <label for="fecha_finalizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Finalización</label>
                <input type="date" id="fecha_finalizacion" name="fecha_finalizacion"
                    x-model="formOrden.fecha_finalizacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <template x-if="errors.fecha_finalizacion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_finalizacion[0]"></p>
                </template>
            </div>
            <div class="col-span-2">
                <label for="observaciones"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="2" x-model="formOrden.observaciones"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <template x-if="errors.observaciones">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.observaciones[0]"></p>
                </template>
            </div>
            <div class="col-span-2">
                <label for="diagnostico_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico
                    del Técnico</label>
                <textarea id="diagnostico_tecnico" name="diagnostico_tecnico" rows="2"
                    x-model="formOrden.diagnostico_tecnico"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <template x-if="errors.diagnostico_tecnico">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_tecnico[0]"></p>
                </template>
            </div>
            <div class="col-span-2">
                <label for="diagnostico_cliente" class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico
                    del Cliente</label>
                <textarea id="diagnostico_cliente" name="diagnostico_cliente" rows="2"
                    x-model="formOrden.diagnostico_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <template x-if="errors.diagnostico_cliente">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_cliente[0]"></p>
                </template>
            </div>

            <div>
                <label for="id_cotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">
                    Cotización</label>
                <select id="id_cotizacion" name="id_cotizacion" x-model="formOrden.id_cotizacion_fk"
                    :disabled="loadingCatalogos.cotizaciones"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Seleccione...</option>
                    <template x-for="cot in cotizacionesOptions" :key="cot.value">
                        <option :value="cot.value" x-text="cot.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.cotizaciones">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando cotizaciones...</span>
                </div>
                <template x-if="errors.id_cotizacion_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_cotizacion_fk[0]"></p>
                </template>
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Orden -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Orden" itemToEdit="ordenToEdit"
        formId="orden-edit-form" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl"
        minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="edit_id_solicitud"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Solicitud</label>
                <select id="edit_id_solicitud" name="edit_id_solicitud" x-model="formOrden.id_solicitud_servicio_fk"
                    :disabled="loadingCatalogos.solicitudes"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
            </div>
            <div>
                <label for="edit_id_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold">
                    Técnico</label>
                <select id="edit_id_tecnico" name="edit_id_tecnico" x-model="formOrden.id_tecnico_fk"
                    :disabled="loadingCatalogos.tecnicos"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
            </div>
            <div>
                <label for="edit_fecha_recepcion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Recepción</label>
                <input type="date" id="edit_fecha_recepcion" name="edit_fecha_recepcion"
                    x-model="formOrden.fecha_recepcion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <template x-if="errors.fecha_recepcion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_recepcion[0]"></p>
                </template>
            </div>
            <div>
                <label for="edit_id_estado_orden"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
                <select id="edit_id_estado_orden" name="edit_id_estado_orden"
                    x-model="formOrden.id_estado_orden_servicio_fk" :disabled="loadingCatalogos.estadosOrden"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
                <input type="date" id="edit_fecha_inicio" name="edit_fecha_inicio" x-model="formOrden.fecha_inicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <template x-if="errors.fecha_inicio">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_inicio[0]"></p>
                </template>
            </div>
            <div>
                <label for="edit_fecha_finalizacion" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Finalización</label>
                <input type="date" id="edit_fecha_finalizacion" name="edit_fecha_finalizacion"
                    x-model="formOrden.fecha_finalizacion"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                <template x-if="errors.fecha_finalizacion">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.fecha_finalizacion[0]"></p>
                </template>
            </div>
            <div class="col-span-2">
                <label for="edit_observaciones"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Observaciones</label>
                <textarea id="edit_observaciones" name="edit_observaciones" rows="2" x-model="formOrden.observaciones"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <template x-if="errors.observaciones">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.observaciones[0]"></p>
                </template>
            </div>
            <div class="col-span-2">
                <label for="edit_diagnostico_tecnico"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico del Técnico</label>
                <textarea id="edit_diagnostico_tecnico" name="edit_diagnostico_tecnico" rows="2"
                    x-model="formOrden.diagnostico_tecnico"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <template x-if="errors.diagnostico_tecnico">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_tecnico[0]"></p>
                </template>
            </div>
            <div class="col-span-2">
                <label for="edit_diagnostico_cliente"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Diagnóstico del Cliente</label>
                <textarea id="edit_diagnostico_cliente" name="edit_diagnostico_cliente" rows="2"
                    x-model="formOrden.diagnostico_cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2"></textarea>
                <template x-if="errors.diagnostico_cliente">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.diagnostico_cliente[0]"></p>
                </template>
            </div>

            <div>
                <label for="edit_id_cotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">
                    Cotización</label>
                <select id="edit_id_cotizacion" name="edit_id_cotizacion" x-model="formOrden.id_cotizacion_fk"
                    :disabled="loadingCatalogos.cotizaciones"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Seleccione...</option>
                    <template x-for="cot in cotizacionesOptions" :key="cot.value">
                        <option :value="cot.value" x-text="cot.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2"
                    x-show="loadingCatalogos.cotizaciones">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Cargando cotizaciones...</span>
                </div>
                <template x-if="errors.id_cotizacion_fk">
                    <p class="text-xs text-red-600 mt-1" x-text="errors.id_cotizacion_fk[0]"></p>
                </template>
            </div>
        </div>
    </x-admin.edit-modal>

    <!-- Modal Confirmar Eliminación Orden -->
    <x-admin.confirmation-modal modal-name="isDeleteModalOpen" title="Eliminar Orden de Servicio"
        item-to-delete="ordenToDelete" item-name-property="id"
        message="¿Estás seguro de que deseas eliminar la orden ID" />
</div>