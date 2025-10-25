<div class="container mx-auto space-y-6" x-data="{
    proyectos: [],
    currentProyectoIndex: 0,
    loading: false,
    // Movimientos del proyecto actual
    ingresosProyecto: [],
    gastosProyecto: [],
    loadingMovimientos: false,
    lastLoadedProjectId: null,
    // Modal de lista de proyectos
    showProjectListModal: false,
    searchQuery: '',
    filterEstado: 'todos', // todos, activos, completados, deficit
    filterBalance: 'todos', // todos, positivo, negativo, cero
    sortBy: 'nombre', // nombre, fecha, balance, ingresos, gastos

    async init() {
        await this.fetchProyectos();
        // si hay proyectos, cargar movimientos del primero
        if (this.proyectos.length > 0) {
            await this.loadMovimientosForCurrent();
        }
    },

    async fetchProyectos() {
        this.loading = true;
        try {
            const response = await fetch('/api/proyectos', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw data;
            this.proyectos = Array.isArray(data?.data) ? data.data : [];
            // Si no hay proyectos, mostrar mensaje
            if (this.proyectos.length === 0) {
                this.currentProyectoIndex = -1;
            }
        } catch (error) {
            console.error('Error fetching proyectos:', error);
            window.showToast && window.showToast('Error al cargar proyectos', 'error');
        } finally {
            this.loading = false;
        }
    },

    get currentProyecto() {
        return this.proyectos[this.currentProyectoIndex] || null;
    },

    async previousProyecto() {
        if (this.proyectos.length === 0) return;
        // reset provisional lists to avoid showing wrong data while loading
        this.ingresosProyecto = [];
        this.gastosProyecto = [];
        this.currentProyectoIndex = this.currentProyectoIndex > 0
            ? this.currentProyectoIndex - 1
            : this.proyectos.length - 1;
        await this.loadMovimientosForCurrent();
    },

    async nextProyecto() {
        if (this.proyectos.length === 0) return;
        this.ingresosProyecto = [];
        this.gastosProyecto = [];
        this.currentProyectoIndex = this.currentProyectoIndex < this.proyectos.length - 1
            ? this.currentProyectoIndex + 1
            : 0;
        await this.loadMovimientosForCurrent();
    },

    async loadMovimientosForCurrent() {
        const proyecto = this.currentProyecto;
        if (!proyecto || !proyecto.id_proyecto_pk) {
            this.ingresosProyecto = [];
            this.gastosProyecto = [];
            this.lastLoadedProjectId = null;
            return;
        }

        const proyectoId = String(proyecto.id_proyecto_pk);
        // Si ya cargamos movimientos de este proyecto y no cambiaron, no recargar
        if (this.lastLoadedProjectId && this.lastLoadedProjectId === proyectoId) return;

        this.loadingMovimientos = true;
        // limpiar antes de cargar
        this.ingresosProyecto = [];
        this.gastosProyecto = [];
        try {
            // fetch ingresos filtrados por proyecto (servidor idealmente filtrará)
            const respI = await fetch(`/api/ingresos?proyecto=${proyectoId}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            const dataI = await respI.json().catch(() => ({}));
            let rawIngresos = (respI.ok && Array.isArray(dataI?.data)) ? dataI.data : [];
            // fallback client-side filter
            const filteredIngresos = rawIngresos.filter(i => {
                // support multiple possible field names for proyecto id
                const candidates = [
                    i.id_proyecto_fk,
                    i.id_proyecto_pk,
                    i.id_proyecto,
                    i.proyecto && i.proyecto.id_proyecto_pk,
                    i.proyecto && i.proyecto.id_proyecto_fk,
                    i.proyecto && i.proyecto.id_proyecto,
                    i.proyecto && i.proyecto.id,
                    i.proyecto && i.proyecto.id_proyecto
                ];
                return candidates.some(c => c !== undefined && c !== null && String(c) === proyectoId);
            });
            console.debug('vista-proyectos: rawIngresos', rawIngresos.length, 'filteredIngresos', filteredIngresos.length, 'proyectoId', proyectoId);
            this.ingresosProyecto = filteredIngresos;

            // fetch gastos filtrados por proyecto
            const respG = await fetch(`/api/gastos?proyecto=${proyectoId}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            const dataG = await respG.json().catch(() => ({}));
            let rawGastos = (respG.ok && Array.isArray(dataG?.data)) ? dataG.data : [];
            const filteredGastos = rawGastos.filter(g => {
                const candidates = [
                    g.id_proyecto_fk,
                    g.id_proyecto_pk,
                    g.id_proyecto,
                    g.proyecto && g.proyecto.id_proyecto_pk,
                    g.proyecto && g.proyecto.id_proyecto_fk,
                    g.proyecto && g.proyecto.id_proyecto,
                    g.proyecto && g.proyecto.id,
                    g.proyecto && g.proyecto.id_proyecto
                ];
                return candidates.some(c => c !== undefined && c !== null && String(c) === proyectoId);
            });
            console.debug('vista-proyectos: rawGastos', rawGastos.length, 'filteredGastos', filteredGastos.length, 'proyectoId', proyectoId);
            this.gastosProyecto = filteredGastos;

            // compute simple totals so the stat cards reflect loaded movimientos
            try {
                const ingresosTotal = this.ingresosProyecto.reduce((s, it) => {
                    const monto = parseFloat(it.monto_ingreso ?? it.monto ?? 0) || 0;
                    return s + monto;
                }, 0);
                const gastosTotal = this.gastosProyecto.reduce((s, it) => {
                    const monto = parseFloat(it.monto ?? it.monto_ingreso ?? 0) || 0;
                    return s + monto;
                }, 0);
                // write totals back to the proyecto object for binding (non-persistent)
                proyecto.total_ingresos = ingresosTotal;
                proyecto.total_gastos = gastosTotal;
            } catch (errTotals) {
                // swallow - not critical
                console.warn('Error calculando totales:', errTotals);
            }

            this.lastLoadedProjectId = proyectoId;
        } catch (e) {
            console.error('Error cargando movimientos del proyecto:', e);
            window.showToast && window.showToast('Error al cargar movimientos', 'error');
            this.ingresosProyecto = [];
            this.gastosProyecto = [];
            this.lastLoadedProjectId = null;
        } finally {
            this.loadingMovimientos = false;
        }
    },

    formatCurrency(amount) {
        return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(amount || 0);
    },

    formatDate(date) {
        if (!date) return 'N/A';
        try {
            const d = new Date(date);
            if (isNaN(d.getTime())) return date;
            return d.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (e) {
            return date;
        }
    },

    openProjectListModal() {
        this.showProjectListModal = true;
    },

    closeProjectListModal() {
        this.showProjectListModal = false;
    },

    async selectProyecto(index) {
        if (index >= 0 && index < this.proyectos.length) {
            this.ingresosProyecto = [];
            this.gastosProyecto = [];
            this.currentProyectoIndex = index;
            this.closeProjectListModal();
            await this.loadMovimientosForCurrent();
        }
    },

    combinedMovimientos() {
        // Merge ingresos and gastos, normalize a date field and type marker
        const items = [];
        try {
            this.ingresosProyecto.forEach(i => {
                const fecha = i.fecha_ingreso || i.created_at || i.fecha || i.createdAt || null;
                items.push(Object.assign({}, i, { __tipo: 'ingreso', __fecha: fecha }));
            });
            this.gastosProyecto.forEach(g => {
                const fecha = g.fecha || g.created_at || g.fecha_gasto || g.createdAt || null;
                items.push(Object.assign({}, g, { __tipo: 'gasto', __fecha: fecha }));
            });
            // sort desc by date (most recent first). Invalid dates go to the end.
            items.sort((a, b) => {
                const da = a.__fecha ? new Date(a.__fecha) : null;
                const db = b.__fecha ? new Date(b.__fecha) : null;
                if (da === null && db === null) return 0;
                if (da === null) return 1;
                if (db === null) return -1;
                return db - da;
            });
        } catch (e) {
            console.error('Error combinando movimientos:', e);
        }
        return items;
    },

    filteredProyectos() {
        let filtered = [...this.proyectos];

        // Aplicar búsqueda
        if (this.searchQuery.trim()) {
            const query = this.searchQuery.toLowerCase().trim();
            filtered = filtered.filter(p => {
                const nombre = (p.nombre_proyecto || '').toLowerCase();
                const desc = (p.descripcion_proyecto || p.descripcion || '').toLowerCase();
                return nombre.includes(query) || desc.includes(query);
            });
        }

        // Aplicar filtro de estado
        if (this.filterEstado !== 'todos') {
            filtered = filtered.filter(p => {
                const balance = (p.total_ingresos || 0) - (p.total_gastos || 0);
                const tieneMovimientos = (p.total_ingresos > 0 || p.total_gastos > 0);
                
                if (this.filterEstado === 'activos') {
                    return balance > 0 || !tieneMovimientos;
                } else if (this.filterEstado === 'completados') {
                    return balance === 0 && tieneMovimientos;
                } else if (this.filterEstado === 'deficit') {
                    return balance < 0;
                }
                return true;
            });
        }

        // Aplicar filtro de balance
        if (this.filterBalance !== 'todos') {
            filtered = filtered.filter(p => {
                const balance = (p.total_ingresos || 0) - (p.total_gastos || 0);
                
                if (this.filterBalance === 'positivo') {
                    return balance > 0;
                } else if (this.filterBalance === 'negativo') {
                    return balance < 0;
                } else if (this.filterBalance === 'cero') {
                    return balance === 0;
                }
                return true;
            });
        }

        // Aplicar ordenamiento
        filtered.sort((a, b) => {
            if (this.sortBy === 'nombre') {
                return (a.nombre_proyecto || '').localeCompare(b.nombre_proyecto || '');
            } else if (this.sortBy === 'fecha') {
                const fechaA = new Date(a.created_at || a.fecha_creacion || 0);
                const fechaB = new Date(b.created_at || b.fecha_creacion || 0);
                return fechaB - fechaA; // más recientes primero
            } else if (this.sortBy === 'balance') {
                const balanceA = (a.total_ingresos || 0) - (a.total_gastos || 0);
                const balanceB = (b.total_ingresos || 0) - (b.total_gastos || 0);
                return balanceB - balanceA; // mayor balance primero
            } else if (this.sortBy === 'ingresos') {
                return (b.total_ingresos || 0) - (a.total_ingresos || 0); // mayores ingresos primero
            } else if (this.sortBy === 'gastos') {
                return (b.total_gastos || 0) - (a.total_gastos || 0); // mayores gastos primero
            }
            return 0;
        });

        return filtered;
    },

    clearFilters() {
        this.searchQuery = '';
        this.filterEstado = 'todos';
        this.filterBalance = 'todos';
        this.sortBy = 'nombre';
    }
}" x-init="init()">
    {{-- Header con navegación de proyecto y botón de nuevo proyecto --}}
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <button @click="previousProyecto()" :disabled="proyectos.length === 0 || loading" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"><i class="fas fa-chevron-left"></i></button>
            <div class="flex items-center space-x-2">
                <h2 @click="openProjectListModal()" class="text-xl nunito-bold text-gray-800 dark:text-white cursor-pointer hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors" x-text="loading ? 'Cargando...' : (currentProyecto ? currentProyecto.nombre_proyecto : 'No hay proyectos')"></h2>
                <span x-show="!loading && proyectos.length > 0" class="text-sm text-gray-500 dark:text-gray-400 nunito-regular" x-text="'(' + (currentProyectoIndex + 1) + ' de ' + proyectos.length + ')'"></span>
                <span x-show="loading" class="inline-block w-4 h-4 border-2 border-gray-300 border-t-gray-600 rounded-full animate-spin"></span>
            </div>
            <button @click="nextProyecto()" :disabled="proyectos.length === 0 || loading" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"><i class="fas fa-chevron-right"></i></button>
        </div>
       <div class="bg-transparent items-center justify-center flex">
        <a href="{{ route('admin.reporte-proyecto') }}" target="_blank" class="flex items-center gap-2 px-6 py-2 border-2 border-emerald-500 rounded-md text-emerald-700 dark:text-emerald-400 nunito-bold text-sm hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white transition-colors duration-300 w-full min-w-[170px] justify-center">
            <i class="fas fa-file-pdf"></i>
            Generar PDF
        </a>
</div>

    </div>
    {{-- Tarjetas de estadísticas (diseño moderno mejorado) --}}
    <div class="top-4 grid grid-cols-1 sm:grid-cols-3 gap-6 bg-gray-50 dark:bg-gray-800 -mx-6 px-6 py-4 rounded-lg" x-show="currentProyecto" x-transition>
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-700 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm nunito-regular opacity-90">Ingresos</p>
                    </div>
                </div>
                <div class="text-3xl nunito-bold mb-1" x-text="formatCurrency((currentProyecto && currentProyecto.total_ingresos) ? currentProyecto.total_ingresos : 0)"></div>
                <p class="text-sm nunito-regular opacity-80">Total recibido</p>
                
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-800 to-amber-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-down text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm nunito-regular opacity-90">Gastos</p>
                    </div>
                </div>
                <div class="text-3xl nunito-bold mb-1" x-text="formatCurrency((currentProyecto && currentProyecto.total_gastos) ? currentProyecto.total_gastos : 0)"></div>
                <p class="text-sm nunito-regular opacity-80">Total gastado</p>
                
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-800 to-blue-500 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-balance-scale text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm nunito-regular opacity-90">Balance</p>
                    </div>
                </div>
                <div class="text-3xl nunito-bold mb-1" x-text="formatCurrency(((currentProyecto && currentProyecto.total_ingresos) ? currentProyecto.total_ingresos : 0) - ((currentProyecto && currentProyecto.total_gastos) ? currentProyecto.total_gastos : 0))"></div>
                <p class="text-sm nunito-regular opacity-80">Saldo neto</p>
                
            </div>
        </div>
    </div>

    {{-- Mensaje cuando no hay proyectos --}}
    <div x-show="!loading && proyectos.length === 0" class="text-center py-12">
        <i class="fas fa-folder-open text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
        <h3 class="text-xl nunito-bold text-gray-600 dark:text-gray-400 mb-2">No hay proyectos registrados</h3>
        <p class="text-gray-500 dark:text-gray-500">Crea tu primer proyecto para comenzar</p>
    </div>

    {{-- Historial de Movimientos (diseño moderno con tarjetas responsive) --}}
    <div class="mt-6 z-0" x-show="currentProyecto" x-transition>
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm dark:shadow-lg border border-gray-400 dark:border-gray-700">
            <div class="p-6 border-b border-gray-400 dark:border-gray-700">
                <h3 class="text-lg nunito-bold text-gray-800 dark:text-white">Historial de Movimientos</h3>
            </div>

            {{-- Lista combinada de movimientos (ingresos + gastos) --}}
            <div class="p-6">
                <div x-show="loadingMovimientos" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Cargando movimientos...</p>
                </div>

                <div x-show="!loadingMovimientos">
                    <template x-if="(ingresosProyecto.length + gastosProyecto.length) === 0">
                        <div class="p-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <div class="nunito-bold">No hay movimientos para este proyecto</div>
                            <div class="text-sm mt-2 text-gray-400" x-text="currentProyecto ? currentProyecto.nombre_proyecto : ''"></div>
                        </div>
                    </template>

                    <template x-if="(ingresosProyecto.length + gastosProyecto.length) > 0">
                        <div class="space-y-4">
                            <!-- Combine and sort by date descending -->
                            <template x-for="(mov, idx) in combinedMovimientos()" :key="(mov.__tipo || 'mov') + '_' + (mov.id_ingresos_pk || mov.id_gasto_pk || idx)">
                                <div :class="mov.__tipo === 'ingreso' ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-700' : 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-700'" class="p-4 rounded-lg border flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="mov.__tipo === 'ingreso' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'">
                                            <i :class="mov.__tipo === 'ingreso' ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                        </div>
                                        <div>
                                            <div class="nunito-bold text-gray-800 dark:text-white" x-text="mov.nombre_ingreso || mov.nombre"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="mov.categoria ? mov.categoria.nombre_categoria || mov.categoria.nombre : (mov.descripcion || '')"></div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="nunito-bold" :class="mov.__tipo==='ingreso' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" x-text="mov.__tipo === 'ingreso' ? formatCurrency(mov.monto_ingreso || mov.monto) : formatCurrency(mov.monto || mov.monto_ingreso)"></div>
                                        <div class="text-xs text-gray-500" x-text="formatDate(mov.__fecha)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Lista de Proyectos --}}
    <div x-show="showProjectListModal" x-cloak @click.self="closeProjectListModal()" style="z-index: 10001; backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);" class="fixed inset-0 -top-10 left-0 right-0 bottom-0 bg-black bg-opacity-50 flex items-center justify-center p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.stop class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            {{-- Header del Modal --}}
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 p-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder-open text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl nunito-bold text-white">Lista de Proyectos</h3>
                        <p class="text-sm text-emerald-100" x-text="proyectos.length + ' proyecto' + (proyectos.length !== 1 ? 's' : '') + ' disponible' + (proyectos.length !== 1 ? 's' : '')"></p>
                    </div>
                </div>
                <button @click="closeProjectListModal()" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Contenido del Modal --}}
            <div class="overflow-y-auto max-h-[calc(80vh-120px)]">
                {{-- Barra de búsqueda y filtros --}}
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 space-y-4 z-10">
                    {{-- Buscador --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input x-model="searchQuery" type="text" placeholder="Buscar proyectos por nombre o descripción..." class="w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white placeholder-gray-400 focus:border-black transition-all">
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Filtros --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        {{-- Filtro de Estado --}}
                        <div>
                            <label class="block text-sm nunito-bold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-tasks mr-1"></i>Estado del Proyecto
                            </label>
                            <select x-model="filterEstado" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:border-black text-sm">
                                <option value="todos">Todos los estados</option>
                                <option value="activos">🟢 Activos (Balance +)</option>
                                <option value="completados">⚪ Equilibrados (Balance 0)</option>
                                <option value="deficit">🔴 En Déficit (Balance -)</option>
                            </select>
                        </div>

                        {{-- Filtro de Balance --}}
                        <div>
                            <label class="block text-sm nunito-bold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-chart-line mr-1"></i>Tipo de Balance
                            </label>
                            <select x-model="filterBalance" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:border-black text-sm">
                                <option value="todos">Todos los balances</option>
                                <option value="positivo">Positivo (Ingresos > Gastos)</option>
                                <option value="negativo">Negativo (Gastos > Ingresos)</option>
                                <option value="cero">Equilibrado (Ingresos = Gastos)</option>
                            </select>
                        </div>

                        {{-- Filtro de Ordenamiento --}}
                        <div>
                            <label class="block text-sm nunito-bold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-sort mr-1"></i>Ordenar por
                            </label>
                            <select x-model="sortBy" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:border-black text-sm">
                                <option value="nombre">Nombre (A-Z)</option>
                                <option value="fecha">Fecha (Más recientes)</option>
                                <option value="balance">Balance (Mayor primero)</option>
                                <option value="ingresos">Ingresos (Mayor primero)</option>
                                <option value="gastos">Gastos (Mayor primero)</option>
                            </select>
                        </div>

                        {{-- Botón limpiar filtros --}}
                        <div class="flex items-end">
                            <button @click="clearFilters()" class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg transition-colors nunito-bold text-sm">
                                <i class="fas fa-redo mr-1"></i>Limpiar Filtros
                            </button>
                        </div>
                    </div>

                    {{-- Contador de resultados --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400 nunito-regular">
                        <span x-text="filteredProyectos().length"></span> proyecto<span x-show="filteredProyectos().length !== 1">s</span> encontrado<span x-show="filteredProyectos().length !== 1">s</span>
                    </div>
                </div>

                {{-- Lista de proyectos --}}
                <div class="p-6">
                <div class="space-y-3">
                    <template x-for="(proyecto, index) in filteredProyectos()" :key="proyecto.id_proyecto_pk || index">
                        <div @click="selectProyecto(proyectos.indexOf(proyecto))" :class="proyectos.indexOf(proyecto) === currentProyectoIndex ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500' : 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 hover:border-emerald-400 dark:hover:border-emerald-500'" class="border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div :class="proyectos.indexOf(proyecto) === currentProyectoIndex ? 'bg-emerald-500' : 'bg-gray-400 dark:bg-gray-500'" class="w-12 h-12 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                        <i class="fas fa-project-diagram text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="nunito-bold text-gray-800 dark:text-white truncate" x-text="proyecto.nombre_proyecto"></h4>
                                            <span x-show="proyectos.indexOf(proyecto) === currentProyectoIndex" class="bg-emerald-500 text-white text-xs px-2 py-1 rounded-full nunito-bold flex-shrink-0">
                                                Actual
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate" x-text="proyecto.descripcion_proyecto || proyecto.descripcion || 'Sin descripción'"></p>
                                        {{-- Mostrar balance --}}
                                        <div class="mt-2 flex items-center gap-3">
                                            <span class="text-xs nunito-bold" :class="((proyecto.total_ingresos || 0) - (proyecto.total_gastos || 0)) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                                                Balance: <span x-text="formatCurrency((proyecto.total_ingresos || 0) - (proyecto.total_gastos || 0))"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="filteredProyectos().length === 0">
                    <div class="text-center py-12">
                        <i class="fas fa-search text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 nunito-bold mb-2">No se encontraron proyectos</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Intenta ajustar los filtros de búsqueda</p>
                    </div>
                </template>
                </div>
            </div>
        </div>
    </div>
</div>