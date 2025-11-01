<div x-data="Object.assign(facturasCrud(), { tab: 'facturas', formFactura: { _touched: {} }, formEditFactura: { _touched: {} }, formDetalle: { _touched: {} }, formEditDetalle: { _touched: {} } })" @include('partials.persist-tab', ['tabKey'=>
    'admin-facturas-tab', 'forceDefault' => true]) class="p-6">

    <div class="mb-6">
        <div class="sticky top-6 left-6 z-50">
            <button x-show="tab==='detalle'" x-cloak @click.prevent="setTab('facturas')"
                class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md nunito-regular text-sm shadow transition-opacity duration-150">Volver</button>
        </div>
    </div>

    <div x-show="tab==='facturas'" class="overflow-x-auto">
        <x-responsive-table title="Facturas" class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                <div class="flex flex-col gap-2 w-full">
                    <div class="flex flex-col sm:flex-row gap-2 w-full">
                        @include('partials.filtros-generales', [
                        'searchModel' => 'searchFacturas',
                        'filtrosSelect' => [
                        // Estado cargado dinámicamente desde Alpine (this.estadosFactura)
                        // Añadimos 'options' vacío para que el partial que espera 'options' no falle.

                        ],
                        'ordenarOptions' => [ 'fecha' => 'Fecha', 'total' => 'Total', 'estado_factura' => 'Estado']
                        ])
                        <!-- Inline Estado select populated by Alpine (estadosFactura) to ensure options appear here -->
                        <div class="w-full sm:w-auto">
                            <select x-model="estadoFacturaFiltro"
                                class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full sm:w-56 md:w-64 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                                <option value="">Todos los estados</option>
                                <template x-for="estado in estadosFactura"
                                    :key="estado.id || estado.id_estado_factura_pk">
                                    <option
                                        :value="estado.nombre_estado || estado.nombre || (estado.id || estado.id_estado_factura_pk)"
                                        x-text="estado.nombre_estado || estado.nombre"></option>
                                </template>                                                                                                                                                                                     
                            </select>
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="actions">
                <div class="flex flex-col gap-2 w-full">
                    <button @click="isFacturaModalOpen = true; formFactura._touched = {}"
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm">
                        Nueva Factura
                    </button>
                    <a href="/admin/reportes-header?modulo=Facturas&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <x-slot name="table">
                <table
                    class="min-w-full text-[10px] bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse break-words">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold text-[10px]">
                        <tr>

                            <th class="py-2 px-4 text-left border-0 text-[10px]">N° de Factura</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Fecha</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">OC</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Subtotal</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Impuesto</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Total</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Total Letras</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Estado</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">CAI</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Cliente</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingFacturas">
                            <tr>
                                <td colspan="12" class="py-8 text-center text-gray-500 nunito-regular"><i
                                        class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td>
                            </tr>
                        </template>
                        <template x-if="!loadingFacturas && facturas.length === 0">
                            <tr>
                                <td colspan="12" class="py-8 text-center text-gray-500 nunito-regular">Sin resultados
                                </td>
                            </tr>
                        </template>
                        <template x-for="factura in filteredFacturas" :key="factura.id || factura.id_factura_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular text-[10px]">

                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.numero"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.fecha"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.oc || '-' "></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.subtotal"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.impuesto || '0.00'"></td>
                                <!-- factura.descuento removed -->
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.total"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.total_letras || '-' ">
                                </td>
                                <td class="py-2 px-4 text-[10px] break-words">
                                    <span class="px-2 py-1 rounded text-xs font-semibold" :class="{
                                              'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': factura.estado_factura === 'Pagada',
                                              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': ['Pendiente','Emitida','Pendiente de Pago'].includes(factura.estado_factura),
                                              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': ['Cancelada','Anulada'].includes(factura.estado_factura),
                                              'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': factura.estado_factura === 'Parcialmente Pagada',
                                              'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300': !factura.estado_factura
                                          }" x-text="factura.estado_factura || 'Sin estado'"></span>
                                </td>
                                <td class="py-2 px-4" x-text="factura.cai || 'Sin CAI'"></td>
                                <td class="py-2 px-4" x-text="factura.cliente_nombre || 'Sin cliente'"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <a :href="'/admin/formato-factura/' + (factura.id || factura.id_factura_pk)"
                                        target="_blank"
                                        class="text-xs px-3 py-1 rounded bg-emerald-500 text-white hover:bg-emerald-600 nunito-regular flex items-center gap-1"><i
                                            class="fas fa-eye"></i> Ver</a>
                                    <button @click.prevent="openDetalleForFactura(factura)"
                                        class="text-gray-300 hover:text-white px-2 py-1 bg-gray-700 rounded nunito-regular text-xs">Detalles</button>
                                    <button @click.prevent="formEditFactura._touched = {}; openEditFactura(factura)"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                    <button @click.prevent="isDeleteFacturaModalOpen = true; itemToDelete = factura"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-slot>

            <x-slot name="cards">
                <template x-if="loadingFacturas">
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400"><i
                            class="fas fa-spinner fa-spin mr-2"></i> Cargando...</div>
                </template>
                <template x-if="!loadingFacturas && facturas.length === 0">
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">Sin resultados</div>
                </template>
                <template x-for="factura in facturas" :key="'card-'+(factura.id || factura.id_factura_pk)">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-black dark:border-gray-600">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Factura <span
                                        x-text="factura.numero"></span></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Fecha: <span
                                        x-text="factura.fecha"></span></p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-semibold" :class="{
                                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': factura.estado_factura === 'Pagada',
                                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': ['Pendiente','Emitida','Pendiente de Pago'].includes(factura.estado_factura),
                                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': ['Cancelada','Anulada'].includes(factura.estado_factura),
                                      'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': factura.estado_factura === 'Parcialmente Pagada',
                                      'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300': !factura.estado_factura
                                  }" x-text="factura.estado_factura || 'Sin estado'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <div>Cliente: <span class="font-medium"
                                    x-text="factura.cliente_nombre || 'Sin cliente'"></span></div>
                            <div>CAI: <span class="font-medium" x-text="factura.cai || '—'"></span></div>
                            <div>Subtotal: <span class="font-medium" x-text="factura.subtotal"></span></div>
                            <div>Total: <span class="font-medium" x-text="factura.total"></span></div>
                            <div class="col-span-2">Total en letras: <span class="font-medium"
                                    x-text="factura.total_letras || '-' "></span></div>
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a :href="'/admin/formato-factura/' + (factura.id || factura.id_factura_pk)" target="_blank"
                                class="px-3 py-1 text-xs bg-emerald-500 text-white rounded hover:bg-emerald-600 flex items-center gap-1"><i
                                    class="fas fa-eye"></i> Ver</a>
                            <button @click.prevent="formEditFactura._touched = {}; openEditFactura(factura)"
                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1"><i
                                    class="fas fa-edit"></i> Editar</button>
                            <button @click.prevent="isDeleteFacturaModalOpen = true; itemToDelete = factura"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1"><i
                                    class="fas fa-trash"></i> Eliminar</button>
                        </div>
                    </div>
                </template>
            </x-slot>
        </x-responsive-table>
    </div>

    <!-- Modales Factura -->
    <x-admin.form-modal class="nunito-bold" modalName="isFacturaModalOpen" title="Nueva Factura"
        submitLabel="Guardar Factura" maxWidth="max-w-2xl" formId="formFactura">
            <template x-if="formError">
                <div class="mb-3 p-3 rounded border border-red-300 bg-red-50 text-red-700 text-sm nunito-regular">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span x-text="formError"></span>
                </div>
            </template>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- N° de Factura se genera automáticamente --}}
            <div>
                <label for="fecha_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="fecha_factura" name="fecha_factura" x-ref="fecha_factura" @input="formFactura._touched.fecha = true" @blur="formFactura._touched.fecha = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formFactura._touched && formFactura._touched.fecha && !$refs.fecha_factura.value ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formFactura._touched && formFactura._touched.fecha && !$refs.fecha_factura.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errors && errors.fecha">
                    <small class="block mt-1 text-xs text-red-600" x-text="errors.fecha[0]"></small>
                </template>
            </div>
            {{-- OC provista por el cliente (campo editable) --}}
            <div>
                <label for="oc_factura" class="block text-sm font-medium text-gray-700 nunito-bold">OC</label>
                <input type="text" id="oc_factura" name="oc_factura" x-model="oc" maxlength="100" @input="formFactura._touched.oc = true" @blur="formFactura._touched.oc = true"
                    placeholder="OC proporcionada por el cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"
                    :class="formFactura._touched && formFactura._touched.oc && (oc === '' || (oc && oc.length >= 100)) ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formFactura._touched && formFactura._touched.oc && (oc === '' || (oc && oc.length >= 100)) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                <template x-if="errors && errors.oc">
                    <small class="block mt-1 text-xs text-red-600" x-text="errors.oc[0]"></small>
                </template>
            </div>
            <!-- Impuesto ahora se calcula automáticamente (15%) y no es editable en el modal de creación -->
            <div>
                <label for="estado_factura_id" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    Factura</label>
                <select id="estado_factura_id" name="estado_factura_id" x-ref="estado_factura_id" @change="formFactura._touched.estado = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formFactura._touched && formFactura._touched.estado && !$refs.estado_factura_id.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <template x-for="estado in estadosFactura" :key="estado.id || estado.id_estado_factura_pk">
                        <option :value="estado.id || estado.id_estado_factura_pk" x-text="estado.nombre_estado"
                            class="nunito-regular">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formFactura._touched && formFactura._touched.estado && !$refs.estado_factura_id.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errors && errors.id_estado_factura_fk">
                    <small class="block mt-1 text-xs text-red-600" x-text="errors.id_estado_factura_fk[0]"></small>
                </template>
            </div>
            <div>
                <label for="cai_factura" class="block text-sm font-medium text-gray-700 nunito-bold">CAI</label>
                <select id="cai_factura" name="cai_factura" x-ref="cai_factura" @change="formFactura._touched.cai = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formFactura._touched && formFactura._touched.cai && !$refs.cai_factura.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un CAI</option>
                    <template x-for="cai in cais" :key="cai.id || cai.id_cai_pk">
                        <option :value="cai.id || cai.id_cai_pk" :disabled="cai._usable === false" x-text="cai._option_label || cai.codigo" class="nunito-regular"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formFactura._touched && formFactura._touched.cai && !$refs.cai_factura.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errors && errors.id_cai_fk">
                    <small class="block mt-1 text-xs text-red-600" x-text="errors.id_cai_fk[0]"></small>
                </template>
                <!-- Ayuda de CAI: vista previa del próximo número o advertencia -->
                <template x-if="$refs.cai_factura && $refs.cai_factura.value">
                    <div class="mt-1">
                        <template x-if="(cais.find(c => (c.id || c.id_cai_pk) == $refs.cai_factura.value)?._usable) === false">
                            <small class="block text-xs text-red-600">Este CAI no se puede usar (vencido/agotado/fecha vencida).</small>
                        </template>
                        <template x-if="(cais.find(c => (c.id || c.id_cai_pk) == $refs.cai_factura.value)?._usable) === true">
                            <small class="block text-xs text-gray-500">Formato número: <span>FAC-YYYYMMDD-ID</span></small>
                            <small class="block text-xs text-gray-500">Siguiente consecutivo CAI: <span x-text="cais.find(c => (c.id || c.id_cai_pk) == $refs.cai_factura.value)?._next_cai_consecutivo ?? '—'"></span></small>
                        </template>
                    </div>
                </template>
            </div>
            <div>
                <label for="cliente_id" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="cliente_id" name="cliente_id" x-ref="cliente_id" @change="formFactura._touched.cliente = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formFactura._touched && formFactura._touched.cliente && !$refs.cliente_id.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="cliente in clientes" :key="cliente.id || cliente.id_cliente_pk">
                        <option :value="cliente.id || cliente.id_cliente_pk" x-text="cliente.nombre"
                            class="nunito-regular">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formFactura._touched && formFactura._touched.cliente && !$refs.cliente_id.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errors && errors.id_cliente_fk">
                    <small class="block mt-1 text-xs text-red-600" x-text="errors.id_cliente_fk[0]"></small>
                </template>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditFacturaModalOpen" title="Editar Factura"
        itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditFactura">
        <template x-if="formErrorEdit">
            <div class="mb-3 p-3 rounded border border-red-300 bg-red-50 text-red-700 text-sm nunito-regular">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span x-text="formErrorEdit"></span>
            </div>
        </template>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- N° de Factura se genera automáticamente; no editable en modal --}}
            <div>
                <label for="edit_fecha_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="edit_fecha_factura" name="edit_fecha_factura" x-ref="edit_fecha_factura" :value="itemToEdit?.fecha" @input="formEditFactura._touched.fecha = true" @blur="formEditFactura._touched.fecha = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditFactura._touched && formEditFactura._touched.fecha && !$refs.edit_fecha_factura.value ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formEditFactura._touched && formEditFactura._touched.fecha && !$refs.edit_fecha_factura.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errorsEdit && errorsEdit.fecha">
                    <small class="block mt-1 text-xs text-red-600" x-text="errorsEdit.fecha[0]"></small>
                </template>
            </div>
            {{-- OC provista por el cliente (editable) --}}
            <div>
                <label for="edit_oc_factura" class="block text-sm font-medium text-gray-700 nunito-bold">OC</label>
                <input type="text" id="edit_oc_factura" name="edit_oc_factura" x-model="itemToEdit.oc" maxlength="100" @input="formEditFactura._touched.oc = true" @blur="formEditFactura._touched.oc = true"
                    placeholder="OC proporcionada por el cliente"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"
                    :class="formEditFactura._touched && formEditFactura._touched.oc && (itemToEdit.oc === '' || (itemToEdit.oc && itemToEdit.oc.length >= 100)) ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formEditFactura._touched && formEditFactura._touched.oc && (itemToEdit.oc === '' || (itemToEdit.oc && itemToEdit.oc.length >= 100)) ? 'text-red-500' : ''">Requerido. Máximo 100 caracteres.</small>
                <template x-if="errorsEdit && errorsEdit.oc">
                    <small class="block mt-1 text-xs text-red-600" x-text="errorsEdit.oc[0]"></small>
                </template>
            </div>
            <!-- Impuesto se calcula automáticamente (15%) y no es editable desde el modal de edición -->
            <!-- Descuento field removed from Editar Factura modal -->
            <!-- Subtotal, Total y Total Letras se calculan desde los detalles; no son editables desde este modal -->
            <div>
                <label for="edit_estado_factura_id" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    Factura</label>
                <select id="edit_estado_factura_id" name="edit_estado_factura_id" x-ref="edit_estado_factura_id" @change="formEditFactura._touched.estado = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditFactura._touched && formEditFactura._touched.estado && !$refs.edit_estado_factura_id.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <template x-for="estado in estadosFactura" :key="estado.id || estado.id_estado_factura_pk">
                        <option :value="estado.id || estado.id_estado_factura_pk"
                            :selected="(itemToEdit?.estado_factura?.id || itemToEdit?.id_estado_factura_fk) == (estado.id || estado.id_estado_factura_pk)"
                            x-text="estado.nombre_estado" class="nunito-regular">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditFactura._touched && formEditFactura._touched.estado && !$refs.edit_estado_factura_id.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errorsEdit && errorsEdit.id_estado_factura_fk">
                    <small class="block mt-1 text-xs text-red-600" x-text="errorsEdit.id_estado_factura_fk[0]"></small>
                </template>
            </div>
            <div>
                <label for="edit_cai_factura" class="block text-sm font-medium text-gray-700 nunito-bold">CAI</label>
                <select id="edit_cai_factura" name="edit_cai_factura" x-ref="edit_cai_factura" @change="formEditFactura._touched.cai = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditFactura._touched && formEditFactura._touched.cai && !$refs.edit_cai_factura.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un CAI</option>
                    <template x-for="cai in cais" :key="cai.id || cai.id_cai_pk">
                        <option :value="cai.id || cai.id_cai_pk"
                            :selected="(itemToEdit?.cai?.id || itemToEdit?.id_cai_fk) == (cai.id || cai.id_cai_pk)"
                            :disabled="cai._usable === false"
                            x-text="cai._option_label || cai.codigo" class="nunito-regular">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditFactura._touched && formEditFactura._touched.cai && !$refs.edit_cai_factura.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errorsEdit && errorsEdit.id_cai_fk">
                    <small class="block mt-1 text-xs text-red-600" x-text="errorsEdit.id_cai_fk[0]"></small>
                </template>
                <!-- Ayuda de CAI (editar): vista previa del próximo número o advertencia -->
                <template x-if="$refs.edit_cai_factura && $refs.edit_cai_factura.value">
                    <div class="mt-1">
                        <template x-if="(cais.find(c => (c.id || c.id_cai_pk) == $refs.edit_cai_factura.value)?._usable) === false">
                            <small class="block text-xs text-red-600">Este CAI no se puede usar (vencido/agotado/fecha vencida).</small>
                        </template>
                        <template x-if="(cais.find(c => (c.id || c.id_cai_pk) == $refs.edit_cai_factura.value)?._usable) === true">
                            <small class="block text-xs text-gray-500">Formato número: <span>FAC-YYYYMMDD-ID</span></small>
                            <small class="block text-xs text-gray-500">Siguiente consecutivo CAI: <span x-text="cais.find(c => (c.id || c.id_cai_pk) == $refs.edit_cai_factura.value)?._next_cai_consecutivo ?? '—'"></span></small>
                        </template>
                    </div>
                </template>
            </div>
            <div>
                <label for="edit_cliente_id" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="edit_cliente_id" name="edit_cliente_id" x-ref="edit_cliente_id" @change="formEditFactura._touched.cliente = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditFactura._touched && formEditFactura._touched.cliente && !$refs.edit_cliente_id.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="cliente in clientes" :key="cliente.id || cliente.id_cliente_pk">
                        <option :value="cliente.id || cliente.id_cliente_pk"
                            :selected="(itemToEdit?.cliente?.id || itemToEdit?.id_cliente_fk) == (cliente.id || cliente.id_cliente_pk)"
                            x-text="cliente.nombre" class="nunito-regular">
                        </option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditFactura._touched && formEditFactura._touched.cliente && !$refs.edit_cliente_id.value ? 'text-red-500' : ''">Requerido.</small>
                <template x-if="errorsEdit && errorsEdit.id_cliente_fk">
                    <small class="block mt-1 text-xs text-red-600" x-text="errorsEdit.id_cliente_fk[0]"></small>
                </template>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteFacturaModalOpen" itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar la factura?" />

    <!-- TAB: DETALLE FACTURA -->
    <div x-show="tab==='detalle'" class="overflow-x-auto">
        <x-responsive-table title="Detalle Factura" class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                <!-- Sin filtro de selección, los detalles se muestran automáticamente para la primera factura -->
            </x-slot>
            <x-slot name="actions">
                <div class="w-full sm:w-auto flex justify-center">
                    <button @click.prevent="openCreateDetalleModal()"
                        class="w-full sm:w-48 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap">Nuevo
                        Detalle</button>
                </div>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left border-0">N° de Factura</th>
                            <th class="py-2 px-4 text-left border-0">Servicio</th>
                            <th class="py-2 px-4 text-left border-0">Fecha Servicio</th>
                            <th class="py-2 px-4 text-left border-0">Horas</th>
                            <th class="py-2 px-4 text-left border-0">Descripción</th>
                            <th class="py-2 px-4 text-left border-0">Precio Unitario</th>
                            <th class="py-2 px-4 text-left border-0">Cantidad</th>

                            <th class="py-2 px-4 text-left border-0">Total Línea</th>
                            <th class="py-2 px-4 text-left border-0">Descuento</th>
                            <th class="py-2 px-4 text-left border-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingDetalles">
                            <tr>
                                <td colspan="12" class="py-8 text-center text-gray-500 nunito-regular"><i
                                        class="fas fa-spinner fa-spin mr-2"></i> Cargando detalles...</td>
                            </tr>
                        </template>
                        <template x-if="!loadingDetalles && detalles.length === 0">
                            <tr>
                                <td colspan="12" class="py-8 text-center text-gray-500 nunito-regular">Sin detalles para
                                    esta factura</td>
                            </tr>
                        </template>
                        <template x-for="detalle in detalles" :key="detalle.id || detalle.id_detalle_factura_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">

                                <td class="py-2 px-4" x-text="detalle.factura_numero || detalle.id_factura_fk"></td>
                                <td class="py-2 px-4" x-text="detalle.servicio_nombre "></td>
                                <td class="py-2 px-4" x-text="detalle.fecha_servicio"></td>
                                <td class="py-2 px-4" x-text="detalle.horas"></td>
                                <td class="py-2 px-4" x-text="detalle.descripcion"></td>
                                <td class="py-2 px-4" x-text="detalle.precio_unitario"></td>
                                <td class="py-2 px-4" x-text="detalle.cantidad"></td>

                                <td class="py-2 px-4" x-text="detalle.total_linea"></td>
                                <td class="py-2 px-4" x-text="detalle.descuento"></td>
                                <td class="py-2 px-4 flex gap-2">
                                    <button @click.prevent="openEditDetalleModal(detalle)"
                                        class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                    <button @click.prevent="openDeleteDetalleModal(detalle)"
                                        class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-slot>

            <x-slot name="cards">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3 border border-black dark:border-gray-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Detalle 1</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Factura: 0001</p>
                        </div>
                        <span
                            class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">SVC-01</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <div>Fecha Servicio: <span class="font-medium">2025-07-26</span></div>
                        <div>Horas: <span class="font-medium">8</span></div>
                        <div class="col-span-2">Descripción: <span class="font-medium">Descripción ejemplo</span></div>
                        <div>Precio Unitario: <span class="font-medium">100.00</span></div>
                        <div>Cantidad: <span class="font-medium">1</span></div>

                        <div class="col-span-2">Total Línea: <span class="font-medium">115.00</span></div>
                        <div class="col-span-2">Descuento: <span class="font-medium">0</span></div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button
                            @click.prevent="isEditDetalleModalOpen = true; detalleToEdit = {id_detalle: 1, id_factura: '0001', id_servicio: 'SVC-01', fecha_servicio: '2025-07-26', horas: 8, descuento: 0}"
                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center gap-1"><i
                                class="fas fa-edit"></i> Editar</button>
                        <button @click.prevent="isDeleteDetalleModalOpen = true; detalleToDelete = {id_detalle: 1}"
                            class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-1"><i
                                class="fas fa-trash"></i> Eliminar</button>
                    </div>
                </div>
            </x-slot>
        </x-responsive-table>
    </div>

    <!-- Modal Nuevo Detalle Factura -->
    <x-admin.form-modal class="nunito-bold" modalName="isDetalleModalOpen" title="Nuevo Detalle Factura"
        submitLabel="Guardar Detalle" maxWidth="max-w-xl" formId="formDetalle">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_factura_fk" class="block text-sm font-medium text-gray-700 nunito-bold">Factura</label>
                <select id="id_factura_fk" name="id_factura_fk" x-model="id_factura_fk" :disabled="currentFacturaFilter" @change="formDetalle._touched.factura = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"
                    :class="formDetalle._touched && formDetalle._touched.factura && !id_factura_fk ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione una factura</option>
                    <template x-for="f in facturas" :key="f.id || f.id_factura_pk">
                        <option :value="f.id || f.id_factura_pk"
                            x-text="(f.numero || f.id || f.id_factura_pk) + ' — ' + (f.cliente_nombre || '')"
                            class="nunito-regular"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formDetalle._touched && formDetalle._touched.factura && !id_factura_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="id_servicio_fk" class="block text sm font-medium text-gray-700 nunito-bold">Servicio</label>
                <select id="id_servicio_fk" name="id_servicio_fk" x-model="id_servicio_fk" @change="formDetalle._touched.servicio = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"
                    :class="formDetalle._touched && formDetalle._touched.servicio && !id_servicio_fk ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un servicio</option>
                    <template x-for="s in servicios" :key="s.id_servicio_pk">
                        <option :value="s.id_servicio_pk" x-text="s.nombre_servicio || s.tarifa || s.id_servicio_pk"
                            class="nunito-regular"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formDetalle._touched && formDetalle._touched.servicio && !id_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <input type="text" id="descripcion" name="descripcion" x-model="descripcion" maxlength="255" @input="formDetalle._touched.descripcion = true" @blur="formDetalle._touched.descripcion = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formDetalle._touched && formDetalle._touched.descripcion && !descripcion ? 'border-red-500' : ''" autocomplete="off">
                <small class="block mt-1 text-sm text-gray-500" :class="formDetalle._touched && formDetalle._touched.descripcion && (!descripcion || descripcion.length > 255) ? 'text-red-500' : ''">Requerido. Máximo 255 caracteres.</small>
            </div>
            <div>
                <label for="precio_unitario" class="block text-sm font-medium text-gray-700 nunito-bold">Precio
                    Unitario</label>
                <input type="number" step="0.01" id="precio_unitario" name="precio_unitario" x-model.number="precio_unitario" @input="formDetalle._touched.precio = true" @blur="formDetalle._touched.precio = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formDetalle._touched && formDetalle._touched.precio && !precio_unitario ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formDetalle._touched && formDetalle._touched.precio && !precio_unitario ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="cantidad" class="block text-sm font-medium text-gray-700 nunito-bold">Cantidad</label>
                <input type="number" id="cantidad" name="cantidad" x-model.number="cantidad" @input="formDetalle._touched.cantidad = true" @blur="formDetalle._touched.cantidad = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formDetalle._touched && formDetalle._touched.cantidad && !cantidad ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formDetalle._touched && formDetalle._touched.cantidad && !cantidad ? 'text-red-500' : ''">Requerido.</small>
            </div>

            <div>
                <label for="fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Servicio</label>
                <input type="date" id="fecha_servicio" name="fecha_servicio" x-model="fecha_servicio" @input="formDetalle._touched.fecha = true" @blur="formDetalle._touched.fecha = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formDetalle._touched && formDetalle._touched.fecha && !fecha_servicio ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formDetalle._touched && formDetalle._touched.fecha && !fecha_servicio ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="horas" class="block text-sm font-medium text-gray-700 nunito-bold">Horas</label>
                <input type="number" id="horas" name="horas" x-model.number="horas"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="descuento" class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" id="descuento" name="descuento" x-model.number="descuento"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Detalle Factura -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditDetalleModalOpen" title="Editar Detalle Factura"
        itemToEdit="detalleToEdit" maxWidth="max-w-xl" formId="formEditDetalle">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_id_factura_fk"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Factura</label>
                <select id="edit_id_factura_fk" name="edit_id_factura_fk" x-ref="edit_id_factura_fk" :disabled="currentFacturaFilter" @change="formEditDetalle._touched.factura = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"
                    :class="formEditDetalle._touched && formEditDetalle._touched.factura && !$refs.edit_id_factura_fk.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione una factura</option>
                    <template x-for="f in facturas" :key="f.id || f.id_factura_pk">
                        <option :value="f.id || f.id_factura_pk"
                            :selected="(detalleToEdit?.id_factura_fk || detalleToEdit?.id_factura || detalleToEdit?.id_factura_pk) == (f.id || f.id_factura_pk)"
                            x-text="(f.numero || f.id || f.id_factura_pk) + ' — ' + (f.cliente_nombre || '')"
                            class="nunito-regular"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditDetalle._touched && formEditDetalle._touched.factura && !edit_id_factura_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_id_servicio_fk"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Servicio</label>
                <select id="edit_id_servicio_fk" name="edit_id_servicio_fk" x-ref="edit_id_servicio_fk" @change="formEditDetalle._touched.servicio = true"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 nunito-regular px-2"
                    :class="formEditDetalle._touched && formEditDetalle._touched.servicio && !$refs.edit_id_servicio_fk.value ? 'border-red-500' : ''">
                    <option value="" class="nunito-regular">Seleccione un servicio</option>
                    <template x-for="s in servicios" :key="s.id_servicio_pk">
                        <option :value="s.id_servicio_pk"
                            :selected="(detalleToEdit?.id_servicio_fk || detalleToEdit?.id_servicio || detalleToEdit?.id_servicio_pk) == s.id_servicio_pk"
                            x-text="s.nombre_servicio || s.tarifa || s.id_servicio_pk" class="nunito-regular"></option>
                    </template>
                </select>
                <small class="block mt-1 text-sm text-gray-500" :class="formEditDetalle._touched && formEditDetalle._touched.servicio && !edit_id_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Servicio</label>
                <input type="date" id="edit_fecha_servicio" name="edit_fecha_servicio" x-ref="edit_fecha_servicio" @input="formEditDetalle._touched.fecha = true" @blur="formEditDetalle._touched.fecha = true"
                    :value="detalleToEdit?.fecha_servicio || ''"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditDetalle._touched && formEditDetalle._touched.fecha && !$refs.edit_fecha_servicio.value ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formEditDetalle._touched && formEditDetalle._touched.fecha && !edit_fecha_servicio ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_descripcion"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descripción</label>
                <input type="text" id="edit_descripcion" name="edit_descripcion" x-ref="edit_descripcion" maxlength="255" @input="formEditDetalle._touched.descripcion = true" @blur="formEditDetalle._touched.descripcion = true"
                    :value="detalleToEdit?.descripcion || ''"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditDetalle._touched && formEditDetalle._touched.descripcion && (!$refs.edit_descripcion.value || $refs.edit_descripcion.value.length > 250) ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formEditDetalle._touched && formEditDetalle._touched.descripcion && (!edit_descripcion || edit_descripcion.length > 250) ? 'text-red-500' : ''">Requerido. Máximo 250 caracteres.</small>
            </div>
            <div>
                <label for="edit_precio_unitario" class="block text-sm font-medium text-gray-700 nunito-bold">Precio
                    Unitario</label>
                <input type="number" step="0.01" id="edit_precio_unitario" name="edit_precio_unitario" x-ref="edit_precio_unitario" @input="formEditDetalle._touched.precio = true" @blur="formEditDetalle._touched.precio = true"
                    :value="detalleToEdit?.precio_unitario || 0"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditDetalle._touched && formEditDetalle._touched.precio && !$refs.edit_precio_unitario.value ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formEditDetalle._touched && formEditDetalle._touched.precio && !edit_precio_unitario ? 'text-red-500' : ''">Requerido.</small>
            </div>
            <div>
                <label for="edit_cantidad" class="block text-sm font-medium text-gray-700 nunito-bold">Cantidad</label>
                <input type="number" id="edit_cantidad" name="edit_cantidad" x-ref="edit_cantidad" @input="formEditDetalle._touched.cantidad = true" @blur="formEditDetalle._touched.cantidad = true" :value="detalleToEdit?.cantidad || 0"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2"
                    :class="formEditDetalle._touched && formEditDetalle._touched.cantidad && !$refs.edit_cantidad.value ? 'border-red-500' : ''">
                <small class="block mt-1 text-sm text-gray-500" :class="formEditDetalle._touched && formEditDetalle._touched.cantidad && !edit_cantidad ? 'text-red-500' : ''">Requerido.</small>
            </div>

            <div>
                <label for="edit_horas" class="block text-sm font-medium text-gray-700 nunito-bold">Horas</label>
                <input type="number" id="edit_horas" name="edit_horas" :value="detalleToEdit?.horas || 0"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descuento"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" id="edit_descuento" name="edit_descuento" :value="detalleToEdit?.descuento || 0"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteDetalleModalOpen" itemToDelete="detalleToDelete"
        message="¿Estás seguro de que quieres eliminar el detalle de factura?" />
</div>