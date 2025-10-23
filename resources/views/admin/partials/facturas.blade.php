<div x-data="Object.assign(facturasCrud(), { tab: 'facturas' })" @include('partials.persist-tab', ['tabKey'=>
    'admin-facturas-tab', 'forceDefault' => true]) class="p-6">

    <div class="mb-6">
        <ul class="flex border-b nunito-bold">
            <li @click="tab='facturas'"
                :class="tab==='facturas' ? 'border-b-2 border-blue-500 text-blue-500' : 'dark:text-gray-200 hover:text-blue-500 cursor-pointer'"
                class="mr-6 pb-2 nunito-bold">Facturas</li>
            <li @click="tab='detalle'"
                :class="tab==='detalle' ? 'border-b-2 border-blue-500 text-blue-500' : 'dark:text-gray-200 hover:text-blue-500 cursor-pointer'"
                class="pb-2 nunito-bold">Detalle de Factura</li>
        </ul>
    </div>

    <!-- TAB: FACTURAS -->
    <div x-show="tab==='facturas'" class="overflow-x-auto">
        <x-responsive-table title="Facturas" class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                <div class="flex flex-col gap-2 w-full">
                    <div class="flex flex-col sm:flex-row gap-2 w-full">
                        @include('partials.filtros-generales', [
                        'searchModel' => 'searchFacturas',
                        'filtrosSelect' => [
                        'estadoFacturaFiltro' => [ 'label' => 'Estado', 'options' => ['Pagada','Pendiente','Cancelada']
                        ],
                        'clienteFacturaFiltro' => [ 'label' => 'Cliente', 'options' => ['BAC Credomatic','Bancafe'] ]
                        ],
                        'ordenarOptions' => [ 'fecha' => 'Fecha', 'total' => 'Total', 'estado_factura' => 'Estado']
                        ])
                    </div>
                </div>
            </x-slot>
            <x-slot name="actions">
                <div class="flex flex-col gap-2 w-full">
                    <button @click="isFacturaModalOpen = true"
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
                            <th class="py-2 px-4 text-left border-0 text-[10px]">ID</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Número</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Fecha</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">OC</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Subtotal</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Impuesto</th>
                            <th class="py-2 px-4 text-left border-0 text-[10px]">Descuento</th>
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
                                <td colspan="13" class="py-8 text-center text-gray-500 nunito-regular"><i
                                        class="fas fa-spinner fa-spin mr-2"></i> Cargando...</td>
                            </tr>
                        </template>
                        <template x-if="!loadingFacturas && facturas.length === 0">
                            <tr>
                                <td colspan="13" class="py-8 text-center text-gray-500 nunito-regular">Sin resultados
                                </td>
                            </tr>
                        </template>
                        <template x-for="factura in filteredFacturas" :key="factura.id || factura.id_factura_pk">
                            <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular text-[10px]">
                                <td class="py-2 px-4 text-[10px] break-words"
                                    x-text="factura.id || factura.id_factura_pk"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.numero"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.fecha"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.oc || '-' "></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.subtotal"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.impuesto || '0.00'"></td>
                                <td class="py-2 px-4 text-[10px] break-words" x-text="factura.descuento || '0.00'"></td>
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
                                    <a href="/admin/formato-factura" target="_blank"
                                        class="text-xs px-3 py-1 rounded bg-emerald-500 text-white hover:bg-emerald-600 nunito-regular flex items-center gap-1"><i
                                            class="fas fa-eye"></i> Ver</a>
                                    <button @click.prevent="isEditFacturaModalOpen = true; itemToEdit = factura"
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
                            <a href="/admin/formato-factura" target="_blank"
                                class="px-3 py-1 text-xs bg-emerald-500 text-white rounded hover:bg-emerald-600 flex items-center gap-1"><i
                                    class="fas fa-eye"></i> Ver</a>
                            <button @click.prevent="isEditFacturaModalOpen = true; itemToEdit = factura"
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="numero_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Número</label>
                <input type="text" id="numero_factura" name="numero_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="fecha_factura" name="fecha_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="oc_factura" class="block text-sm font-medium text-gray-700 nunito-bold">OC</label>
                <input type="text" id="oc_factura" name="oc_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="subtotal_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Subtotal</label>
                <input type="number" id="subtotal_factura" name="subtotal_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="impuesto_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Impuesto</label>
                <input type="number" step="0.01" id="impuesto_factura" name="impuesto_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="descuento_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" step="0.01" id="descuento_factura" name="descuento_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="total_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                <input type="number" id="total_factura" name="total_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="total_letras_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Total
                    Letras</label>
                <input type="text" id="total_letras_factura" name="total_letras_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="estado_factura_id" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    Factura</label>
                <select id="estado_factura_id" name="estado_factura_id"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <template x-for="estado in estadosFactura" :key="estado.id || estado.id_estado_factura_pk">
                        <option :value="estado.id || estado.id_estado_factura_pk" x-text="estado.nombre_estado"
                            class="nunito-regular">
                        </option>
                    </template>
                </select>
            </div>
            <div>
                <label for="cai_factura" class="block text-sm font-medium text-gray-700 nunito-bold">CAI</label>
                <select id="cai_factura" name="cai_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un CAI</option>
                    <template x-for="cai in cais" :key="cai.id || cai.id_cai_pk">
                        <option :value="cai.id || cai.id_cai_pk" x-text="cai.codigo" class="nunito-regular">
                        </option>
                    </template>
                </select>
            </div>
            <div>
                <label for="cliente_id" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="cliente_id" name="cliente_id"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="cliente in clientes" :key="cliente.id || cliente.id_cliente_pk">
                        <option :value="cliente.id || cliente.id_cliente_pk" x-text="cliente.nombre"
                            class="nunito-regular">
                        </option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditFacturaModalOpen" title="Editar Factura"
        itemToEdit="itemToEdit" maxWidth="max-w-2xl" formId="formEditFactura">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_numero_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Número</label>
                <input type="text" id="edit_numero_factura" name="edit_numero_factura" :value="itemToEdit?.numero"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="edit_fecha_factura" name="edit_fecha_factura" :value="itemToEdit?.fecha"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_oc_factura" class="block text-sm font-medium text-gray-700 nunito-bold">OC</label>
                <input type="text" id="edit_oc_factura" name="edit_oc_factura" :value="itemToEdit?.oc"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_subtotal_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Subtotal</label>
                <input type="number" id="edit_subtotal_factura" name="edit_subtotal_factura"
                    :value="itemToEdit?.subtotal"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_impuesto_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Impuesto</label>
                <input type="number" step="0.01" id="edit_impuesto_factura" name="edit_impuesto_factura"
                    :value="itemToEdit?.impuesto"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descuento_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" step="0.01" id="edit_descuento_factura" name="edit_descuento_factura"
                    :value="itemToEdit?.descuento"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_total_factura"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                <input type="number" id="edit_total_factura" name="edit_total_factura" :value="itemToEdit?.total"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_total_letras_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Total
                    Letras</label>
                <input type="text" id="edit_total_letras_factura" name="edit_total_letras_factura"
                    :value="itemToEdit?.total_letras"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado_factura_id" class="block text-sm font-medium text-gray-700 nunito-bold">Estado
                    Factura</label>
                <select id="edit_estado_factura_id" name="edit_estado_factura_id"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <template x-for="estado in estadosFactura" :key="estado.id || estado.id_estado_factura_pk">
                        <option :value="estado.id || estado.id_estado_factura_pk"
                            :selected="(itemToEdit?.estado_factura?.id || itemToEdit?.id_estado_factura_fk) == (estado.id || estado.id_estado_factura_pk)"
                            x-text="estado.nombre_estado" class="nunito-regular">
                        </option>
                    </template>
                </select>
            </div>
            <div>
                <label for="edit_cai_factura" class="block text-sm font-medium text-gray-700 nunito-bold">CAI</label>
                <select id="edit_cai_factura" name="edit_cai_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un CAI</option>
                    <template x-for="cai in cais" :key="cai.id || cai.id_cai_pk">
                        <option :value="cai.id || cai.id_cai_pk"
                            :selected="(itemToEdit?.cai?.id || itemToEdit?.id_cai_fk) == (cai.id || cai.id_cai_pk)"
                            x-text="cai.codigo" class="nunito-regular">
                        </option>
                    </template>
                </select>
            </div>
            <div>
                <label for="edit_cliente_id" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="edit_cliente_id" name="edit_cliente_id"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <template x-for="cliente in clientes" :key="cliente.id || cliente.id_cliente_pk">
                        <option :value="cliente.id || cliente.id_cliente_pk"
                            :selected="(itemToEdit?.cliente?.id || itemToEdit?.id_cliente_fk) == (cliente.id || cliente.id_cliente_pk)"
                            x-text="cliente.nombre" class="nunito-regular">
                        </option>
                    </template>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteFacturaModalOpen" itemToDelete="itemToDelete"
        message="¿Estás seguro de que quieres eliminar la factura?" />

    <!-- TAB: DETALLE FACTURA -->
    <div x-show="tab==='detalle'" class="overflow-x-auto">
        <x-responsive-table title="Detalle Factura" class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4">
            <x-slot name="filters">
                @include('partials.filtros-generales', [
                'searchModel' => 'searchDetalleFactura',
                'filtrosSelect' => [
                'servicioDetalleFiltro' => [ 'label' => 'Servicio', 'options' => ['SVC-01','SVC-02'] ],
                'facturaDetalleFiltro' => [ 'label' => 'Factura', 'options' => ['0001','0002'] ]
                ],
                'ordenarOptions' => [ 'fecha_servicio' => 'Fecha Servicio', 'horas' => 'Horas', 'descuento' =>
                'Descuento']
                ])
            </x-slot>
            <x-slot name="actions">
                <div class="w-full sm:w-auto flex justify-center">
                    <button @click="isDetalleModalOpen = true"
                        class="w-full sm:w-48 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular whitespace-nowrap">Nuevo
                        Detalle</button>
                </div>
            </x-slot>
            <x-slot name="table">
                <table class="min-w-full text-sm bg-white dark:bg-gray-900 rounded-lg overflow-hidden border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left border-0">ID Detalle</th>
                            <th class="py-2 px-4 text-left border-0">ID Factura</th>
                            <th class="py-2 px-4 text-left border-0">ID Servicio</th>
                            <th class="py-2 px-4 text-left border-0">Fecha Servicio</th>
                            <th class="py-2 px-4 text-left border-0">Horas</th>
                            <th class="py-2 px-4 text-left border-0">Descripción</th>
                            <th class="py-2 px-4 text-left border-0">Precio Unitario</th>
                            <th class="py-2 px-4 text-left border-0">Cantidad</th>
                            <th class="py-2 px-4 text-left border-0">Impuesto</th>
                            <th class="py-2 px-4 text-left border-0">Total Línea</th>
                            <th class="py-2 px-4 text-left border-0">Descuento</th>
                            <th class="py-2 px-4 text-left border-0">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Placeholder item as in original (replace later with real data source) -->
                        <tr class="border-b border-gray-200 dark:border-gray-700 nunito-regular">
                            <td class="py-2 px-4">1</td>
                            <td class="py-2 px-4">0001</td>
                            <td class="py-2 px-4">SVC-01</td>
                            <td class="py-2 px-4">2025-07-26</td>
                            <td class="py-2 px-4">8</td>
                            <td class="py-2 px-4">Descripción ejemplo</td>
                            <td class="py-2 px-4">100.00</td>
                            <td class="py-2 px-4">1</td>
                            <td class="py-2 px-4">15.00</td>
                            <td class="py-2 px-4 text-[10px] max-w-[120px] whitespace-normal break-words truncate"
                                x-text="factura.total_letras || '-' "></td>
                            <td class="py-2 px-4">0</td>
                            <td class="py-2 px-4 flex gap-2">
                                <button
                                    @click.prevent="isEditDetalleModalOpen = true; detalleToEdit = {id_detalle: 1, id_factura: '0001', id_servicio: 'SVC-01', fecha_servicio: '2025-07-26', horas: 8, descripcion: 'Descripción ejemplo', precio_unitario: 100.00, cantidad: 1, impuesto: 15.00, total_linea: 115.00, descuento: 0}"
                                    class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                <button
                                    @click.prevent="isDeleteDetalleModalOpen = true; detalleToDelete = {id_detalle: 1}"
                                    class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                            </td>
                            <td class="py-2 px-4 text-[10px] max-w-[80px] whitespace-normal break-words truncate"
                                x-text="factura.cai || 'Sin CAI'"></td>
                            <td class="py-2 px-4 text-[10px] max-w-[100px] whitespace-normal break-words truncate"
                                x-text="factura.cliente_nombre || 'Sin cliente'"></td>
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
                        <div>Impuesto: <span class="font-medium">15.00</span></div>
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
        submitLabel="Guardar Detalle" maxWidth="max-w-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_factura" class="block text-sm font-medium text-gray-700 nunito-bold">ID Factura</label>
                <input type="text" id="id_factura" name="id_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="id_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">ID Servicio</label>
                <input type="text" id="id_servicio" name="id_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Servicio</label>
                <input type="date" id="fecha_servicio" name="fecha_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="horas" class="block text-sm font-medium text-gray-700 nunito-bold">Horas</label>
                <input type="number" id="horas" name="horas"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="descuento" class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" id="descuento" name="descuento"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Detalle Factura -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditDetalleModalOpen" title="Editar Detalle Factura"
        itemToEdit="detalleToEdit" maxWidth="max-w-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_id_factura" class="block text-sm font-medium text-gray-700 nunito-bold">ID
                    Factura</label>
                <input type="text" id="edit_id_factura" name="edit_id_factura" :value="detalleToEdit.id_factura"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_id_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">ID
                    Servicio</label>
                <input type="text" id="edit_id_servicio" name="edit_id_servicio" :value="detalleToEdit.id_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha
                    Servicio</label>
                <input type="date" id="edit_fecha_servicio" name="edit_fecha_servicio"
                    :value="detalleToEdit.fecha_servicio"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_horas" class="block text-sm font-medium text-gray-700 nunito-bold">Horas</label>
                <input type="number" id="edit_horas" name="edit_horas" :value="detalleToEdit.horas"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descuento"
                    class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" id="edit_descuento" name="edit_descuento" :value="detalleToEdit.descuento"
                    class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500  nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteDetalleModalOpen" itemToDelete="detalleToDelete"
        message="¿Estás seguro de que quieres eliminar el detalle de factura?" />
</div>