<div x-data="{ tab: 'calendario', isAddModalOpen: false, isEditModalOpen: false, isDetailModalOpen: false, isCancelModalOpen: false, isAddCalendarioModalOpen: false, isEditCalendarioModalOpen: false, selectedEvent: null, calendarioToEdit: {fecha: '', descripcion: '', estado: '', cliente: '', agencia: '', tipo_mantenimiento: ''} }" @include('partials.persist-tab', ['tabKey' => 'admin-calendario-tab']) class="container mx-auto px-4 sm:px-8">
    <div class="w-full">
        <ul class="flex border-b nunito-bold">
            <li @click="tab='calendario'" :class="tab==='calendario' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-300 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Calendario</li>
            <li @click="tab='eventosLista'" :class="tab==='eventosLista' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-300 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Lista de Eventos</li>
        </ul>

                <div x-show="tab==='calendario'" class="py-8">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                                <h2 class="text-2xl font-semibold leading-tight nunito-bold mb-3 text-gray-800 dark:text-white">Calendario</h2>
                                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                        <button class="transition duration-100 ease-in-out w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 flex items-center justify-center rounded-lg nunito-regular text-sm" @click="isAddModalOpen = true ">
                                                <i class="fas fa-plus mr-2"></i> Agregar
                                        </button>
                                        <a href="/admin/reportes-header?modulo=Calendario&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                                             class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition duration-100 ease-in-out whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                                                <i class="fas fa-file-alt"></i> Generar Reporte
                                        </a>
                                </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 mb-4">
                                @include('partials.filtros-generales', [
                                    'searchModel' => 'searchCalendario',
                                    'filtrosSelect' => [
                                        'agenciaFiltro' => [
                                            'label' => 'Agencias',
                                            'options' => ['Agencia Central', 'Agencia Norte']
                                        ],
                                        'estadoFiltro' => [
                                            'label' => 'Estados',
                                            'options' => ['Programado', 'Realizado', 'Cancelado']
                                        ],
                                        'tipoFiltro' => [
                                            'label' => 'Tipo de mantenimiento',
                                            'options' => ['Preventivo', 'Correctivo']
                                        ],
                                        'clienteFiltro' => [
                                            'label' => 'Clientes',
                                            'options' => ['Juan Pérez', 'Ana López']
                                        ]
                                    ],
                                    'ordenarOptions' => [
                                        'fecha' => 'Fecha',
                                        'estado' => 'Estado',
                                        'agencia' => 'Agencia',
                                        'cliente' => 'Cliente'
                                    ]
                                ])
                        </div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-2xl overflow-hidden bg-white dark:bg-gray-900 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold"><i class="fas fa-chevron-left"></i></button>
                        <span class="text-lg font-semibold text-gray-700 dark:text-white nunito-bold">Julio 2025</span>
                        <button class="text-blue-500 hover:text-blue-700 font-semibold"><i class="fas fa-chevron-right"></i></button>
                    </div>
                        <div class="grid p-4 grid-cols-7 gap-2 rounded-lg text-center dark:bg-gray-800 ">
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2">Dom</div>
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2">Lun</div>
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2">Mar</div>
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2">Mié</div>
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2">Jue</div>
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2">Vie</div>
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2">Sáb</div>
                        <div class="py-3 text-base nunito-regular"></div>
                        <div class="py-3 text-base nunito-regular"></div>
                        <div class="py-3 text-base nunito-regular"></div>
                        <div class="py-3 text-base nunito-regular"></div>
                        <div class="py-3 text-base nunito-regular">1</div>
                        <div class="py-3 text-base nunito-regular">2</div>
                        <div class="py-3 text-base nunito-regular">3</div>
                        <div class="py-3 text-base nunito-regular">4</div>
                        <div class="py-3 text-base nunito-regular">5</div>
                        <div class="py-3 text-base nunito-regular">6</div>
                        <div class="py-3 text-base nunito-regular">7</div>
                        <div class="py-3 text-base nunito-regular">
                            <div class="font-bold nunito-bold">8</div>
                            <div class="mt-1 bg-blue-100 text-blue-700 rounded px-2 py-1 text-xs whitespace-nowrap flex items-center gap-1 cursor-pointer nunito-regular" @click="isDetailModalOpen = true; selectedEvent = {titulo: 'Reunión', hora: '10:00 am', estado: 'Programado', agencia: 'Agencia Central', direccion: 'Col. Centro, Tegucigalpa', cliente: 'Juan Pérez', tipo: 'Preventivo', orden: 'OS-00123', observaciones: 'Revisión general', diagnostico: 'Sin novedad'}">
                                <i class="fas fa-calendar-check mr-1"></i> Reunión<br><span class="font-normal">10:00 am</span>
                            </div>
                        </div>
                        <div class="py-3 text-base nunito-regular">9</div>
                        <div class="py-3 text-base nunito-regular">10</div>
                        <div class="py-3 text-base nunito-regular">11</div>
                        <div class="py-3 text-base nunito-regular">12</div>
                        <div class="py-3 text-base nunito-regular">13</div>
                        <div class="py-3 text-base nunito-regular">14</div>
                        <div class="py-3 text-base nunito-regular">15</div>
                        <div class="py-3 text-base nunito-regular">16</div>
                        <div class="py-3 text-base nunito-regular">17</div>
                        <div class="py-3 text-base nunito-regular">18</div>
                        <div class="py-3 text-base nunito-regular">19</div>
                        <div class="py-3 text-base nunito-regular">20</div>
                        <div class="py-3 text-base nunito-regular">21</div>
                        <div class="py-3 text-base nunito-regular">22</div>
                        <div class="py-3 text-base nunito-regular">23</div>
                        <div class="py-3 text-base nunito-regular">24</div>
                        <div class="py-3 text-base nunito-regular">25</div>
                        <div class="py-3 text-base nunito-regular">26</div>
                        <div class="py-3 text-base nunito-regular">27</div>
                        <div class="py-3 text-base nunito-regular">28</div>
                        <div class="py-3 text-base nunito-regular">29</div>
                        <div class="py-3 text-base nunito-regular">30</div>
                        <div class="py-3 text-base nunito-regular">31</div>
                    </div>
                </div>
            </div>
            <!-- Modal Detalle Evento -->
            <x-admin.edit-modal class="nunito-bold" modalName="isDetailModalOpen" title="Detalle del Evento" itemToEdit="selectedEvent">
                <div class="space-y-2">
                    <div><span class="font-bold nunito-bold">Título:</span> <span class="nunito-regular" x-text="selectedEvent?.titulo"></span></div>
                    <div><span class="font-bold nunito-bold">Fecha y hora:</span> 8 Julio 2025, <span class="nunito-regular" x-text="selectedEvent?.hora"></span></div>
                    <div><span class="font-bold nunito-bold">Estado:</span> <span :class="selectedEvent?.estado === 'Programado' ? 'text-blue-600' : selectedEvent?.estado === 'Realizado' ? 'text-green-600' : 'text-red-600'" class="nunito-regular" x-text="selectedEvent?.estado"></span></div>
                    <div><span class="font-bold nunito-bold">Agencia:</span> <span class="nunito-regular" x-text="selectedEvent?.agencia"></span></div>
                    <div><span class="font-bold nunito-bold">Dirección:</span> <span class="nunito-regular" x-text="selectedEvent?.direccion"></span></div>
                    <div><span class="font-bold nunito-bold">Cliente:</span> <span class="nunito-regular" x-text="selectedEvent?.cliente"></span></div>
                    <div><span class="font-bold nunito-bold">Tipo de mantenimiento:</span> <span class="nunito-regular" x-text="selectedEvent?.tipo"></span></div>
                    <div><span class="font-bold nunito-bold">Observaciones:</span> <span class="nunito-regular" x-text="selectedEvent?.observaciones"></span></div>
                    <div><span class="font-bold nunito-bold">Diagnóstico:</span> <span class="nunito-regular" x-text="selectedEvent?.diagnostico"></span></div>
                    <div class="flex gap-2 mt-4">
                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center nunito-regular" @click="isEditModalOpen = true"><i class="fas fa-edit mr-2"></i>Editar</button>
                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center nunito-regular" @click="isCancelModalOpen = true"><i class="fas fa-ban mr-2"></i>Cancelar</button>
                    </div>
                </div>
            </x-admin.edit-modal>
            <!-- Modal Crear Evento -->
            <x-admin.form-modal class="nunito-bold" modalName="isAddModalOpen" title="Agregar Evento" submitLabel="Guardar">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option class="nunito-regular">Agencia Central</option>
                            <option class="nunito-regular">Agencia Norte</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option class="nunito-regular">Juan Pérez</option>
                            <option class="nunito-regular">Ana López</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de mantenimiento</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option class="nunito-regular">Preventivo</option>
                            <option class="nunito-regular">Correctivo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Fecha y hora</label>
                        <input type="datetime-local" class="border rounded px-3 py-2 w-full nunito-regular" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option class="nunito-regular">Programado</option>
                            <option class="nunito-regular">Realizado</option>
                            <option class="nunito-regular">Cancelado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Observaciones</label>
                        <textarea class="border rounded px-3 py-2 w-full nunito-regular"></textarea>
                    </div>
                </div>
            </x-admin.form-modal>
            <!-- Modal Editar Evento -->
            <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Evento" itemToEdit="selectedEvent">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option :selected="selectedEvent?.agencia === 'Agencia Central'" class="nunito-regular">Agencia Central</option>
                            <option :selected="selectedEvent?.agencia === 'Agencia Norte'" class="nunito-regular">Agencia Norte</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option :selected="selectedEvent?.cliente === 'Juan Pérez'" class="nunito-regular">Juan Pérez</option>
                            <option :selected="selectedEvent?.cliente === 'Ana López'" class="nunito-regular">Ana López</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de mantenimiento</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option :selected="selectedEvent?.tipo === 'Preventivo'" class="nunito-regular">Preventivo</option>
                            <option :selected="selectedEvent?.tipo === 'Correctivo'" class="nunito-regular">Correctivo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Fecha y hora</label>
                        <input type="datetime-local" class="border rounded px-3 py-2 w-full nunito-regular" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                        <select class="border rounded px-3 py-2 w-full nunito-regular">
                            <option :selected="selectedEvent?.estado === 'Programado'" class="nunito-regular">Programado</option>
                            <option :selected="selectedEvent?.estado === 'Realizado'" class="nunito-regular">Realizado</option>
                            <option :selected="selectedEvent?.estado === 'Cancelado'" class="nunito-regular">Cancelado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Observaciones</label>
                        <textarea class="border rounded px-3 py-2 w-full nunito-regular" x-text="selectedEvent?.observaciones"></textarea>
                    </div>
                </div>
            </x-admin.edit-modal>
            <!-- Modal Cancelar Evento -->
            <x-admin.confirmation-modal class="nunito-bold" modalName="isCancelModalOpen" itemToDelete="selectedEvent" message="¿Está seguro que desea cancelar este evento? El estado cambiará a 'Cancelado'." />
        </div>

        <div x-show="tab==='eventosLista'" class="py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <h2 class="text-2xl font-semibold leading-tight nunito-bold mb-3 text-gray-800 dark:text-white">Lista de Eventos</h2>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <button class="transition duration-100 ease-in-out w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 flex items-center justify-center rounded-lg nunito-regular text-sm" @click="isAddModalOpen = true">
                        <i class="fas fa-plus mr-2"></i> Agregar Evento
                    </button>
                    <a href="/admin/reportes-header?modulo=Calendario&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition duration-100 ease-in-out whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2 mb-6">
                @include('partials.filtros-generales', [
                    'searchModel' => 'searchEventos',
                    'filtrosSelect' => [
                        'estadoEventoFiltro' => [
                            'label' => 'Estados',
                            'options' => ['Programado', 'Realizado', 'Cancelado']
                        ],
                        'agenciaEventoFiltro' => [
                            'label' => 'Agencias',
                            'options' => ['Agencia Central', 'Agencia Norte']
                        ]
                    ],
                    'ordenarOptions' => [
                        'fecha' => 'Fecha',
                        'estado' => 'Estado',
                        'cliente' => 'Cliente'
                    ]
                ])
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Evento 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white nunito-bold">Reunión Mensual</h3>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-semibold">Programado</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>8 de Julio, 2025</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-clock mr-2"></i>
                            <span>10:00 AM</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-user mr-2"></i>
                            <span>Juan Pérez</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-building mr-2"></i>
                            <span>Agencia Central</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-wrench mr-2"></i>
                            <span>Mantenimiento Preventivo</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t dark:border-gray-600 flex gap-2">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1" @click="isDetailModalOpen = true; selectedEvent = {titulo: 'Reunión Mensual', hora: '10:00 am', estado: 'Programado', agencia: 'Agencia Central', direccion: 'Col. Centro, Tegucigalpa', cliente: 'Juan Pérez', tipo: 'Preventivo', orden: 'OS-00123', observaciones: 'Revisión general', diagnostico: 'Sin novedad'}">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1" @click="isEditModalOpen = true">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1" @click="isCancelModalOpen = true">
                            <i class="fas fa-ban"></i> Cancelar
                        </button>
                    </div>
                </div>

                <!-- Evento 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white nunito-bold">Mantenimiento Correctivo</h3>
                        <span class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded-full text-xs font-semibold">Realizado</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>5 de Julio, 2025</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-clock mr-2"></i>
                            <span>2:00 PM</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-user mr-2"></i>
                            <span>Ana López</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-building mr-2"></i>
                            <span>Agencia Norte</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-tools mr-2"></i>
                            <span>Mantenimiento Correctivo</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t dark:border-gray-600 flex gap-2">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1" @click="isDetailModalOpen = true; selectedEvent = {titulo: 'Mantenimiento Correctivo', hora: '2:00 pm', estado: 'Realizado', agencia: 'Agencia Norte', direccion: 'Col. Norte, SPS', cliente: 'Ana López', tipo: 'Correctivo', orden: 'OS-00124', observaciones: 'Reparación urgente', diagnostico: 'Problema resuelto'}">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1" @click="isEditModalOpen = true">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                </div>

                <!-- Evento 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-red-500">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white nunito-bold">Inspección Técnica</h3>
                        <span class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 px-2 py-1 rounded-full text-xs font-semibold">Cancelado</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>3 de Julio, 2025</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-clock mr-2"></i>
                            <span>9:00 AM</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-user mr-2"></i>
                            <span>Carlos Mendoza</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-building mr-2"></i>
                            <span>Agencia Central</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <i class="fas fa-search mr-2"></i>
                            <span>Inspección Técnica</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t dark:border-gray-600 flex gap-2">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1" @click="isDetailModalOpen = true; selectedEvent = {titulo: 'Inspección Técnica', hora: '9:00 am', estado: 'Cancelado', agencia: 'Agencia Central', direccion: 'Col. Centro, Tegucigalpa', cliente: 'Carlos Mendoza', tipo: 'Inspección', orden: 'OS-00125', observaciones: 'Cancelado por cliente', diagnostico: 'No realizado'}">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1" @click="isEditModalOpen = true">
                            <i class="fas fa-redo"></i> Reprogramar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ver Detalles del Evento -->
        <x-admin.form-modal modalName="isDetailModalOpen" title="Detalles del Evento" submitLabel="" hideActions="true" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Información General</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Título del Evento</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.titulo"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Hora</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.hora"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Estado</label>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold nunito-bold" 
                                  :class="{
                                      'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedEvent.estado === 'Programado',
                                      'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300': selectedEvent.estado === 'Realizado',
                                      'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300': selectedEvent.estado === 'Cancelado'
                                  }" 
                                  x-text="selectedEvent.estado">
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Agencia</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.agencia"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Dirección</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.direccion"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Detalles del Servicio</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Cliente</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.cliente"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Tipo de Mantenimiento</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.tipo"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Orden de Servicio</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.orden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Observaciones</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.observaciones"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Diagnóstico</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x-text="selectedEvent.diagnostico"></p>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.form-modal>
    </div>
</div>
