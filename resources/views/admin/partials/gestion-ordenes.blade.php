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
    <div class="overflow-x-auto">
        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h2 class="text-2xl text-gray-800 dark:text-gray-200 nunito-bold">Lista de Órdenes de Servicio</h2>
            </x-slot>
            <x-slot name="filtros">
                <div class="flex flex-wrap items-center gap-3">
                    <input type="text" x-model="searchOrden" placeholder="Buscar..."
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                    <select x-model="tecnicoOrden"
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                        <option value="">Todos los técnicos</option>
                        <template x-for="tecnico in tecnicosDisponibles" :key="tecnico.value">
                            <option :value="tecnico.value" x-text="tecnico.label"></option>
                        </template>
                    </select>
                    <select x-model="ordenarPor"
                        class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 sm:min-w-[14rem] md:min-w-[16rem] shrink-0 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                        <option value="fecha_recepcion">Ordenar por Fecha Recepción</option>
                        <option value="id">Ordenar por Orden</option>
                        <option value="fecha_inicio">Ordenar por Fecha Inicio</option>
                        <option value="fecha_finalizacion">Ordenar por Fecha Finalización</option>
                    </select>
                </div>
            </x-slot>
            <x-slot name="boton">
                <button @click="openCreateOrden()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm"
                    :disabled="saving">
                    <span x-show="!saving">Nueva Orden</span>
                    <span x-show="saving" class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i>
                        Guardando...</span>
                </button>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Orden</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Solicitud
                            </th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Técnico</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Estado</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Cliente</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Fecha Recepción
                            </th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Fecha Inicio</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Fecha Finalización</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Cotización</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Observaciones</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Diag. Cliente</th>
                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Diag. Técnico</th>

                            <th class="py-2 px-4 text-left text-gray-900 dark:text-gray-200 nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingOrdenes">
                            <tr>
                                <td colspan="13"
                                    class="py-4 px-4 text-center text-gray-600 dark:text-gray-300 nunito-regular">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando órdenes de servicio...
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loadingOrdenes && filteredOrdenes().length === 0">
                            <tr>
                                <td colspan="13"
                                    class="py-4 px-4 text-center text-gray-600 dark:text-gray-300 nunito-regular">
                                    No se encontraron órdenes de servicio.
                                </td>
                            </tr>
                        </template>
                        <template x-for="orden in filteredOrdenes()" :key="orden.id">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">
                                    <span class="text-xs text-gray-500 nunito-bold" x-text="orden.numero || '—'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">
                                    <span class="text-xs text-gray-500 nunito-bold" x-text="orden.numero_solicitud || '—'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">
                                    <span class="text-xs text-gray-500 nunito-bold" x-text="orden.tecnico_nombre || '—'"></span>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="orden.estado || '—'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="orden.cliente_nombre || '—'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular"
                                    x-text="orden.fecha_recepcion || '—'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="orden.fecha_inicio || '—'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular" x-text="orden.fecha_finalizacion || '—'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular">
                                    <div class="flex flex-col">
                                        <span x-text="orden.id_cotizacion ? orden.id_cotizacion : '—'"></span>
                                    </div>
                                </td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular max-w-[16rem] truncate" :title="orden.observaciones || ''" x-text="orden.observaciones || '—'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular max-w-[14rem] truncate" :title="orden.diagnostico_cliente || ''" x-text="orden.diagnostico_cliente || '—'"></td>
                                <td class="py-2 px-4 text-gray-900 dark:text-gray-200 nunito-regular max-w-[14rem] truncate" :title="orden.diagnostico_tecnico || ''" x-text="orden.diagnostico_tecnico || '—'"></td>

                                <td class="py-2 px-4">
                                    <div class="flex gap-2 items-center">
                                        <a :href="detalleUrl(orden.id)" target="_blank"
                                            class="inline-flex items-center justify-center text-xs px-2 py-1 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 nunito-regular">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                        <a href="#" @click.prevent="openEditOrden(orden)"
                                            class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                        <a href="#" @click.prevent="openDeleteOrden(orden)"
                                            class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <tr x-show="expandedRows[orden.id]" x-transition
                                class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <td colspan="13" class="py-3 px-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Estado:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.estado || '—'"></span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Fecha
                                                Inicio:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.fecha_inicio || '—'"></span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Fecha
                                                Finalización:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.fecha_finalizacion || '—'"></span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Cliente:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.cliente_nombre || '—'"></span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Contacto:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.contacto_valor ? (orden.contacto_valor + (orden.contacto_tipo ? ' (' + orden.contacto_tipo + ')' : '')) : '—'"></span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Cotización:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.id_cotizacion ? orden.id_cotizacion : '—'"></span>
                                        </div>
                                        <div class="md:col-span-2 lg:col-span-3">
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Observaciones:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.observaciones || '—'"></span>
                                        </div>
                                        <div class="md:col-span-2 lg:col-span-3">
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Diagnóstico
                                                Técnico:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.diagnostico_tecnico || '—'"></span>
                                        </div>
                                        <div class="md:col-span-2 lg:col-span-3">
                                            <span
                                                class="font-semibold text-gray-600 dark:text-gray-300 nunito-bold">Diagnóstico
                                                Cliente:</span>
                                            <span class="ml-2 text-gray-900 dark:text-gray-200 nunito-regular"
                                                x-text="orden.diagnostico_cliente || '—'"></span>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        </template>
                </table>
            </div>
        </x-admin.tabla-crud>
    </div>

    <!-- Modal Nueva Orden -->
    <x-admin.form-modal class="nunito-bold" modalName="isModalOpen" title="Nueva Orden" submitLabel="Guardar Orden"
        formId="orden-form" maxWidth="max-w-lg xl:max-w-2xl 2xl:max-w-3xl" minHeight="min-h-[400px] xl:min-h-[600px]">
        <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
            <div>
                <label for="id_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">ID
                    Solicitud</label>
                <select id="id_solicitud" name="id_solicitud" x-model="formOrden.id_solicitud_servicio_fk"
                    :disabled="loadingCatalogos.solicitudes"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
                <label for="id_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold">ID Técnico</label>
                <select id="id_tecnico" name="id_tecnico" x-model="formOrden.id_tecnico_fk"
                    :disabled="loadingCatalogos.tecnicos"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2" x-show="loadingCatalogos.estadosOrden">
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
                <label for="id_cotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">ID
                    Cotización</label>
                <select id="id_cotizacion" name="id_cotizacion" x-model="formOrden.id_cotizacion_fk"
                    :disabled="loadingCatalogos.cotizaciones"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Sin cotización</option>
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
                <label for="edit_id_solicitud" class="block text-sm font-medium text-gray-700 nunito-bold">ID
                    Solicitud</label>
                <select id="edit_id_solicitud" name="edit_id_solicitud" x-model="formOrden.id_solicitud_servicio_fk"
                    :disabled="loadingCatalogos.solicitudes"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
                <label for="edit_id_tecnico" class="block text-sm font-medium text-gray-700 nunito-bold">ID
                    Técnico</label>
                <select id="edit_id_tecnico" name="edit_id_tecnico" x-model="formOrden.id_tecnico_fk"
                    :disabled="loadingCatalogos.tecnicos"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
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
                <label for="edit_id_estado_orden" class="block text-sm font-medium text-gray-700 nunito-bold">Estado</label>
                <select id="edit_id_estado_orden" name="edit_id_estado_orden" x-model="formOrden.id_estado_orden_servicio_fk"
                    :disabled="loadingCatalogos.estadosOrden"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Estado por defecto</option>
                    <template x-for="opt in estadosOrdenOptions" :key="opt.value">
                        <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                </select>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2" x-show="loadingCatalogos.estadosOrden">
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
                <label for="edit_id_cotizacion" class="block text-sm font-medium text-gray-700 nunito-bold">ID
                    Cotización</label>
                <select id="edit_id_cotizacion" name="edit_id_cotizacion" x-model="formOrden.id_cotizacion_fk"
                    :disabled="loadingCatalogos.cotizaciones"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="">Sin cotización</option>
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