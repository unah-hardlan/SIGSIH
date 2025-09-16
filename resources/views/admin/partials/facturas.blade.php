<div x-data="{
    tab: 'facturas',
    isFacturaModalOpen: false, 
    isEditFacturaModalOpen: false, 
    facturaToEdit: {id: '', numero: '', fecha: '', oc: '', subtotal: '', total: '', total_letras: '', estado_factura: '', cai: '', cliente: ''}, 
    isDeleteFacturaModalOpen: false, 
    facturaToDelete: {id: ''},
    isDetalleModalOpen: false,
    isEditDetalleModalOpen: false,
    detalleToEdit: {id_detalle: '', id_factura: '', id_servicio: '', fecha_servicio: '', horas: '', descuento: ''},
    isDeleteDetalleModalOpen: false,
    detalleToDelete: {id_detalle: ''}
}" @include('partials.persist-tab', ['tabKey' => 'admin-facturas-tab']) class="p-6">

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
        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h2 class="text-2xl dark:text-white text-gray-800 nunito-bold">Facturas</h2>
            </x-slot>
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchFacturas',
                    'filtrosSelect' => [
                        'estadoFacturaFiltro' => [
                            'label' => 'Estado',
                            'options' => ['Pagada', 'Pendiente', 'Cancelada']
                        ],
                        'clienteFacturaFiltro' => [
                            'label' => 'Cliente',
                            'options' => ['BAC Credomatic', 'Bancafe']
                        ]
                    ],
                    'ordenarOptions' => [
                        'fecha' => 'Fecha',
                        'total' => 'Total',
                        'estado_factura' => 'Estado'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="flex flex-col gap-2 items-stretch">
                    <button @click="isFacturaModalOpen = true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap text-sm">Nueva Factura</button>
                    <a href="/admin/reportes-header?modulo=Facturas&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition whitespace-nowrap flex items-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </x-slot>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                    <tr>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Número</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Fecha</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">OC</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Subtotal</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Total</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Total Letras</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Estado Factura</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">CAI</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Cliente</th>
                        <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b nunito-regular bg-white dark:bg-gray-900">
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">1</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">0001</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2025-07-30</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">OC-12345</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">5000</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">5500</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Cinco mil quinientos lempiras</td>
                        <td class="py-2 px-4">
                            <span class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 px-2 py-1 rounded nunito-regular">Pagada</span>
                        </td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">CAI-987654321</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">BAC credomatic</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="/admin/formato-factura" target="_blank"
                                class="inline-flex items-center justify-center text-xs w-24 h-9 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 mr-2 nunito-regular">
                                <i class="fas fa-eye mr-1"></i> Ver detalles
                            </a>
                            <a href="#"
                                @click.prevent="isEditFacturaModalOpen = true; facturaToEdit = {id: 1, numero: '0001', fecha: '2025-07-30', oc: 'OC-12345', subtotal: 5000, total: 5500, total_letras: 'Cinco mil quinientos lempiras', estado_factura: 'Pagada', cai: 'CAI-987654321', cliente: 'BAC credomatic'}"
                                class="inline-flex items-center justify-center text-blue-500 hover:text-blue-700 nunito-regular">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" @click.prevent="isDeleteFacturaModalOpen = true; facturaToDelete = {id: 1}"
                                class="inline-flex items-center justify-center text-red-500 hover:text-red-700 nunito-regular">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <tr class="border-b nunito-regular bg-white dark:bg-gray-900">
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">0002</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2025-07-31</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">OC-54321</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">6000</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">6500</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Seis mil quinientos lempiras</td>
                        <td class="py-2 px-4">
                            <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 px-2 py-1 rounded nunito-regular">Pendiente</span>
                        </td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">CAI-123456789</td>
                        <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">Bancafe</td>
                        <td class="py-2 px-4 flex gap-2">
                            <a href="/admin/formato-factura" target="_blank"
                                class="inline-flex items-center justify-center text-xs w-24 h-9 rounded bg-emerald-500 text-white hover:bg-emerald-600 duration-300 mr-2 nunito-regular">
                                <i class="fas fa-eye mr-1"></i> Ver detalles
                            </a>
                            <a href="#"
                                @click.prevent="isEditFacturaModalOpen = true; facturaToEdit = {id: 2, numero: '0002', fecha: '2025-07-31', oc: 'OC-54321', subtotal: 6000, total: 6500, total_letras: 'Seis mil quinientos lempiras', estado_factura: 'Pendiente', cai: 'CAI-123456789', cliente: 'Bancafe'}"
                                class="inline-flex items-center justify-center text-blue-500 hover:text-blue-700 nunito-regular">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" @click.prevent="isDeleteFacturaModalOpen = true; facturaToDelete = {id: 2}"
                                class="inline-flex items-center justify-center text-red-500 hover:text-red-700 nunito-regular">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </x-admin.tabla-crud>
    </div>

    <!-- Modales Factura -->
    <x-admin.form-modal class="nunito-bold" modalName="isFacturaModalOpen" title="Nueva Factura" submitLabel="Guardar Factura"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="numero_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Número</label>
                <input type="text" id="numero_factura" name="numero_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="fecha_factura" name="fecha_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="oc_factura" class="block text-sm font-medium text-gray-700 nunito-bold">OC</label>
                <input type="text" id="oc_factura" name="oc_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="subtotal_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Subtotal</label>
                <input type="number" id="subtotal_factura" name="subtotal_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="total_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                <input type="number" id="total_factura" name="total_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="total_letras_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Total Letras</label>
                <input type="text" id="total_letras_factura" name="total_letras_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="estado_factura_id" class="block text-sm font-medium text-gray-700 nunito-bold">Estado Factura</label>
                <select id="estado_factura_id" name="estado_factura_id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <option value="1" class="nunito-regular">Pagada</option>
                    <option value="2" class="nunito-regular">Pendiente</option>
                    <option value="3" class="nunito-regular">Cancelada</option>
                </select>
            </div>
            <div>
                <label for="cai_factura" class="block text-sm font-medium text-gray-700 nunito-bold">CAI</label>
                <input type="text" id="cai_factura" name="cai_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="cliente_id" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="cliente_id" name="cliente_id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <option value="1" class="nunito-regular">Bac Credomatic</option>
                    <option value="2" class="nunito-regular">Bancafe</option>
                </select>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.edit-modal class="nunito-bold" modalName="isEditFacturaModalOpen" title="Editar Factura" itemToEdit="facturaToEdit"
        maxWidth="max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_numero_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Número</label>
                <input type="text" id="edit_numero_factura" name="edit_numero_factura" :value="facturaToEdit.numero" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha</label>
                <input type="date" id="edit_fecha_factura" name="edit_fecha_factura" :value="facturaToEdit.fecha" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_oc_factura" class="block text-sm font-medium text-gray-700 nunito-bold">OC</label>
                <input type="text" id="edit_oc_factura" name="edit_oc_factura" :value="facturaToEdit.oc" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_subtotal_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Subtotal</label>
                <input type="number" id="edit_subtotal_factura" name="edit_subtotal_factura" :value="facturaToEdit.subtotal" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_total_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Total</label>
                <input type="number" id="edit_total_factura" name="edit_total_factura" :value="facturaToEdit.total" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_total_letras_factura" class="block text-sm font-medium text-gray-700 nunito-bold">Total Letras</label>
                <input type="text" id="edit_total_letras_factura" name="edit_total_letras_factura" :value="facturaToEdit.total_letras" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_estado_factura_id" class="block text-sm font-medium text-gray-700 nunito-bold">Estado Factura</label>
                <select id="edit_estado_factura_id" name="edit_estado_factura_id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un estado</option>
                    <option value="1" :selected="facturaToEdit.estado_factura === 'Pagada'" class="nunito-regular">Pagada</option>
                    <option value="2" :selected="facturaToEdit.estado_factura === 'Pendiente'" class="nunito-regular">Pendiente</option>
                    <option value="3" :selected="facturaToEdit.estado_factura === 'Cancelada'" class="nunito-regular">Cancelada</option>
                </select>
            </div>
            <div>
                <label for="edit_cai_factura" class="block text-sm font-medium text-gray-700 nunito-bold">CAI</label>
                <input type="text" id="edit_cai_factura" name="edit_cai_factura" :value="facturaToEdit.cai" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_cliente_id" class="block text-sm font-medium text-gray-700 nunito-bold">Cliente</label>
                <select id="edit_cliente_id" name="edit_cliente_id" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
                    <option value="" class="nunito-regular">Seleccione un cliente</option>
                    <option value="1" :selected="facturaToEdit.cliente === 'Bac Credomatic'" class="nunito-regular">Bac Credomatic</option>
                    <option value="2" :selected="facturaToEdit.cliente === 'Bancafe'" class="nunito-regular">Bancafe</option>
                </select>
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteFacturaModalOpen" itemToDelete="facturaToDelete"
        message="¿Estás seguro de que quieres eliminar la factura?" />

    <!-- TAB: DETALLE FACTURA -->
    <div x-show="tab==='detalle'" class="overflow-x-auto">
        <x-admin.tabla-crud class="nunito-bold">
            <x-slot name="titulo">
                <h2 class="text-2xl dark:text-white text-gray-800 nunito-bold">Detalle Factura</h2>
            </x-slot>
            <x-slot name="filtros">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchDetalleFactura',
                    'filtrosSelect' => [
                        'servicioDetalleFiltro' => [
                            'label' => 'Servicio',
                            'options' => ['SVC-01', 'SVC-02']
                        ],
                        'facturaDetalleFiltro' => [
                            'label' => 'Factura',
                            'options' => ['0001', '0002']
                        ]
                    ],
                    'ordenarOptions' => [
                        'fecha_servicio' => 'Fecha Servicio',
                        'horas' => 'Horas',
                        'descuento' => 'Descuento'
                    ]
                ])
            </x-slot>
            <x-slot name="boton">
                <div class="w-full flex justify-center">
                    <button @click="isDetalleModalOpen = true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap w-11/12 sm:w-48">Nuevo
                        Detalle</button>
                </div>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Detalle</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Factura</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">ID Servicio</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Fecha Servicio</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Horas</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Descuento</th>
                            <th class="py-2 px-4 text-left nunito-bold text-gray-800 dark:text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular bg-white dark:bg-gray-900">
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">1</td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">0001</td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">SVC-01</td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">2025-07-26</td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">8</td>
                            <td class="py-2 px-4 nunito-regular text-gray-800 dark:text-white">0</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#"
                                    @click.prevent="isEditDetalleModalOpen = true; detalleToEdit = {id_detalle: 1, id_factura: '0001', id_servicio: 'SVC-01', fecha_servicio: '2025-07-26', horas: 8, descuento: 0}"
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-300 nunito-regular"><i class="fas fa-edit"></i></a>
                                <a href="#"
                                    @click.prevent="isDeleteDetalleModalOpen = true; detalleToDelete = {id_detalle: 1}"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 nunito-regular"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-admin.tabla-crud>
    </div>

    <!-- Modal Nuevo Detalle Factura -->
    <x-admin.form-modal class="nunito-bold" modalName="isDetalleModalOpen" title="Nuevo Detalle Factura" submitLabel="Guardar Detalle"
        maxWidth="max-w-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="id_factura" class="block text-sm font-medium text-gray-700 nunito-bold">ID Factura</label>
                <input type="text" id="id_factura" name="id_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="id_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">ID Servicio</label>
                <input type="text" id="id_servicio" name="id_servicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Servicio</label>
                <input type="date" id="fecha_servicio" name="fecha_servicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="horas" class="block text-sm font-medium text-gray-700 nunito-bold">Horas</label>
                <input type="number" id="horas" name="horas" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="descuento" class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" id="descuento" name="descuento" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.form-modal>

    <!-- Modal Editar Detalle Factura -->
    <x-admin.edit-modal class="nunito-bold" modalName="isEditDetalleModalOpen" title="Editar Detalle Factura" itemToEdit="detalleToEdit"
        maxWidth="max-w-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="edit_id_factura" class="block text-sm font-medium text-gray-700 nunito-bold">ID Factura</label>
                <input type="text" id="edit_id_factura" name="edit_id_factura" :value="detalleToEdit.id_factura" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_id_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">ID Servicio</label>
                <input type="text" id="edit_id_servicio" name="edit_id_servicio" :value="detalleToEdit.id_servicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_fecha_servicio" class="block text-sm font-medium text-gray-700 nunito-bold">Fecha Servicio</label>
                <input type="date" id="edit_fecha_servicio" name="edit_fecha_servicio" :value="detalleToEdit.fecha_servicio" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_horas" class="block text-sm font-medium text-gray-700 nunito-bold">Horas</label>
                <input type="number" id="edit_horas" name="edit_horas" :value="detalleToEdit.horas" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
            <div>
                <label for="edit_descuento" class="block text-sm font-medium text-gray-700 nunito-bold">Descuento</label>
                <input type="number" id="edit_descuento" name="edit_descuento" :value="detalleToEdit.descuento" class="mt-1 block w-full rounded-md border-gray-500 shadow-sm border focus:border-gray-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 nunito-regular px-2">
            </div>
        </div>
    </x-admin.edit-modal>

    <x-admin.confirmation-modal class="nunito-bold" modalName="isDeleteDetalleModalOpen" itemToDelete="detalleToDelete"
        message="¿Estás seguro de que quieres eliminar el detalle de factura?" />
</div>