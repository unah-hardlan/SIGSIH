<div x-data="{
    tab: 'calendario',
    canInsertCal: @perm(['Gestión de Calendario','Gestion de Calendario'], 'insercion') true @else false @endperm,
    canEditCal: @perm(['Gestión de Calendario','Gestion de Calendario'], 'actualizacion') true @else false @endperm,
    canDeleteCal: @perm(['Gestión de Calendario','Gestion de Calendario'], 'eliminacion') true @else false @endperm,
    isAddModalOpen: false,
    isEditModalOpen: false,
    isDetailModalOpen: false,
    isCancelModalOpen: false,
    isAddCalendarioModalOpen: false,
    isEditCalendarioModalOpen: false,
    selectedEvent: null,
    isDeleteModalOpen: false,
    eventToDelete: null,
    isAddListModalOpen: false,
    isEditListModalOpen: false,
    isDetailListModalOpen: false,
    isDeleteListModalOpen: false,
    selectedEventLista: null,
    eventToDeleteLista: null,
    calendarioToEdit: {fecha: '', descripcion: '', estado: '', cliente: '', agencia: '', tipo_mantenimiento: ''},
    openAdd(dateStr = null){
        const now = dateStr ? new Date(dateStr + 'T08:00:00') : new Date();
        const pad = (n)=>String(n).padStart(2,'0');
        const fecha = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T08:00`;
        this.formEvento = { fecha, descripcion_calendario:'', observaciones_calendario:'', id_estado_calendario_fk:'', id_agencias_fk:'', id_orden_servicio_fk:'', id_tipo_mantenimiento_fk:'', id_cliente_fk:'', _touched: {} };
        this.selectedEvent = null;
        this.isAddModalOpen = true;
    },
    openAddList(dateStr = null){
        const now = dateStr ? new Date(dateStr + 'T08:00:00') : new Date();
        const pad = (n)=>String(n).padStart(2,'0');
        const fecha = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T08:00`;
        this.formEventoLista = { fecha, descripcion_calendario:'', observaciones_calendario:'', id_estado_calendario_fk:'', id_agencias_fk:'', id_orden_servicio_fk:'', id_tipo_mantenimiento_fk:'', id_cliente_fk:'', _touched: {} };
        this.selectedEventLista = null;
        this.isAddListModalOpen = true;
    },
    formEvento: {
        fecha: '',
        descripcion_calendario: '',
        observaciones_calendario: '',
        id_estado_calendario_fk: '',
        id_agencias_fk: '',
        id_orden_servicio_fk: '',
        id_tipo_mantenimiento_fk: '',
        id_cliente_fk: '',
        _touched: {},
    },
    formEventoLista: {
        fecha: '',
        descripcion_calendario: '',
        observaciones_calendario: '',
        id_estado_calendario_fk: '',
        id_agencias_fk: '',
        id_orden_servicio_fk: '',
        id_tipo_mantenimiento_fk: '',
        id_cliente_fk: '',
        _touched: {},
    },
    submitting: false,
    loadingCatalogs: false,
    loadingEvents: false,
    loadingFilteredClientes: false,
    loadingFilteredOrdenes: false,
    catalogAgencias: [],
    catalogEstados: [],
    catalogTiposMantenimiento: [],
    catalogClientes: [],
    filteredClientes: [],
    filteredOrdenesServicio: [],
    catalogOrdenesServicio: [],
    currentYear: new Date().getFullYear(),
    currentMonth: new Date().getMonth(),
    isMonthModalOpen: false,
    selectedMonth: new Date().getMonth(),
    selectedYear: new Date().getFullYear(),
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    dayNames: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
    events: {},
    searchEventos: '',
    estadoEventoFiltro: '',
    agenciaEventoFiltro: '',
    ordenarPor: 'fecha', // <-- CORREGIDO AQUÍ

    flattenEvents(){
        const out = [];
        for(const day in this.events){
            const arr = this.events[day]||[];
            for(const ev of arr){ out.push({ ...ev, __day: day }); }
        }
        return out;
    },
    formatDateEs(dateStr){
        const d = (dateStr||'').slice(0,10).split('-');
        if(d.length<3) return dateStr||'';
        const y=+d[0], m=+d[1]-1, day=+d[2];
        const nombreMes = this.monthNames[m] || '';
        return `${day} de ${nombreMes}, ${y}`;
    },
    filteredEvents(){
        const q = (this.searchEventos||'').toLowerCase();
        const estado = (this.estadoEventoFiltro||'').toLowerCase();
        const agencia = (this.agenciaEventoFiltro||'').toLowerCase();
        let list = this.flattenEvents().filter(ev => {
            const hayQ = !q || [ev.titulo, ev.cliente, ev.agencia, ev.tipo, ev.direccion].some(v=> (v||'').toLowerCase().includes(q));
            const okEstado = !estado || (ev.estado||'').toLowerCase() === estado;
            const okAgencia = !agencia || (ev.agencia||'').toLowerCase() === agencia;
            return hayQ && okEstado && okAgencia;
        });
        const by = this.ordenarPor||'fecha';
        list.sort((a,b)=>{
            if(by==='estado') return (a.estado||'').localeCompare(b.estado||'');
            if(by==='cliente') return (a.cliente||'').localeCompare(b.cliente||'');
            return (a.raw?.fecha||'').localeCompare(b.raw?.fecha||'');
        });
        return list;
    },
    openEdit(ev){
        if(!ev) return;
        // Clone to break references and force Alpine reactivity
        const cloned = JSON.parse(JSON.stringify(ev));
        this.selectedEvent = cloned; 
        if(!this.canEditCal){ this.isDetailModalOpen = true; return; }
        const raw = cloned.raw || {}; 
        const fecha = (raw.fecha||'').replace(' ','T').slice(0,16);
        this.formEvento = {
            fecha,
            descripcion_calendario: raw.descripcion_calendario || cloned.titulo || '',
            observaciones_calendario: raw.observaciones_calendario || cloned.observaciones || '',
            id_estado_calendario_fk: raw.id_estado_calendario_fk || '',
            id_agencias_fk: raw.id_agencias_fk || '',
            id_orden_servicio_fk: raw.id_orden_servicio_fk || '',
            id_tipo_mantenimiento_fk: raw.id_tipo_mantenimiento_fk || '',
            id_cliente_fk: raw.id_cliente_fk || '',
            _touched: {}
        };
        try { window.calendarioApiHandlers && window.calendarioApiHandlers.onAgenciaChange(this, this.formEvento.id_agencias_fk); } catch(_) {}
        try { window.calendarioApiHandlers && window.calendarioApiHandlers.onClienteChange(this, this.formEvento.id_cliente_fk); } catch(_) {}
        requestAnimationFrame(()=>{ this.isEditModalOpen = true; });
    },
    openEditList(ev){
        if(!ev) return;
        const cloned = JSON.parse(JSON.stringify(ev));
        this.selectedEventLista = cloned;
        const raw = cloned.raw || {};
        const fecha = (raw.fecha||'').replace(' ','T').slice(0,16);
        this.formEventoLista = {
            fecha,
            descripcion_calendario: raw.descripcion_calendario || cloned.titulo || '',
            observaciones_calendario: raw.observaciones_calendario || cloned.observaciones || '',
            id_estado_calendario_fk: raw.id_estado_calendario_fk || '',
            id_agencias_fk: raw.id_agencias_fk || '',
            id_orden_servicio_fk: raw.id_orden_servicio_fk || '',
            id_tipo_mantenimiento_fk: raw.id_tipo_mantenimiento_fk || '',
            id_cliente_fk: raw.id_cliente_fk || '',
            _touched: {}
        };
        try { window.calendarioApiHandlers && window.calendarioApiHandlers.onAgenciaChange(this, this.formEventoLista.id_agencias_fk); } catch(_) {}
        try { window.calendarioApiHandlers && window.calendarioApiHandlers.onClienteChange(this, this.formEventoLista.id_cliente_fk); } catch(_) {}
        requestAnimationFrame(()=>{ this.isEditListModalOpen = true; });
    },
    async quickDelete(ev){
        if(!ev) return;
        this.openDelete(ev);
    },
    openDelete(ev){
        if(!ev) return; if(!this.canDeleteCal){ window.showToast && window.showToast('Sin permiso para eliminar', 'warning'); return; } this.eventToDelete = ev; this.isDeleteModalOpen = true;
    },
    openDeleteList(ev){ if(!ev) return; this.eventToDeleteLista = ev; this.isDeleteListModalOpen = true; },
    async confirmDelete(){
        if(!this.eventToDelete) return; 
        await window.calendarioApiHandlers.deleteEvent(this, this.eventToDelete.id);
        this.isDeleteModalOpen = false; this.eventToDelete = null;
    },
    async confirmDeleteList(){
        if(!this.eventToDeleteLista) return;
        await window.calendarioApiHandlers.deleteEvent(this, this.eventToDeleteLista.id);
        this.isDeleteListModalOpen = false; this.eventToDeleteLista = null;
    },
    cancelDelete(){ this.isDeleteModalOpen = false; this.eventToDelete = null; },
    cancelDeleteList(){ this.isDeleteListModalOpen = false; this.eventToDeleteLista = null; },
    
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
        
        for (let i = 0; i < firstDay; i++) {
            days.push({ day: '', isEmpty: true });
        }
        
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
    },
    async init() {
        const now = new Date();
        const pad = (n) => String(n).padStart(2,'0');
        this.formEvento.fecha = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T08:00`;
        await window.calendarioApiHandlers.loadCatalogs(this);
        await window.calendarioApiHandlers.fetchMonth(this);
        this.$watch('isEditModalOpen', (open) => { if(!open){ this.selectedEvent=null; } });
        this.$watch('isEditListModalOpen', (open) => { if(!open){ this.selectedEventLista=null; } });
        this.$watch('formEvento.id_agencias_fk', (val) => {
            if(!val){ this.formEvento.id_cliente_fk=''; this.filteredClientes=[]; }
            window.calendarioApiHandlers.onAgenciaChange(this, val);
        });
        this.$watch('formEventoLista.id_agencias_fk', (val) => {
            if(!val){ this.formEventoLista.id_cliente_fk=''; this.filteredClientes=[]; }
            window.calendarioApiHandlers.onAgenciaChange(this, val);
        });
        this.$watch('formEvento.id_cliente_fk', (val) => {
            if(!val){ this.formEvento.id_orden_servicio_fk=''; this.filteredOrdenesServicio=[]; }
            window.calendarioApiHandlers.onClienteChange(this, val);
        });
        this.$watch('formEventoLista.id_cliente_fk', (val) => {
            if(!val){ this.formEventoLista.id_orden_servicio_fk=''; this.filteredOrdenesServicio=[]; }
            window.calendarioApiHandlers.onClienteChange(this, val);
        });
    }
}" @include('partials.persist-tab', ['tabKey'=> 'admin-calendario-tab']) class="container mx-auto px-1 sm:px-8">
    <div class="w-full">
        <ul class="flex border-b nunito-bold overflow-x-auto">
            <li @click="tab='calendario'"
                :class="tab==='calendario' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-300 hover:text-blue-500 cursor-pointer'"
                class="mr-4 sm:mr-6 pb-2 nunito-bold whitespace-nowrap text-sm sm:text-base">Calendario</li>
            <li @click="tab='eventosLista'"
                :class="tab==='eventosLista' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600 dark:text-gray-300 hover:text-blue-500 cursor-pointer'"
                class="mr-4 sm:mr-6 pb-2 nunito-bold whitespace-nowrap text-sm sm:text-base">Lista de Eventos</li>
        </ul>

        <div x-show="tab==='calendario'" class="py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-2xl font-semibold leading-tight nunito-bold mb-3 text-gray-800 dark:text-white">
                    Calendario</h2>
            </div>
            <div class="mx-0 sm:-mx-8 px-1 sm:px-8 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-2xl overflow-hidden bg-white dark:bg-gray-900 p-2 sm:p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-4 sm:mb-6">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <button
                                @click="previousMonth(); $nextTick(() => window.calendarioApiHandlers.fetchMonth($data))"
                                class="text-blue-500 hover:text-blue-700 font-semibold p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors">
                                <i class="fas fa-chevron-left text-lg"></i>
                            </button>
                            <span
                                class="text-lg sm:text-xl font-semibold text-gray-700 dark:text-white nunito-bold cursor-pointer hover:underline"
                                @click="isMonthModalOpen = true"
                                x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                            <button @click="nextMonth(); $nextTick(() => window.calendarioApiHandlers.fetchMonth($data))"
                                class="text-blue-500 hover:text-blue-700 font-semibold p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors">
                                <i class="fas fa-chevron-right text-lg"></i>
                            </button>
                        </div>
                        <button @click="goToToday(); $nextTick(() => window.calendarioApiHandlers.fetchMonth($data))"
                            class="w-full sm:w-auto text-sm bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg nunito-regular transition-colors">
                            <i class="fas fa-calendar-day mr-2"></i> Hoy
                        </button>
                    </div>

                    <div class="grid p-0.5 sm:p-4 grid-cols-7 gap-0.5 sm:gap-2 rounded-lg text-center dark:bg-gray-800">
                        <template x-for="dayName in dayNames" :key="dayName">
                            <div class="text-xs sm:text-base font-bold text-gray-600 dark:text-gray-300 nunito-bold bg-gray-200 dark:bg-gray-700 rounded-sm sm:rounded-xl py-1 sm:py-3 mx-0 sm:mx-2"
                                x-text="dayName"></div>
                        </template>

                        <template x-for="dayData in getCalendarDays()" :key="dayData.dateStr || Math.random()">
                            <div class="text-xs sm:text-base nunito-regular min-h-10 sm:min-h-24 relative" :class="dayData.isEmpty ? '' : [
                                     isToday(dayData.day) ? 'bg-blue-50 dark:bg-blue-900/40 ring-1 sm:ring-2 ring-blue-500 dark:ring-blue-400/70' : 'hover:bg-blue-50 dark:hover:bg-blue-800/40',
                                     'rounded-sm sm:rounded-lg cursor-pointer transition-all px-0.5 sm:px-1 pt-0.5 sm:pt-1 pb-0.5 sm:pb-2 flex flex-col gap-0.5 sm:gap-1 dark:border dark:border-gray-700/40'
                                 ].join(' ')"
                                @click="if(!dayData.isEmpty){ if(canInsertCal){ calendarioToEdit.fecha = dayData.dateStr; openAdd(dayData.dateStr); } else { window.showToast && window.showToast('Sin permiso para crear', 'warning'); } }">
                                <template x-if="!dayData.isEmpty">
                                    <div class="flex flex-col h-full">
                                        <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                                            <span class="font-bold nunito-bold text-gray-700 dark:text-gray-200 text-xs sm:text-sm"
                                                :class="isToday(dayData.day) ? 'text-blue-700 dark:text-blue-300' : ''"
                                                x-text="dayData.day"></span>
                                        </div>
                                        <div class="flex flex-col gap-0.5 sm:gap-1 overflow-hidden">
                                            <template x-for="event in dayData.events" :key="event.id">
                                                <div @click.stop="openEdit(event)"
                                                    class="relative group w-full rounded-sm sm:rounded-md pl-1.5 sm:pl-3 pr-1 sm:pr-2 py-0.5 sm:py-1.5 text-[9px] sm:text-[11px] leading-tight flex items-center gap-1 sm:gap-2 cursor-pointer transition-all backdrop-blur-sm overflow-hidden border shadow-sm dark:shadow-none"
                                                    :class="{
                                                         'bg-indigo-50/90 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-200 border-indigo-200/70 dark:border-indigo-500/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50': event.estado==='Programado',
                                                         'bg-emerald-50/90 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-200 border-emerald-200/70 dark:border-emerald-500/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50': event.estado==='Realizado',
                                                         'bg-rose-50/90 dark:bg-rose-950/30 text-rose-700 dark:text-rose-200 border-rose-200/70 dark:border-rose-500/30 hover:bg-rose-100 dark:hover:bg-rose-900/50': event.estado==='Cancelado'
                                                     }">
                                                    <span class="absolute left-0 top-0 h-full w-0.5 sm:w-1" :class="{
                                                             'bg-indigo-500': event.estado==='Programado',
                                                             'bg-emerald-500': event.estado==='Realizado',
                                                             'bg-rose-500': event.estado==='Cancelado'
                                                          }"></span>
                                                    <span class="w-1 h-1 sm:w-2 sm:h-2 rounded-full flex-shrink-0" :class="{
                                                             'bg-indigo-500': event.estado==='Programado',
                                                             'bg-emerald-500': event.estado==='Realizado',
                                                             'bg-rose-500': event.estado==='Cancelado'
                                                          }"></span>
                                                    <div class="flex-1 min-w-0 flex items-center gap-0.5 sm:gap-1">
                                                        <span class="truncate font-medium tracking-tight text-[9px] sm:text-xs"
                                                            x-text="event.titulo"></span>
                                                        <span
                                                            class="text-[7px] sm:text-[10px] px-1 sm:px-1.5 py-0.5 rounded-sm sm:rounded-md ml-auto font-semibold ring-1 ring-inset"
                                                            :class="{
                                                                 'bg-indigo-100/70 dark:bg-indigo-800/40 text-indigo-700 dark:text-indigo-200 ring-indigo-300/60 dark:ring-indigo-500/40': event.estado==='Programado',
                                                                 'bg-emerald-100/70 dark:bg-emerald-800/40 text-emerald-700 dark:text-emerald-200 ring-emerald-300/60 dark:ring-emerald-500/40': event.estado==='Realizado',
                                                                 'bg-rose-100/70 dark:bg-rose-800/40 text-rose-700 dark:text-rose-200 ring-rose-300/60 dark:ring-rose-500/40': event.estado==='Cancelado'
                                                              }" x-text="event.hora"></span>
                                                        @perm(['Gestión de Calendario','Gestion de Calendario'],
                                                        'eliminacion')
                                                        <button @click.stop="openDelete(event)"
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity text-red-600 hover:text-red-700 flex-shrink-0 text-[8px]"
                                                            title="Eliminar">
                                                            <i class="fas fa-trash-alt text-[8px]"></i>
                                                        </button>
                                                        @else
                                                        <span
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-400 cursor-not-allowed flex-shrink-0 text-[8px]"
                                                            title="Sin permiso para eliminar">
                                                            <i class="fas fa-trash-alt text-[8px]"></i>
                                                        </span>
                                                        @endperm
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
            <x-admin.edit-modal class="nunito-bold" modalName="isDetailModalOpen" title="Detalle del Evento"
                itemToEdit="selectedEvent">
                <div class="space-y-2">
                    <div><span class="font-bold nunito-bold">Título:</span> <span class="nunito-regular"
                            x-text="selectedEvent?.titulo"></span></div>
                    <div><span class="font-bold nunito-bold">Fecha y hora:</span> 8 Julio 2025, <span
                            class="nunito-regular" x-text="selectedEvent?.hora"></span></div>
                    <div><span class="font-bold nunito-bold">Estado:</span> <span
                            :class="selectedEvent?.estado === 'Programado' ? 'text-blue-600' : selectedEvent?.estado === 'Realizado' ? 'text-green-600' : 'text-red-600'"
                            class="nunito-regular" x-text="selectedEvent?.estado"></span></div>
                    <div><span class="font-bold nunito-bold">Agencia:</span> <span class="nunito-regular"
                            x-text="selectedEvent?.agencia"></span></div>
                    <div><span class="font-bold nunito-bold">Dirección:</span> <span class="nunito-regular"
                            x-text="selectedEvent?.direccion"></span></div>
                    <div><span class="font-bold nunito-bold">Cliente:</span> <span class="nunito-regular"
                            x-text="selectedEvent?.cliente"></span></div>
                    <div><span class="font-bold nunito-bold">Tipo de mantenimiento:</span> <span class="nunito-regular"
                            x-text="selectedEvent?.tipo"></span></div>
                    <div><span class="font-bold nunito-bold">Observaciones:</span> <span class="nunito-regular"
                            x-text="selectedEvent?.observaciones"></span></div>
                    <div><span class="font-bold nunito-bold">Diagnóstico:</span> <span class="nunito-regular"
                            x-text="selectedEvent?.diagnostico"></span></div>
                    <div class="flex gap-2 mt-4">
                        @perm(['Gestión de Calendario','Gestion de Calendario'], 'actualizacion')
                        <button
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center nunito-regular"
                            @click="isEditModalOpen = true"><i class="fas fa-edit mr-2"></i>Editar</button>
                        <button
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center nunito-regular"
                            @click="isCancelModalOpen = true"><i class="fas fa-ban mr-2"></i>Cancelar</button>
                        @else
                        <button
                            class="bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded flex items-center nunito-regular cursor-not-allowed"
                            disabled title="Sin permiso para editar"><i class="fas fa-edit mr-2"></i>Editar</button>
                        <button
                            class="bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded flex items-center nunito-regular cursor-not-allowed"
                            disabled title="Sin permiso para actualizar"><i
                                class="fas fa-ban mr-2"></i>Cancelar</button>
                        @endperm
                    </div>
                </div>
            </x-admin.edit-modal>
            <x-admin.form-modal class="nunito-bold" modalName="isAddModalOpen" title="Agregar Evento"
                submitLabel="Guardar" :formId="'form-add-event'" maxWidth="max-w-lg sm:max-w-xl lg:max-w-2xl">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                        <select x-model.number="formEvento.id_agencias_fk" required
                            @change="formEvento._touched.agencia = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.agencia && !formEvento.id_agencias_fk ? 'border-red-500' : ''">
                            <option value="" disabled>Seleccione...</option>
                            <template x-for="a in catalogAgencias" :key="a.id_agencias_pk">
                                <option :value="a.id_agencias_pk" x-text="a.nombre_agencia"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.agencia && !formEvento.id_agencias_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                        <select x-model.number="formEvento.id_cliente_fk" required
                            @change="formEvento._touched.cliente = true" :disabled="!formEvento.id_agencias_fk"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.cliente && !formEvento.id_cliente_fk ? 'border-red-500' : ''">
                            <option value="" disabled x-show="!formEvento.id_agencias_fk">Seleccione una agencia primero
                            </option>
                            <option value="" disabled x-show="formEvento.id_agencias_fk">Seleccione...</option>
                            <template
                                x-for="c in (formEvento.id_agencias_fk ? (filteredClientes || []) : catalogClientes)"
                                :key="c.id">
                                <option :value="c.id" x-text="c.nombre"></option>
                            </template>
                        </select>
                        <template
                            x-if="formEvento.id_agencias_fk && !loadingFilteredClientes && (!filteredClientes || filteredClientes.length===0)">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i>
                                No hay clientes disponibles para esta agencia
                            </p>
                        </template>
                        <template x-if="formEvento.id_agencias_fk && loadingFilteredClientes">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando clientes...
                            </p>
                        </template>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.cliente && !formEvento.id_cliente_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de mantenimiento</label>
                        <select x-model.number="formEvento.id_tipo_mantenimiento_fk" required
                            @change="formEvento._touched.tipo = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.tipo && !formEvento.id_tipo_mantenimiento_fk ? 'border-red-500' : ''">
                            <option value="" disabled>Seleccione...</option>
                            <template x-for="t in catalogTiposMantenimiento" :key="t.id_tipo_mantenimiento_pk">
                                <option :value="t.id_tipo_mantenimiento_pk" x-text="t.tipo_mantenimiento"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.tipo && !formEvento.id_tipo_mantenimiento_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Fecha y hora</label>
                        <input type="datetime-local" x-model="formEvento.fecha" required
                            @input="formEvento._touched.fecha = true" @blur="formEvento._touched.fecha = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.fecha && !formEvento.fecha ? 'border-red-500' : ''" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                        <select x-model.number="formEvento.id_estado_calendario_fk" required
                            @change="formEvento._touched.estado = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.estado && !formEvento.id_estado_calendario_fk ? 'border-red-500' : ''">
                            <option value="" disabled>Seleccione...</option>
                            <template x-for="e in catalogEstados" :key="e.id_estado_calendario_pk">
                                <option :value="e.id_estado_calendario_pk" x-text="e.nombre"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.estado && !formEvento.id_estado_calendario_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Orden de Servicio</label>
                        <select x-model.number="formEvento.id_orden_servicio_fk" required
                            @change="formEvento._touched.orden = true" :disabled="!formEvento.id_cliente_fk"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.orden && !formEvento.id_orden_servicio_fk ? 'border-red-500' : ''">
                            <option value="" disabled x-show="!formEvento.id_cliente_fk">Seleccione un cliente primero
                            </option>
                            <option value="" disabled x-show="formEvento.id_cliente_fk">Seleccione una orden...</option>
                            <template
                                x-for="os in (formEvento.id_cliente_fk ? (filteredOrdenesServicio || []) : (catalogOrdenesServicio || []))"
                                :key="os.id || os.id_orden_servicio_pk">
                                <option :value="(os.id || os.id_orden_servicio_pk)"
                                    x-text="(os.label) || (os.numero_orden_servicio || os.codigo_orden || ('OS-' + (os.id_orden_servicio_pk || os.id)))">
                                </option>
                            </template>
                        </select>
                        <template
                            x-if="formEvento.id_cliente_fk && !loadingFilteredOrdenes && (!filteredOrdenesServicio || filteredOrdenesServicio.length===0)">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i>
                                No hay órdenes de servicio disponibles para este cliente
                            </p>
                        </template>
                        <template x-if="formEvento.id_cliente_fk && loadingFilteredOrdenes">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando órdenes de servicio...
                            </p>
                        </template>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.orden && !formEvento.id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                        <input type="text" x-model="formEvento.descripcion_calendario" maxlength="200" required
                            @input="formEvento._touched.descripcion = true"
                            @blur="formEvento._touched.descripcion = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.descripcion && (formEvento.descripcion_calendario === '' || formEvento.descripcion_calendario.length >= 200) ? 'border-red-500' : ''"
                            placeholder="Descripción del evento" />
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.descripcion && (formEvento.descripcion_calendario === '' || formEvento.descripcion_calendario.length >= 200) ? 'text-red-500' : ''">Requerido.
                            Máximo 200 caracteres.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium nunito-bold">Observaciones</label>
                        <textarea x-model="formEvento.observaciones_calendario" maxlength="500" required
                            @input="formEvento._touched.observaciones = true"
                            @blur="formEvento._touched.observaciones = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.observaciones && (formEvento.observaciones_calendario === '' || formEvento.observaciones_calendario.length >= 500) ? 'border-red-500' : ''"
                            placeholder="Notas u observaciones"></textarea>
                        <small class="block text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.observaciones && (formEvento.observaciones_calendario === '' || formEvento.observaciones_calendario.length >= 500) ? 'text-red-500' : ''">Requerido.
                            Máximo 500 caracteres.</small>
                    </div>
                </div>
                <div
                    @modal-submit.window="if($event.detail.formId==='form-add-event' && !submitting){ const f=document.getElementById('form-add-event'); if(f && !f.reportValidity()){ return; } submitting=true; window.calendarioApiHandlers.createEvent($data, normalizeForm(formEvento)).finally(()=>{ submitting=false; isAddModalOpen=false; resetForm(); }); }">
                </div>
                <div class="hidden"
                    x-init="resetForm = () => { const now=new Date(); const pad=n=>String(n).padStart(2,'0'); const fecha=`${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T08:00`; formEvento = { fecha, descripcion_calendario:'', observaciones_calendario:'', id_estado_calendario_fk:'', id_agencias_fk:'', id_orden_servicio_fk:'', id_tipo_mantenimiento_fk:'', id_cliente_fk:'' }; }; normalizeForm = (f) => ({ ...f, fecha: f.fecha?.includes('T') ? f.fecha.replace('T',' ') + ':00' : f.fecha });">
                </div>
            </x-admin.form-modal>
            <x-admin.edit-modal class="nunito-bold" modalName="isEditModalOpen" title="Editar Evento"
                itemToEdit="selectedEvent" :formId="'form-edit-event'">
                <div class="space-y-4"
                    x-init="$watch('selectedEvent', (ev) => { if(!ev) return; formEvento = { fecha: (ev.raw?.fecha||'').replace(' ','T').slice(0,16), descripcion_calendario: ev.raw?.descripcion_calendario||'', observaciones_calendario: ev.raw?.observaciones_calendario||'', id_estado_calendario_fk: ev.raw?.id_estado_calendario_fk||'', id_agencias_fk: ev.raw?.id_agencias_fk||'', id_orden_servicio_fk: ev.raw?.id_orden_servicio_fk||'', id_tipo_mantenimiento_fk: ev.raw?.id_tipo_mantenimiento_fk||'', id_cliente_fk: ev.raw?.id_cliente_fk||'' }; })">
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                        <select x-model.number="formEvento.id_agencias_fk" required
                            @change="formEvento._touched.agencia = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.agencia && !formEvento.id_agencias_fk ? 'border-red-500' : ''">
                            <option value="" disabled>Seleccione...</option>
                            <template x-for="a in catalogAgencias" :key="a.id_agencias_pk">
                                <option :value="a.id_agencias_pk" x-text="a.nombre_agencia"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.agencia && !formEvento.id_agencias_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                        <select x-model.number="formEvento.id_cliente_fk" required
                            @change="formEvento._touched.cliente = true" :disabled="!formEvento.id_agencias_fk"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.cliente && !formEvento.id_cliente_fk ? 'border-red-500' : ''">
                            <option value="" disabled x-show="!formEvento.id_agencias_fk">Seleccione una agencia primero
                            </option>
                            <option value="" disabled x-show="formEvento.id_agencias_fk">Seleccione...</option>
                            <template
                                x-for="c in (formEvento.id_agencias_fk ? (filteredClientes || []) : catalogClientes)"
                                :key="c.id">
                                <option :value="c.id" x-text="c.nombre"></option>
                            </template>
                        </select>
                        <template
                            x-if="formEvento.id_agencias_fk && !loadingFilteredClientes && (!filteredClientes || filteredClientes.length===0)">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i>
                                No hay clientes disponibles para esta agencia
                            </p>
                        </template>
                        <template x-if="formEvento.id_agencias_fk && loadingFilteredClientes">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando clientes...
                            </p>
                        </template>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.cliente && !formEvento.id_cliente_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de mantenimiento</label>
                        <select x-model.number="formEvento.id_tipo_mantenimiento_fk" required
                            @change="formEvento._touched.tipo = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.tipo && !formEvento.id_tipo_mantenimiento_fk ? 'border-red-500' : ''">
                            <option value="" disabled>Seleccione...</option>
                            <template x-for="t in catalogTiposMantenimiento" :key="t.id_tipo_mantenimiento_pk">
                                <option :value="t.id_tipo_mantenimiento_pk" x-text="t.tipo_mantenimiento"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.tipo && !formEvento.id_tipo_mantenimiento_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Fecha y hora</label>
                        <input type="datetime-local" x-model="formEvento.fecha" required
                            @input="formEvento._touched.fecha = true" @blur="formEvento._touched.fecha = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.fecha && !formEvento.fecha ? 'border-red-500' : ''" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                        <select x-model.number="formEvento.id_estado_calendario_fk" required
                            @change="formEvento._touched.estado = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.estado && !formEvento.id_estado_calendario_fk ? 'border-red-500' : ''">
                            <option value="" disabled>Seleccione...</option>
                            <template x-for="e in catalogEstados" :key="e.id_estado_calendario_pk">
                                <option :value="e.id_estado_calendario_pk" x-text="e.nombre"></option>
                            </template>
                        </select>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.estado && !formEvento.id_estado_calendario_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Orden de Servicio</label>
                        <select x-model.number="formEvento.id_orden_servicio_fk" required
                            @change="formEvento._touched.orden = true" :disabled="!formEvento.id_cliente_fk"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.orden && !formEvento.id_orden_servicio_fk ? 'border-red-500' : ''">
                            <option value="" disabled x-show="!formEvento.id_cliente_fk">Seleccione un cliente primero
                            </option>
                            <option value="" disabled x-show="formEvento.id_cliente_fk">Seleccione una orden...</option>
                            <template
                                x-for="os in (formEvento.id_cliente_fk ? (filteredOrdenesServicio || []) : (catalogOrdenesServicio || []))"
                                :key="os.id || os.id_orden_servicio_pk">
                                <option :value="(os.id || os.id_orden_servicio_pk)"
                                    x-text="(os.label) || (os.numero_orden_servicio || os.codigo_orden || ('OS-' + (os.id_orden_servicio_pk || os.id)))">
                                </option>
                            </template>
                        </select>
                        <template
                            x-if="formEvento.id_cliente_fk && !loadingFilteredOrdenes && (!filteredOrdenesServicio || filteredOrdenesServicio.length===0)">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i>
                                No hay órdenes de servicio disponibles para este cliente
                            </p>
                        </template>
                        <template x-if="formEvento.id_cliente_fk && loadingFilteredOrdenes">
                            <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando órdenes de servicio...
                            </p>
                        </template>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.orden && !formEvento.id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                        <input type="text" x-model="formEvento.descripcion_calendario" maxlength="200" required
                            @input="formEvento._touched.descripcion = true"
                            @blur="formEvento._touched.descripcion = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.descripcion && (formEvento.descripcion_calendario === '' || formEvento.descripcion_calendario.length >= 200) ? 'border-red-500' : ''" />
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.descripcion && (formEvento.descripcion_calendario === '' || formEvento.descripcion_calendario.length >= 200) ? 'text-red-500' : ''">Requerido.
                            Máximo 200 caracteres.</small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 nunito-bold">Observaciones</label>
                        <textarea x-model="formEvento.observaciones_calendario" maxlength="500" required
                            @input="formEvento._touched.observaciones = true"
                            @blur="formEvento._touched.observaciones = true"
                            class="border rounded px-3 py-2 w-full nunito-regular"
                            :class="formEvento._touched && formEvento._touched.observaciones && (formEvento.observaciones_calendario === '' || formEvento.observaciones_calendario.length >= 500) ? 'border-red-500' : ''"></textarea>
                        <small class="block mt-1 text-sm text-gray-500"
                            :class="formEvento._touched && formEvento._touched.observaciones && (formEvento.observaciones_calendario === '' || formEvento.observaciones_calendario.length >= 500) ? 'text-red-500' : ''">Requerido.
                            Máximo 500 caracteres.</small>
                    </div>
                </div>
                <div
                    @modal-submit.window="if($event.detail.formId==='form-edit-event' && selectedEvent && !submitting){ const f=document.getElementById('form-edit-event'); if(f && !f.reportValidity()){ return; } submitting=true; window.calendarioApiHandlers.updateEvent($data, selectedEvent.id, normalizeForm(formEvento)).finally(()=>{ submitting=false; isEditModalOpen=false; }); }">
                </div>
            </x-admin.edit-modal>
            <x-admin.confirmation-modal class="nunito-bold" modalName="isCancelModalOpen" itemToDelete="selectedEvent"
                message="¿Está seguro que desea cancelar este evento? El estado cambiará a 'Cancelado'." />
            <div
                @confirm-delete.window="if(selectedEvent){ const cancelId = (catalogEstados.find(e=> (e.codigo||'').toLowerCase()==='cancelado')?.id_estado_calendario_pk) || (catalogEstados.find(e=> (e.nombre||'').toLowerCase()==='cancelado')?.id_estado_calendario_pk); if(cancelId) window.calendarioApiHandlers.cancelEvent($data, selectedEvent.id, cancelId).then(()=>{ isCancelModalOpen=false; }); else { window.showToast && window.showToast('No hay estado CANCELADO en el catálogo', 'error'); } }">
            </div>
        </div>

        <div x-show="tab==='eventosLista'" class="py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <h2 class="text-2xl font-semibold leading-tight nunito-bold mb-3 text-gray-800 dark:text-white">Lista de
                    Eventos</h2>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    @perm(['Gestión de Calendario','Gestion de Calendario'], 'insercion')
                    <button
                        class="transition duration-100 ease-in-out w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 flex items-center justify-center rounded-lg nunito-regular text-sm"
                        @click="openAddList()">
                        <i class="fas fa-plus mr-2"></i> Agregar Evento
                    </button>
                    @else
                    <button disabled title="Sin permiso para crear"
                        class="transition duration-100 ease-in-out w-full sm:w-auto bg-gray-300 text-gray-600 font-bold py-2 px-4 flex items-center justify-center rounded-lg nunito-regular text-sm cursor-not-allowed">
                        <i class="fas fa-plus mr-2"></i> Agregar Evento
                    </button>
                    @endperm
                    <a href="/admin/reportes-header?modulo=Calendario&fecha={{ now()->format('d-M-Y') }}"
                        target="_blank"
                        class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg nunito-regular transition duration-100 ease-in-out whitespace-nowrap flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-file-alt"></i> Generar Reporte
                    </a>
                </div>
            </div>

            <div class="flex flex-col gap-2 mb-6">
                <input type="text" x-model="searchEventos" placeholder="Buscar..."
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200" />
                <select x-model="estadoEventoFiltro"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="">Todos los estados</option>
                    <template x-for="e in catalogEstados" :key="e.id_estado_calendario_pk">
                        <option :value="e.nombre || e.codigo" x-text="e.nombre || e.codigo"></option>
                    </template>
                </select>
                <select x-model="agenciaEventoFiltro"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="">Todas las agencias</option>
                    <template x-for="a in catalogAgencias" :key="a.id_agencias_pk">
                        <option :value="a.nombre_agencia" x-text="a.nombre_agencia"></option>
                    </template>
                </select>
                <select x-model="ordenarPor"
                    class="border border-gray-500 rounded px-3 py-2 text-sm font-semibold nunito-bold w-full dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
                    <option value="fecha">Ordenar por Fecha</option>
                    <option value="estado">Ordenar por Estado</option>
                    <option value="cliente">Ordenar por Cliente</option>
                </select>
            </div>

            <div class="grid gap-3 sm:gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <template x-for="ev in filteredEvents()" :key="ev.id">
                    <div class="bg-white dark:bg-gray-800 rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6 border-l-4" :class="{
                            'border-blue-500': ev.estado==='Programado',
                            'border-green-500': ev.estado==='Realizado',
                            'border-red-500': ev.estado==='Cancelado'
                         }">
                        <div class="flex justify-between items-start mb-2 sm:mb-3">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 dark:text-white nunito-bold" x-text="ev.titulo">
                            </h3>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold ml-2 flex-shrink-0" :class="{
                                     'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300': ev.estado==='Programado',
                                     'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300': ev.estado==='Realizado',
                                     'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300': ev.estado==='Cancelado'
                                  }" x-text="ev.estado"></span>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                <i class="fas fa-calendar-alt mr-2 w-4 text-center"></i>
                                <span x-text="formatDateEs(ev.raw?.fecha)"></span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                <i class="fas fa-clock mr-2 w-4 text-center"></i>
                                <span x-text="ev.hora"></span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300" x-show="ev.cliente">
                                <i class="fas fa-user mr-2 w-4 text-center"></i>
                                <span x-text="ev.cliente"></span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300" x-show="ev.agencia">
                                <i class="fas fa-building mr-2 w-4 text-center"></i>
                                <span x-text="ev.agencia"></span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300" x-show="ev.tipo">
                                <i class="fas fa-wrench mr-2 w-4 text-center"></i>
                                <span x-text="ev.tipo"></span>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 pt-3 border-t dark:border-gray-600 flex flex-col sm:flex-row gap-2">
                            <button
                                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm flex items-center justify-center gap-2 transition-colors"
                                @click="selectedEventLista = ev; isDetailListModalOpen = true">
                                <i class="fas fa-eye"></i>
                                <span>Ver</span>
                            </button>
                            @perm(['Gestión de Calendario','Gestion de Calendario'], 'actualizacion')
                            <button
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm flex items-center justify-center gap-2 transition-colors"
                                @click="openEditList(ev)">
                                <i class="fas fa-edit"></i>
                                <span>Editar</span>
                            </button>
                            @else
                            <button
                                class="flex-1 bg-gray-300 text-gray-600 px-3 py-2 rounded text-sm cursor-not-allowed flex items-center justify-center gap-2"
                                disabled title="Sin permiso para editar">
                                <i class="fas fa-edit"></i>
                                <span>Editar</span>
                            </button>
                            @endperm
                            @perm(['Gestión de Calendario','Gestion de Calendario'], 'eliminacion')
                            <button
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm flex items-center justify-center gap-2 transition-colors"
                                @click="openDeleteList(ev)">
                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                            @else
                            <button
                                class="flex-1 bg-red-600/50 text-white px-3 py-2 rounded text-sm cursor-not-allowed flex items-center justify-center gap-2"
                                disabled title="Sin permiso para eliminar">
                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                            @endperm
                        </div>
                    </div>
                </template>
                <template x-if="filteredEvents().length === 0">
                    <div class="col-span-full">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center text-gray-600 dark:text-gray-300">
                            No hay eventos para mostrar.
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <x-admin.form-modal modalName="isDetailModalOpen" title="Detalles del Evento" submitLabel="" hideActions="true"
            maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Información General
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Título
                                del Evento</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.titulo : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Hora</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.hora : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Estado</label>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold nunito-bold" :class="selectedEvent ? {
                                      'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedEvent.estado === 'Programado',
                                      'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300': selectedEvent.estado === 'Realizado',
                                      'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300': selectedEvent.estado === 'Cancelado'
                                  } : ''" x-text="selectedEvent ? selectedEvent.estado : ''">
                            </span>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Agencia</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.agencia : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Dirección</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.direccion : ''"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Detalles del Servicio
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Cliente</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.cliente : ''"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Tipo
                                de Mantenimiento</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.tipo : ''"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Orden
                                de Servicio</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.orden : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Observaciones</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.observaciones : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Diagnóstico</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEvent ? selectedEvent.diagnostico : ''"></p>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.form-modal modalName="isDetailListModalOpen" title="Detalles del Evento" submitLabel=""
            hideActions="true" maxWidth="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Información General
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Título
                                del Evento</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.titulo : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Hora</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.hora : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Estado</label>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold nunito-bold" :class="selectedEventLista ? {
                                    'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300': selectedEventLista.estado === 'Programado',
                                    'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300': selectedEventLista.estado === 'Realizado',
                                    'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300': selectedEventLista.estado === 'Cancelado'
                                } : ''" x-text="selectedEventLista ? selectedEventLista.estado : ''">
                            </span>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Agencia</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.agencia : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Dirección</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.direccion : ''"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Detalles del Servicio
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Cliente</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.cliente : ''"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Tipo
                                de Mantenimiento</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.tipo : ''"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Orden
                                de Servicio</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.orden : ''"></p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 nunito-bold">Observaciones</label>
                            <p class="text-gray-800 dark:text-white nunito-regular"
                                x-text="selectedEventLista ? selectedEventLista.observaciones : ''"></p>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.form-modal>

        <x-admin.form-modal class="nunito-bold" modalName="isAddListModalOpen" title="Agregar Evento"
            submitLabel="Guardar" :formId="'form-add-event-list'">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                    <select x-model.number="formEventoLista.id_agencias_fk" required
                        @change="formEventoLista._touched.agencia = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.agencia && !formEventoLista.id_agencias_fk ? 'border-red-500' : ''">
                        <option value="" disabled>Seleccione...</option>
                        <template x-for="a in catalogAgencias" :key="a.id_agencias_pk">
                            <option :value="a.id_agencias_pk" x-text="a.nombre_agencia"></option>
                        </template>
                    </select>
                    <template
                        x-if="formEventoLista.id_agencias_fk && !loadingFilteredClientes && (!filteredClientes || filteredClientes.length===0)">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            No hay clientes disponibles para esta agencia
                        </p>
                    </template>
                    <template x-if="formEventoLista.id_agencias_fk && loadingFilteredClientes">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-spinner fa-spin"></i>
                            Cargando clientes...
                        </p>
                    </template>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                    <select x-model.number="formEventoLista.id_cliente_fk" required
                        @change="formEventoLista._touched.cliente = true" :disabled="!formEventoLista.id_agencias_fk"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.cliente && !formEventoLista.id_cliente_fk ? 'border-red-500' : ''">
                        <option value="" disabled x-show="!formEventoLista.id_agencias_fk">Seleccione una agencia
                            primero</option>
                        <option value="" disabled x-show="formEventoLista.id_agencias_fk">Seleccione...</option>
                        <template
                            x-for="c in (formEventoLista.id_agencias_fk ? (filteredClientes || []) : catalogClientes)"
                            :key="c.id">
                            <option :value="c.id" x-text="c.nombre"></option>
                        </template>
                    </select>
                    <template
                        x-if="formEventoLista.id_agencias_fk && !loadingFilteredClientes && (!filteredClientes || filteredClientes.length===0)">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            No hay clientes disponibles para esta agencia
                        </p>
                    </template>
                    <template x-if="formEventoLista.id_agencias_fk && loadingFilteredClientes">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-spinner fa-spin"></i>
                            Cargando clientes...
                        </p>
                    </template>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.cliente && !formEventoLista.id_cliente_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de mantenimiento</label>
                    <select x-model.number="formEventoLista.id_tipo_mantenimiento_fk" required
                        @change="formEventoLista._touched.tipo = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.tipo && !formEventoLista.id_tipo_mantenimiento_fk ? 'border-red-500' : ''">
                        <option value="" disabled>Seleccione...</option>
                        <template x-for="t in catalogTiposMantenimiento" :key="t.id_tipo_mantenimiento_pk">
                            <option :value="t.id_tipo_mantenimiento_pk" x-text="t.tipo_mantenimiento"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.tipo && !formEventoLista.id_tipo_mantenimiento_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Fecha y hora</label>
                    <input type="datetime-local" x-model="formEventoLista.fecha" required
                        @input="formEventoLista._touched.fecha = true" @blur="formEventoLista._touched.fecha = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.fecha && !formEventoLista.fecha ? 'border-red-500' : ''" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                    <select x-model.number="formEventoLista.id_estado_calendario_fk" required
                        @change="formEventoLista._touched.estado = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.estado && !formEventoLista.id_estado_calendario_fk ? 'border-red-500' : ''">
                        <option value="" disabled>Seleccione...</option>
                        <template x-for="e in catalogEstados" :key="e.id_estado_calendario_pk">
                            <option :value="e.id_estado_calendario_pk" x-text="e.nombre"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.estado && !formEventoLista.id_estado_calendario_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Orden de Servicio</label>
                    <select x-model.number="formEventoLista.id_orden_servicio_fk" required
                        @change="formEventoLista._touched.orden = true" :disabled="!formEventoLista.id_cliente_fk"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.orden && !formEventoLista.id_orden_servicio_fk ? 'border-red-500' : ''">
                        <option value="" disabled x-show="!formEventoLista.id_cliente_fk">Seleccione un cliente primero
                        </option>
                        <option value="" disabled x-show="formEventoLista.id_cliente_fk">Seleccione una orden...
                        </option>
                        <template
                            x-for="os in (formEventoLista.id_cliente_fk ? (filteredOrdenesServicio || []) : (catalogOrdenesServicio || []))"
                            :key="os.id || os.id_orden_servicio_pk">
                            <option :value="(os.id || os.id_orden_servicio_pk)"
                                x-text="(os.label) || (os.numero_orden_servicio || os.codigo_orden || ('OS-' + (os.id_orden_servicio_pk || os.id)))">
                            </option>
                        </template>
                    </select>
                    <template
                        x-if="formEventoLista.id_cliente_fk && !loadingFilteredOrdenes && (!filteredOrdenesServicio || filteredOrdenesServicio.length===0)">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            No hay órdenes de servicio disponibles para este cliente
                        </p>
                    </template>
                    <template x-if="formEventoLista.id_cliente_fk && loadingFilteredOrdenes">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-spinner fa-spin"></i>
                            Cargando órdenes de servicio...
                        </p>
                    </template>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.orden && !formEventoLista.id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                    <input type="text" x-model="formEventoLista.descripcion_calendario" maxlength="200" required
                        @input="formEventoLista._touched.descripcion = true"
                        @blur="formEventoLista._touched.descripcion = true"
                        class="border rounded px-3 py-2 w-full nunito-regular" placeholder="Descripción del evento"
                        :class="formEventoLista._touched && formEventoLista._touched.descripcion && (formEventoLista.descripcion_calendario === '' || formEventoLista.descripcion_calendario.length >= 200) ? 'border-red-500' : ''" />
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.descripcion && (formEventoLista.descripcion_calendario === '' || formEventoLista.descripcion_calendario.length >= 200) ? 'text-red-500' : ''">Requerido.
                        Máximo 200 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Observaciones</label>
                    <textarea x-model="formEventoLista.observaciones_calendario" maxlength="500" required
                        @input="formEventoLista._touched.observaciones = true"
                        @blur="formEventoLista._touched.observaciones = true"
                        class="border rounded px-3 py-2 w-full nunito-regular" placeholder="Notas u observaciones"
                        :class="formEventoLista._touched && formEventoLista._touched.observaciones && (formEventoLista.observaciones_calendario === '' || formEventoLista.observaciones_calendario.length >= 500) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.observaciones && (formEventoLista.observaciones_calendario === '' || formEventoLista.observaciones_calendario.length >= 500) ? 'text-red-500' : ''">Requerido.
                        Máximo 500 caracteres.</small>
                </div>
            </div>
            <div
                @modal-submit.window="if($event.detail.formId==='form-add-event-list' && !submitting){ const f=document.getElementById('form-add-event-list'); if(f && !f.reportValidity()){ return; } submitting=true; window.calendarioApiHandlers.createEvent($data, normalizeForm(formEventoLista)).finally(()=>{ submitting=false; isAddListModalOpen=false; resetFormLista(); }); }">
            </div>
            <div class="hidden"
                x-init="resetFormLista = () => { const now=new Date(); const pad=n=>String(n).padStart(2,'0'); const fecha=`${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T08:00`; formEventoLista = { fecha, descripcion_calendario:'', observaciones_calendario:'', id_estado_calendario_fk:'', id_agencias_fk:'', id_orden_servicio_fk:'', id_tipo_mantenimiento_fk:'', id_cliente_fk:'', _touched: {} }; }; normalizeForm = (f) => ({ ...f, fecha: f.fecha?.includes('T') ? f.fecha.replace('T',' ') + ':00' : f.fecha });">
            </div>
        </x-admin.form-modal>

        <x-admin.edit-modal class="nunito-bold" modalName="isEditListModalOpen" title="Editar Evento"
            itemToEdit="selectedEventLista" :formId="'form-edit-event-list'">
            <div class="space-y-4"
                x-init="$watch('selectedEventLista', (ev) => { if(!ev) return; formEventoLista = { fecha: (ev.raw?.fecha||'').replace(' ','T').slice(0,16), descripcion_calendario: ev.raw?.descripcion_calendario||'', observaciones_calendario: ev.raw?.observaciones_calendario||'', id_estado_calendario_fk: ev.raw?.id_estado_calendario_fk||'', id_agencias_fk: ev.raw?.id_agencias_fk||'', id_orden_servicio_fk: ev.raw?.id_orden_servicio_fk||'', id_tipo_mantenimiento_fk: ev.raw?.id_tipo_mantenimiento_fk||'', id_cliente_fk: ev.raw?.id_cliente_fk||'', _touched: {} }; })">
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Agencia</label>
                    <select x-model.number="formEventoLista.id_agencias_fk" required
                        @change="formEventoLista._touched.agencia = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.agencia && !formEventoLista.id_agencias_fk ? 'border-red-500' : ''">
                        <option value="" disabled>Seleccione...</option>
                        <template x-for="a in catalogAgencias" :key="a.id_agencias_pk">
                            <option :value="a.id_agencias_pk" x-text="a.nombre_agencia"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.agencia && !formEventoLista.id_agencias_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Cliente</label>
                    <select x-model.number="formEventoLista.id_cliente_fk" required
                        @change="formEventoLista._touched.cliente = true" :disabled="!formEventoLista.id_agencias_fk"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.cliente && !formEventoLista.id_cliente_fk ? 'border-red-500' : ''">
                        <option value="" disabled x-show="!formEventoLista.id_agencias_fk">Seleccione una agencia
                            primero</option>
                        <option value="" disabled x-show="formEventoLista.id_agencias_fk">Seleccione...</option>
                        <template
                            x-for="c in (formEventoLista.id_agencias_fk ? (filteredClientes || []) : catalogClientes)"
                            :key="c.id">
                            <option :value="c.id" x-text="c.nombre"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.cliente && !formEventoLista.id_cliente_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Tipo de mantenimiento</label>
                    <select x-model.number="formEventoLista.id_tipo_mantenimiento_fk" required
                        @change="formEventoLista._touched.tipo = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.tipo && !formEventoLista.id_tipo_mantenimiento_fk ? 'border-red-500' : ''">
                        <option value="" disabled>Seleccione...</option>
                        <template x-for="t in catalogTiposMantenimiento" :key="t.id_tipo_mantenimiento_pk">
                            <option :value="t.id_tipo_mantenimiento_pk" x-text="t.tipo_mantenimiento"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.tipo && !formEventoLista.id_tipo_mantenimiento_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Fecha y hora</label>
                    <input type="datetime-local" x-model="formEventoLista.fecha" required
                        @input="formEventoLista._touched.fecha = true" @blur="formEventoLista._touched.fecha = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.fecha && !formEventoLista.fecha ? 'border-red-500' : ''" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Estado</label>
                    <select x-model.number="formEventoLista.id_estado_calendario_fk" required
                        @change="formEventoLista._touched.estado = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.estado && !formEventoLista.id_estado_calendario_fk ? 'border-red-500' : ''">
                        <option value="" disabled>Seleccione...</option>
                        <template x-for="e in catalogEstados" :key="e.id_estado_calendario_pk">
                            <option :value="e.id_estado_calendario_pk" x-text="e.nombre"></option>
                        </template>
                    </select>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.estado && !formEventoLista.id_estado_calendario_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Orden de Servicio</label>
                    <select x-model.number="formEventoLista.id_orden_servicio_fk" required
                        :disabled="!formEventoLista.id_cliente_fk"
                        class="border rounded px-3 py-2 w-full nunito-regular">
                        <option value="" disabled x-show="!formEventoLista.id_cliente_fk">Seleccione un cliente primero
                        </option>
                        <option value="" disabled x-show="formEventoLista.id_cliente_fk">Seleccione una orden...
                        </option>
                        <template
                            x-for="os in (formEventoLista.id_cliente_fk ? (filteredOrdenesServicio || []) : (catalogOrdenesServicio || []))"
                            :key="os.id || os.id_orden_servicio_pk">
                            <option :value="(os.id || os.id_orden_servicio_pk)"
                                x-text="(os.label) || (os.numero_orden_servicio || os.codigo_orden || ('OS-' + (os.id_orden_servicio_pk || os.id)))">
                            </option>
                        </template>
                    </select>
                    <template
                        x-if="formEventoLista.id_cliente_fk && !loadingFilteredOrdenes && (!filteredOrdenesServicio || filteredOrdenesServicio.length===0)">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            No hay órdenes de servicio disponibles para este cliente
                        </p>
                    </template>
                    <template x-if="formEventoLista.id_cliente_fk && loadingFilteredOrdenes">
                        <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-spinner fa-spin"></i>
                            Cargando órdenes de servicio...
                        </p>
                    </template>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.orden && !formEventoLista.id_orden_servicio_fk ? 'text-red-500' : ''">Requerido.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Descripción</label>
                    <input type="text" x-model="formEventoLista.descripcion_calendario" maxlength="200" required
                        @input="formEventoLista._touched.descripcion = true"
                        @blur="formEventoLista._touched.descripcion = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.descripcion && (formEventoLista.descripcion_calendario === '' || formEventoLista.descripcion_calendario.length >= 200) ? 'border-red-500' : ''" />
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.descripcion && (formEventoLista.descripcion_calendario === '' || formEventoLista.descripcion_calendario.length >= 200) ? 'text-red-500' : ''">Requerido.
                        Máximo 200 caracteres.</small>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 nunito-bold">Observaciones</label>
                    <textarea x-model="formEventoLista.observaciones_calendario" maxlength="500" required
                        @input="formEventoLista._touched.observaciones = true"
                        @blur="formEventoLista._touched.observaciones = true"
                        class="border rounded px-3 py-2 w-full nunito-regular"
                        :class="formEventoLista._touched && formEventoLista._touched.observaciones && (formEventoLista.observaciones_calendario === '' || formEventoLista.observaciones_calendario.length >= 500) ? 'border-red-500' : ''"></textarea>
                    <small class="block mt-1 text-sm text-gray-500"
                        :class="formEventoLista._touched && formEventoLista._touched.observaciones && (formEventoLista.observaciones_calendario === '' || formEventoLista.observaciones_calendario.length >= 500) ? 'text-red-500' : ''">Requerido.
                        Máximo 500 caracteres.</small>
                </div>
            </div>
            <div
                @modal-submit.window="if($event.detail.formId==='form-edit-event-list' && selectedEventLista && !submitting){ const f=document.getElementById('form-edit-event-list'); if(f && !f.reportValidity()){ return; } submitting=true; window.calendarioApiHandlers.updateEvent($data, selectedEventLista.id, normalizeForm(formEventoLista)).finally(()=>{ submitting=false; isEditListModalOpen=false; }); }">
            </div>
        </x-admin.edit-modal>

        <div x-show="isDeleteListModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" style="display:none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-900 w-full max-w-md rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
                <div class="px-5 pt-5 pb-4">
                    <h3
                        class="text-lg font-bold mb-2 text-gray-800 dark:text-white nunito-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i> Eliminar Evento
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 nunito-regular">
                        ¿Estás seguro de que deseas eliminar el evento <span class="font-semibold"
                            x-text="eventToDeleteLista?.titulo"></span>? Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="px-5 pb-5 flex justify-end gap-2">
                    <button @click="cancelDeleteList" type="button"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm nunito-regular hover:bg-gray-300 dark:hover:bg-gray-600">Cancelar</button>
                    <button @click="confirmDeleteList" type="button"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm nunito-bold flex items-center gap-2">
                        <i class="fas fa-trash"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="isMonthModalOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-md">
        <div x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-xs mx-auto">
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white nunito-bold">Seleccionar mes y año</h3>
            <div class="mb-4">
                <select x-model="selectedMonth"
                    class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700">
                    <template x-for="(name, idx) in monthNames" :key="idx">
                        <option :value="idx" x-text="name"></option>
                    </template>
                </select>
            </div>
            <div class="mb-4">
                <input type="number" x-model="selectedYear" min="1900" max="2100"
                    class="w-full border rounded px-3 py-2 nunito-regular bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700" />
            </div>
            <div class="flex justify-end gap-2">
                <button @click="isMonthModalOpen = false"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg nunito-regular hover:bg-gray-300 dark:hover:bg-gray-600">Cancelar</button>
                <button
                    @click="currentMonth = selectedMonth; currentYear = selectedYear; isMonthModalOpen = false; $nextTick(() => window.calendarioApiHandlers.fetchMonth($data));"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg nunito-regular hover:bg-blue-700">Seleccionar</button>
            </div>
        </div>
    </div>

    <div x-show="isDeleteModalOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" style="display:none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-900 w-full max-w-md rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
            <div class="px-5 pt-5 pb-4">
                <h3 class="text-lg font-bold mb-2 text-gray-800 dark:text-white nunito-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-500"></i> Eliminar Evento
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 nunito-regular">
                    ¿Estás seguro de que deseas eliminar el evento <span class="font-semibold"
                        x-text="eventToDelete?.titulo"></span>? Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="px-5 pb-5 flex justify-end gap-2">
                <button @click="cancelDelete" type="button"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm nunito-regular hover:bg-gray-300 dark:hover:bg-gray-600">Cancelar</button>
                <button @click="confirmDelete" type="button"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm nunito-bold flex items-center gap-2">
                    <i class="fas fa-trash"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>