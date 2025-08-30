<div x-data="{ tab: 'calendario', isAddModalOpen: false, isEditModalOpen: false, isDetailModalOpen: false, isCancelModalOpen: false, isAddCalendarioModalOpen: false, isEditCalendarioModalOpen: false, selectedEvent: null, calendarioToEdit: {fecha: '', descripcion: '', estado: '', cliente: '', agencia: '', tipo_mantenimiento: ''} }" @include('partials.persist-tab', ['tabKey' => 'admin-calendario-tab']) class="container mx-auto px-4 sm:px-8">
    <div class="w-full">
        <ul class="flex border-b nunito-bold">
            <li @click="tab='calendario'" :class="tab==='calendario' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Calendario</li>
            <li @click="tab='calendarioCampos'" :class="tab==='calendarioCampos' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Tabla Calendario</li>
        </ul>

        <div x-show="tab==='calendario'" class="py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <h2 class="text-2xl font-semibold leading-tight nunito-bold mb-3">Calendario</h2>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <button class="transition duration-100 ease-in-out w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 flex items-center justify-center rounded-lg nunito-regular" @click="isAddModalOpen = true">
                        <i class="fas fa-plus mr-2"></i> Agregar
                    </button>
                    <a href="/admin/reportes-header?modulo=Calendario&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                       class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-bold transition duration-100 ease-in-out whitespace-nowrap flex items-center justify-center gap-2">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 mb-4">
                <select class="border rounded px-3 py-2 text-sm nunito-regular">
                    <option class="nunito-regular" value="">Filtrar por agencia</option>
                    <option class="nunito-regular">Agencia Central</option>
                    <option class="nunito-regular">Agencia Norte</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm nunito-regular">
                    <option class="nunito-regular" value="">Filtrar por estado</option>
                    <option class="nunito-regular">Programado</option>
                    <option class="nunito-regular">Realizado</option>
                    <option class="nunito-regular">Cancelado</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm nunito-regular">
                    <option class="nunito-regular" value="">Filtrar por tipo de mantenimiento</option>
                    <option class="nunito-regular">Preventivo</option>
                    <option class="nunito-regular">Correctivo</option>
                </select>
                <select class="border rounded px-3 py-2 text-sm nunito-regular">
                    <option class="nunito-regular" value="">Filtrar por cliente</option>
                    <option class="nunito-regular">Juan Pérez</option>
                    <option class="nunito-regular">Ana López</option>
                </select>
            </div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden bg-white p-6">
                    <div class="flex items-center justify-between mb-4">
                        <button class="text-blue-500 hover:text-blue-700 font-semibold"><i class="fas fa-chevron-left"></i></button>
                        <span class="text-lg font-semibold text-gray-700 nunito-bold">Julio 2025</span>
                        <button class="text-blue-500 hover:text-blue-700 font-semibold"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="grid grid-cols-7 gap-2 text-center">
                        <div class="text-sm font-bold text-gray-600 nunito-bold">Dom</div>
                        <div class="text-sm font-bold text-gray-600 nunito-bold">Lun</div>
                        <div class="text-sm font-bold text-gray-600 nunito-bold">Mar</div>
                        <div class="text-sm font-bold text-gray-600 nunito-bold">Mié</div>
                        <div class="text-sm font-bold text-gray-600 nunito-bold">Jue</div>
                        <div class="text-sm font-bold text-gray-600 nunito-bold">Vie</div>
                        <div class="text-sm font-bold text-gray-600 nunito-bold">Sáb</div>
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

        <div x-show="tab==='calendarioCampos'" class="overflow-x-auto w-full">
            <div class="bg-white rounded-lg shadow p-6 mt-6 w-full">
                <div class="sticky top-0 z-10 bg-white pb-4 mb-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
                    <h2 class="text-2xl text-gray-800 nunito-bold">Calendario</h2>
                    <button @click="isAddCalendarioModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg nunito-bold transition whitespace-nowrap">Nuevo Calendario</button>
                </div>
                <table class="min-w-full text-sm w-full">
                    <thead class="bg-gray-100 nunito-bold">
                        <tr>
                            <th class="py-2 px-4 text-left nunito-bold">ID Calendario</th>
                            <th class="py-2 px-4 text-left nunito-bold">Fecha</th>
                            <th class="py-2 px-4 text-left nunito-bold">Descripción</th>
                            <th class="py-2 px-4 text-left nunito-bold">ID Estado</th>
                            <th class="py-2 px-4 text-left nunito-bold">ID Cliente</th>
                            <th class="py-2 px-4 text-left nunito-bold">ID Agencia</th>
                            <th class="py-2 px-4 text-left nunito-bold">ID Tipo Mantenimiento</th>
                            <th class="py-2 px-4 text-left nunito-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b nunito-regular">
                            <td class="py-2 px-4 nunito-regular">CAL-001</td>
                            <td class="py-2 px-4 nunito-regular">2025-07-08</td>
                            <td class="py-2 px-4 nunito-regular">Reunión mensual</td>
                            <td class="py-2 px-4 nunito-regular">E-001</td>
                            <td class="py-2 px-4 nunito-regular">CL-001</td>
                            <td class="py-2 px-4 nunito-regular">AG-001</td>
                            <td class="py-2 px-4 nunito-regular">TM-001</td>
                            <td class="py-2 px-4 flex gap-2">
                                <a href="#" @click.prevent="isEditCalendarioModalOpen = true; calendarioToEdit = {fecha: '2025-07-08', descripcion: 'Reunión mensual', estado: 'E-001', cliente: 'CL-001', agencia: 'AG-001', tipo_mantenimiento: 'TM-001'}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                                <a href="#" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                        <!-- Modal Crear Calendario -->
                        <x-admin.form-modal class="nunito-bold" modalName="isAddCalendarioModalOpen" title="Agregar Calendario" submitLabel="Guardar Calendario" maxWidth="max-w-xs xl:max-w-2xl 2xl:max-w-3xl">
                            <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Fecha</label>
                                    <input type="date" class="border rounded px-3 py-2 w-full nunito-regular" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                                    <textarea class="border rounded px-3 py-2 w-full nunito-regular"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Tipo Mantenimiento</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" />
                                </div>
                            </div>
                        </x-admin.form-modal>
                        <!-- Modal Editar Calendario -->
                        <x-admin.edit-modal class="nunito-bold" modalName="isEditCalendarioModalOpen" title="Editar Calendario" itemToEdit="calendarioToEdit" maxWidth="max-w-xs xl:max-w-2xl 2xl:max-w-3xl">
                            <div class="flex flex-col gap-4 xl:grid xl:grid-cols-2 xl:gap-6">
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Fecha</label>
                                    <input type="date" class="border rounded px-3 py-2 w-full nunito-regular" x-model="calendarioToEdit.fecha" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                                    <textarea class="border rounded px-3 py-2 w-full nunito-regular" x-model="calendarioToEdit.descripcion"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" x-model="calendarioToEdit.estado" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" x-model="calendarioToEdit.cliente" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" x-model="calendarioToEdit.agencia" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1 nunito-bold">Tipo Mantenimiento</label>
                                    <input type="text" class="border rounded px-3 py-2 w-full nunito-regular" x-model="calendarioToEdit.tipo_mantenimiento" />
                                </div>
                            </div>
                        </x-admin.edit-modal>
            </div>
        </div>
    </div>
</div>
