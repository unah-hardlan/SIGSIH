<div x-data="{ tab: 'calendario', isAddModalOpen: false, isEditModalOpen: false, isDetailModalOpen: false, isCancelModalOpen: false, isAddCalendarioModalOpen: false, selectedEvent: null }" @include('partials.persist-tab', ['tabKey' => 'admin-calendario-tab']) class="container mx-auto px-4 sm:px-8">
    <div class="w-full">
        <ul class="flex border-b nunito-bold">
            <li @click="tab='calendario'" :class="tab==='calendario' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2">Calendario</li>
            <li @click="tab='calendarioCampos'" :class="tab==='calendarioCampos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2">Tabla Calendario</li>
        </ul>

        <div x-show="tab==='calendario'" class="py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <h2 class="text-2xl font-semibold leading-tight nunito-bold mb-3">Calendario</h2>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <button class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 flex items-center justify-center rounded-lg" @click="isAddModalOpen = true">
                        <i class="fas fa-plus mr-2"></i> Agregar
                    </button>
                    <a href="/admin/reportes-header?modulo=Calendario&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap flex items-center justify-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 mb-4">
                <select class="border rounded px-3 py-2 text-sm">
                    <option value="">Filtrar por agencia</option>
                    <option>Agencia Central</option>
                    <option>Agencia Norte</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm">
                    <option value="">Filtrar por estado</option>
                    <option>Programado</option>
                    <option>Realizado</option>
                    <option>Cancelado</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm">
                    <option value="">Filtrar por tipo de mantenimiento</option>
                    <option>Preventivo</option>
                    <option>Correctivo</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm">
                    <option value="">Filtrar por cliente</option>
                    <option>Juan Pérez</option>
                    <option>Ana López</option>
                </select>
            </div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden bg-white p-6">
                    <div class="flex items-center justify-between mb-4">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold"><i class="fas fa-chevron-left"></i></button>
                        <span class="text-lg font-semibold text-gray-700">Julio 2025</span>
                        <button class="text-blue-500 hover:text-blue-700 font-semibold"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="grid grid-cols-7 gap-2 text-center">
                        <div class="text-sm font-bold text-gray-600">Dom</div>
                        <div class="text-sm font-bold text-gray-600">Lun</div>
                        <div class="text-sm font-bold text-gray-600">Mar</div>
                        <div class="text-sm font-bold text-gray-600">Mié</div>
                        <div class="text-sm font-bold text-gray-600">Jue</div>
                        <div class="text-sm font-bold text-gray-600">Vie</div>
                        <div class="text-sm font-bold text-gray-600">Sáb</div>
                        <div class="py-3 text-base"></div>
                        <div class="py-3 text-base"></div>
                        <div class="py-3 text-base"></div>
                        <div class="py-3 text-base"></div>
                        <div class="py-3 text-base">1</div>
                        <div class="py-3 text-base">2</div>
                        <div class="py-3 text-base">3</div>
                        <div class="py-3 text-base">4</div>
                        <div class="py-3 text-base">5</div>
                        <div class="py-3 text-base">6</div>
                        <div class="py-3 text-base">7</div>
                        <div class="py-3 text-base">
                            <div class="font-bold">8</div>
                            <div class="mt-1 bg-blue-100 text-blue-700 rounded px-2 py-1 text-xs whitespace-nowrap flex items-center gap-1 cursor-pointer" @click="isDetailModalOpen = true; selectedEvent = {titulo: 'Reunión', hora: '10:00 am', estado: 'Programado', agencia: 'Agencia Central', direccion: 'Col. Centro, Tegucigalpa', cliente: 'Juan Pérez', tipo: 'Preventivo', orden: 'OS-00123', observaciones: 'Revisión general', diagnostico: 'Sin novedad'}">
                                <i class="fas fa-calendar-check mr-1"></i> Reunión<br><span class="font-normal">10:00 am</span>
                            </div>
                        </div>
                        <div class="py-3 text-base">9</div>
                        <div class="py-3 text-base">10</div>
                        <div class="py-3 text-base">11</div>
                        <div class="py-3 text-base">12</div>
                        <div class="py-3 text-base">13</div>
                        <div class="py-3 text-base">14</div>
                        <div class="py-3 text-base">15</div>
                        <div class="py-3 text-base">16</div>
                        <div class="py-3 text-base">17</div>
                        <div class="py-3 text-base">18</div>
                        <div class="py-3 text-base">19</div>
                        <div class="py-3 text-base">20</div>
                        <div class="py-3 text-base">21</div>
                        <div class="py-3 text-base">22</div>
                        <div class="py-3 text-base">23</div>
                        <div class="py-3 text-base">24</div>
                        <div class="py-3 text-base">25</div>
                        <div class="py-3 text-base">26</div>
                        <div class="py-3 text-base">27</div>
                        <div class="py-3 text-base">28</div>
                        <div class="py-3 text-base">29</div>
                        <div class="py-3 text-base">30</div>
                        <div class="py-3 text-base">31</div>
                    </div>
                </div>
            </div>
            <!-- Modal Detalle Evento -->
            <x-admin.edit-modal modalName="isDetailModalOpen" title="Detalle del Evento" itemToEdit="selectedEvent">
                <div class="space-y-2">
                    <div><span class="font-bold">Título:</span> <span x-text="selectedEvent?.titulo"></span></div>
                    <div><span class="font-bold">Fecha y hora:</span> 8 Julio 2025, <span x-text="selectedEvent?.hora"></span></div>
                    <div><span class="font-bold">Estado:</span> <span :class="selectedEvent?.estado === 'Programado' ? 'text-blue-600' : selectedEvent?.estado === 'Realizado' ? 'text-green-600' : 'text-red-600'" x-text="selectedEvent?.estado"></span></div>
                    <div><span class="font-bold">Agencia:</span> <span x-text="selectedEvent?.agencia"></span></div>
                    <div><span class="font-bold">Dirección:</span> <span x-text="selectedEvent?.direccion"></span></div>
                    <div><span class="font-bold">Cliente:</span> <span x-text="selectedEvent?.cliente"></span></div>
                    <div><span class="font-bold">Tipo de mantenimiento:</span> <span x-text="selectedEvent?.tipo"></span></div>
                    <div><span class="font-bold">Observaciones:</span> <span x-text="selectedEvent?.observaciones"></span></div>
                    <div><span class="font-bold">Diagnóstico:</span> <span x-text="selectedEvent?.diagnostico"></span></div>
                    <div class="flex gap-2 mt-4">
                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center" @click="isEditModalOpen = true"><i class="fas fa-edit mr-2"></i>Editar</button>
                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center" @click="isCancelModalOpen = true"><i class="fas fa-ban mr-2"></i>Cancelar</button>
                    </div>
                </div>
            </x-admin.edit-modal>
            <!-- Modal Crear Evento -->
            <x-admin.form-modal modalName="isAddModalOpen" title="Agregar Evento" submitLabel="Guardar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Agencia</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option>Agencia Central</option>
                            <option>Agencia Norte</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Cliente</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option>Juan Pérez</option>
                            <option>Ana López</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Tipo de mantenimiento</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option>Preventivo</option>
                            <option>Correctivo</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Fecha y hora</label>
                        <input type="datetime-local" class="border rounded px-3 py-2 w-full" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Estado</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option>Programado</option>
                            <option>Realizado</option>
                            <option>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Observaciones</label>
                        <textarea class="border rounded px-3 py-2 w-full"></textarea>
                    </div>
                </div>
            </x-admin.form-modal>
            <!-- Modal Editar Evento -->
            <x-admin.edit-modal modalName="isEditModalOpen" title="Editar Evento" itemToEdit="selectedEvent">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Agencia</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option :selected="selectedEvent?.agencia === 'Agencia Central'">Agencia Central</option>
                            <option :selected="selectedEvent?.agencia === 'Agencia Norte'">Agencia Norte</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Cliente</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option :selected="selectedEvent?.cliente === 'Juan Pérez'">Juan Pérez</option>
                            <option :selected="selectedEvent?.cliente === 'Ana López'">Ana López</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Tipo de mantenimiento</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option :selected="selectedEvent?.tipo === 'Preventivo'">Preventivo</option>
                            <option :selected="selectedEvent?.tipo === 'Correctivo'">Correctivo</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Fecha y hora</label>
                        <input type="datetime-local" class="border rounded px-3 py-2 w-full" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium mb-1">Estado</label>
                        <select class="border rounded px-3 py-2 w-full">
                            <option :selected="selectedEvent?.estado === 'Programado'">Programado</option>
                            <option :selected="selectedEvent?.estado === 'Realizado'">Realizado</option>
                            <option :selected="selectedEvent?.estado === 'Cancelado'">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Observaciones</label>
                        <textarea class="border rounded px-3 py-2 w-full" x-text="selectedEvent?.observaciones"></textarea>
                    </div>
                </div>
            </x-admin.edit-modal>
            <!-- Modal Cancelar Evento -->
            <x-admin.confirmation-modal modalName="isCancelModalOpen" itemToDelete="selectedEvent" message="¿Está seguro que desea cancelar este evento? El estado cambiará a 'Cancelado'." />
        </div>

        <div x-show="tab==='calendarioCampos'" class="overflow-x-auto w-full">
            <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
                <div class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
                    <h2 class="text-2xl text-gray-800 nunito-bold">Calendario</h2>
                    <button @click="isAddCalendarioModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nuevo Calendario</button>
                </div>
                <table class="min-w-full text-sm w-full">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left">ID Calendario</th>
                            <th class="py-2 px-4 text-left">Fecha</th>
                            <th class="py-2 px-4 text-left">Descripción</th>
                            <th class="py-2 px-4 text-left">ID Estado</th>
                            <th class="py-2 px-4 text-left">ID Cliente</th>
                            <th class="py-2 px-4 text-left">ID Agencia</th>
                            <th class="py-2 px-4 text-left">ID Tipo Mantenimiento</th>
                            <th class="py-2 px-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4">CAL-001</td>
                            <td class="py-2 px-4">2025-07-08</td>
                            <td class="py-2 px-4">Reunión mensual</td>
                            <td class="py-2 px-4">E-001</td>
                            <td class="py-2 px-4">CL-001</td>
                            <td class="py-2 px-4">AG-001</td>
                            <td class="py-2 px-4">TM-001</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
