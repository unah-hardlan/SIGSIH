<div x-data="{ 
    tab: 'calendario', 
    isAddModalOpen: false, 
    isEditModalOpen: false, 
    isDetailModalOpen: false, 
    isCancelModalOpen: false, 
    isAddCalendarioModalOpen: false, 
    isEditCalendarioModalOpen: false, 
    selectedEvent: null, 
    calendarioToEdit: {fecha: '', descripcion: '', estado: '', cliente: '', agencia: '', tipo_mantenimiento: ''},
    // Calendar variables
    currentYear: new Date().getFullYear(),
    currentMonth: new Date().getMonth(),
    isMonthModalOpen: false,
    selectedMonth: new Date().getMonth(),
    selectedYear: new Date().getFullYear(),
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    dayNames: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
    events: {
    // Evento de prueba para hoy (visualización de colores)
    '2025-09-05': [{titulo: 'Visita Técnica (Hoy)', hora: '11:00 am', estado: 'Programado', agencia: 'Agencia Central', direccion: 'Col. Centro, Tegucigalpa', cliente: 'Cliente Demo', tipo: 'Preventivo', orden: 'OS-00999', observaciones: 'Evento de prueba para colores', diagnostico: 'N/A'}],
        '2025-07-08': [{titulo: 'Reunión', hora: '10:00 am', estado: 'Programado', agencia: 'Agencia Central', direccion: 'Col. Centro, Tegucigalpa', cliente: 'Juan Pérez', tipo: 'Preventivo', orden: 'OS-00123', observaciones: 'Revisión general', diagnostico: 'Sin novedad'}],
        '2025-07-15': [{titulo: 'Mantenimiento', hora: '2:00 pm', estado: 'Realizado', agencia: 'Agencia Norte', direccion: 'Col. Norte, SPS', cliente: 'María López', tipo: 'Correctivo', orden: 'OS-00124', observaciones: 'Reparación urgente', diagnostico: 'Resuelto'}]
    },
    
    // Calendar methods
    getDaysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
    },
    
    getFirstDayOfMonth(year, month) {
        return new Date(year, month, 1).getDay();
    },
    
    getCalendarDays() {
        const daysInMonth = this.getDaysInMonth(this.currentYear, this.currentMonth);
        const firstDay = this.getFirstDayOfMonth(this.currentYear, this.currentMonth);
        const days = [];
        
        // Add empty days for previous month
        for (let i = 0; i < firstDay; i++) {
            days.push({ day: '', isEmpty: true });
        }
        
        // Add days of current month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            days.push({ 
                day: day, 
                isEmpty: false, 
                dateStr: dateStr,
                events: this.events[dateStr] || []
            });
        }
        
        return days;
    },
    
    previousMonth() {
        if (this.currentMonth === 0) {
            this.currentMonth = 11;
            this.currentYear--;
        } else {
            this.currentMonth--;
        }
    },
    
    nextMonth() {
        if (this.currentMonth === 11) {
            this.currentMonth = 0;
            this.currentYear++;
        } else {
            this.currentMonth++;
        }
    },
    
    goToToday() {
        const today = new Date();
        this.currentYear = today.getFullYear();
        this.currentMonth = today.getMonth();
    },
    
    isToday(day) {
        const today = new Date();
        return day === today.getDate() && 
               this.currentMonth === today.getMonth() && 
               this.currentYear === today.getFullYear();
    }
}" @include('partials.persist-tab', ['tabKey' => 'admin-calendario-tab']) class="container mx-auto px-4 sm:px-8">
    <div class="w-full">
        <ul class="flex border-b nunito-bold">
            <li @click="tab='calendario'" :class="tab==='calendario' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-300 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Calendario</li>
            <li @click="tab='eventosLista'" :class="tab==='eventosLista' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-300 hover:text-blue-500 cursor-pointer'" class="mr-6 pb-2 nunito-bold">Lista de Eventos</li>
        </ul>

                <div x-show="tab==='calendario'" class="py-8">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <h2 class="text-2xl font-semibold leading-tight nunito-bold mb-3 text-gray-800 dark:text-white">Calendario</h2>
                                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                        <a href="/admin/reportes-header?modulo=Calendario&fecha={{ now()->format('d-M-Y') }}" target="_blank"
                                             class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition duration-100 ease-in-out whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                                                <i class="fas fa-file-alt"></i> Generar Reporte
                                        </a>
                                </div>
                        </div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-2xl overflow-hidden bg-white dark:bg-gray-900 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <button @click="previousMonth()" class="text-blue-500 hover:text-blue-700 font-semibold p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors">
                            <i class="fas fa-chevron-left text-lg"></i>
                        </button>
                        <span class="text-xl font-semibold text-gray-700 dark:text-white nunito-bold cursor-pointer hover:underline" @click="isMonthModalOpen = true" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                        <button @click="nextMonth()" class="text-blue-500 hover:text-blue-700 font-semibold p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors">
                            <i class="fas fa-chevron-right text-lg"></i>
                        </button>
                    </div>
                    <div class="flex justify-end mb-2">
                        <button @click="goToToday()" class="text-sm bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg nunito-regular transition-colors shadow">
                            <i class="fas fa-calendar-day mr-2"></i> Hoy
                        </button>
                    </div>
                    
                    <div class="grid p-4 grid-cols-7 gap-2 rounded-lg text-center dark:bg-gray-800">
                        <template x-for="dayName in dayNames" :key="dayName">
                            <div class="text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-xl py-3 mx-2" x-text="dayName"></div>
                        </template>
                        
                        <template x-for="dayData in getCalendarDays()" :key="dayData.dateStr || Math.random()">
                            <div class="text-base nunito-regular min-h-24 relative"
                                 :class="dayData.isEmpty ? '' : [
                                     isToday(dayData.day) ? 'bg-blue-50 dark:bg-blue-900/40 ring-2 ring-blue-500 dark:ring-blue-400/70' : 'hover:bg-blue-50 dark:hover:bg-blue-800/40',
                                     'rounded-lg cursor-pointer transition-all px-1 pt-1 pb-2 flex flex-col gap-1 dark:border dark:border-gray-700/40'
                                 ].join(' ')"
                                 @click="if(!dayData.isEmpty){ isAddModalOpen = true; calendarioToEdit.fecha = dayData.dateStr; }">
                                <template x-if="!dayData.isEmpty">
                                    <div class="flex flex-col h-full">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-bold nunito-bold text-gray-700 dark:text-gray-200 text-sm"
                                                  :class="isToday(dayData.day) ? 'text-blue-700 dark:text-blue-300' : ''" x-text="dayData.day"></span>
                                        </div>
                                        <div class="flex flex-col gap-1 overflow-hidden">
                                            <template x-for="event in dayData.events" :key="event.titulo">
                                                <div @click.stop="isEditModalOpen = true; selectedEvent = event"
                                                     class="relative group w-full rounded-md pl-3 pr-2 py-1.5 text-[11px] leading-tight flex items-center gap-2 cursor-pointer transition-all backdrop-blur-sm overflow-hidden border shadow-sm dark:shadow-none"
                                                     :class="{
                                                         'bg-indigo-50/90 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-200 border-indigo-200/70 dark:border-indigo-500/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50': event.estado==='Programado',
                                                         'bg-emerald-50/90 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-200 border-emerald-200/70 dark:border-emerald-500/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50': event.estado==='Realizado',
                                                         'bg-rose-50/90 dark:bg-rose-950/30 text-rose-700 dark:text-rose-200 border-rose-200/70 dark:border-rose-500/30 hover:bg-rose-100 dark:hover:bg-rose-900/50': event.estado==='Cancelado'
                                                     }">
                                                    <span class="absolute left-0 top-0 h-full w-1"
                                                          :class="{
                                                             'bg-indigo-500': event.estado==='Programado',
                                                             'bg-emerald-500': event.estado==='Realizado',
                                                             'bg-rose-500': event.estado==='Cancelado'
                                                          }"></span>
                                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                                          :class="{
                                                             'bg-indigo-500': event.estado==='Programado',
                                                             'bg-emerald-500': event.estado==='Realizado',
                                                             'bg-rose-500': event.estado==='Cancelado'
                                                          }"></span>
                                                    <div class="flex-1 min-w-0 flex items-center gap-1">
                                                        <span class="truncate font-medium tracking-tight" x-text="event.titulo"></span>
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded-md ml-auto font-semibold ring-1 ring-inset"
                                                              :class="{
                                                                 'bg-indigo-100/70 dark:bg-indigo-800/40 text-indigo-700 dark:text-indigo-200 ring-indigo-300/60 dark:ring-indigo-500/40': event.estado==='Programado',
                                                                 'bg-emerald-100/70 dark:bg-emerald-800/40 text-emerald-700 dark:text-emerald-200 ring-emerald-300/60 dark:ring-emerald-500/40': event.estado==='Realizado',
                                                                 'bg-rose-100/70 dark:bg-rose-800/40 text-rose-700 dark:text-rose-200 ring-rose-300/60 dark:ring-rose-500/40': event.estado==='Cancelado'
                                                              }" x-text="event.hora"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
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
                            <p class="text-gray-800 dark:text-white nunito-regular" x_text="selectedEvent.direccion"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Detalles del Servicio</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Cliente</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x_text="selectedEvent.cliente"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Tipo de Mantenimiento</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x_text="selectedEvent.tipo"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Orden de Servicio</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x_text="selectedEvent.orden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Observaciones</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x_text="selectedEvent.observaciones"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Diagnóstico</label>
                            <p class="text-gray-800 dark:text-white nunito-regular" x_text="selectedEvent.diagnostico"></p>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.form-modal>
    </div>

    <!-- Modal Selección de Mes/Año -->
    <div x-show="isMonthModalOpen" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-md">
        <div x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-200 transform" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-xs mx-auto">
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Seleccionar mes y año</h3>
            <div class="mb-4">
                <select x-model="selectedMonth" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700">
                    <template x-for="(name, idx) in monthNames" :key="idx">
                        <option :value="idx" x-text="name"></option>
                    </template>
                </select>
            </div>
            <div class="mb-4">
                <input type="number" x-model="selectedYear" min="1900" max="2100" class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700" />
            </div>
            <div class="flex justify-end gap-2">
                <button @click="isMonthModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg nunito-regular hover:bg-gray-300 dark:hover:bg-gray-600">Cancelar</button>
                <button @click="currentMonth = selectedMonth; currentYear = selectedYear; isMonthModalOpen = false" class="px-4 py-2 bg-blue-600 text-white rounded-lg nunito-regular hover:bg-blue-700">Seleccionar</button>
            </div>
        </div>
    </div>
</div>
