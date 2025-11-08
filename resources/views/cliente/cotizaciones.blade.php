@extends('cliente.layouts.app')
@section('title','Cotizaciones - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="cotizacionesCliente()">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 serif">Cotizaciones</h1>

    <div
        class="text-gray-700 dark:text-gray-300 dark:bg-gray-800 mb-4 serif border border-gray-400 p-4 bg-indigo-200 rounded-md">
        Las cotizaciones es la propuesta comercial que le hemos preparado. Desde esta sección puede revisarlas, aprobarlas o rechazarlas según su conveniencia. Puede hacer clic en el botón "Ver" para abrir la cotización en una nueva pestaña y revisarla en detalle. Si está de acuerdo con los términos, puede aprobarla haciendo clic en "Aprobar". Si no está de acuerdo, puede rechazarla haciendo clic en "Rechazar". Recuerde que una vez aprobada o rechazada, la cotización no podrá ser modificada. 
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-5 serif">
        <div
            class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total Cotizaciones</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.total"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-green-700 to-green-900 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Aprobadas</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.aprobadas"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-amber-600 to-amber-800 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Borrador</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.borrador"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-file-pen text-2xl"></i>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-red-500 to-red-900 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Rechazadas</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.rechazadas"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-slate-600 to-slate-800 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Vencidas</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.vencidas"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-calendar-times text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-4 serif">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <input x-model.debounce.400ms="filtros.search" type="text"
                        placeholder="Buscar código o descripción..."
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-10 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
                <select x-model="filtros.estado"
                    class="w-40 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                    <option value="">Todos los estados</option>
                    <option value="BRD">Borrador</option>
                    <option value="APB">Aprobada</option>
                    <option value="REC">Rechazada</option>
                    <option value="VEN">Vencida</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input x-model="filtros.desde" type="date"
                    class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                <span class="text-gray-400 text-xs">→</span>
                <input x-model="filtros.hasta" type="date"
                    class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                <button @click="resetFiltros()"
                    class="text-xs px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">Reiniciar</button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Código</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Fecha</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Válido Hasta</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Subtotal</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Total</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="7"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">
                                No hay cotizaciones que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="c in paginadas" :key="c.codigo">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="c.codigo"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="c.fecha"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="c.valido_hasta"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="formatHNL(c.subtotal)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="formatHNL(c.total)"></td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide"
                                    :class="estadoBadge(c)" x-text="c.estado_nombre || '—'"></span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="'/cliente/detalle-cotizacion?id='+c.id" target="_blank" rel="noopener"
                                        data-no-spa
                                        class="px-3 py-1.5 rounded-md text-[11px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition inline-flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>Ver</span>
                                    </a>
                                    <button @click="solicitarConfirmacion(c, 'aprobada')" x-show="puedeGestionar(c)"
                                        :disabled="accion.loading && accion.id === c.id"
                                        :class="{'opacity-60 cursor-not-allowed': accion.loading && accion.id === c.id}"
                                        class="px-3 py-1.5 rounded-md text-[11px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition inline-flex items-center gap-1 disabled:hover:bg-emerald-600">
                                        <i class="fas fa-check"></i>
                                        <span>Aprobar</span>
                                    </button>
                                    <button @click="solicitarConfirmacion(c, 'rechazada')" x-show="puedeGestionar(c)"
                                        :disabled="accion.loading && accion.id === c.id"
                                        :class="{'opacity-60 cursor-not-allowed': accion.loading && accion.id === c.id}"
                                        class="px-3 py-1.5 rounded-md text-[11px] bg-red-600 hover:bg-red-700 text-white font-medium transition inline-flex items-center gap-1 disabled:hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                        <span>Rechazar</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-xs bg-gray-50 dark:bg-gray-900/40"
            x-show="filtradas.length > pageSize">
            <div class="text-gray-600 dark:text-gray-400"
                x-text="'Mostrando ' + inicioPagina + '-' + finPagina + ' de ' + filtradas.length"></div>
            <div class="flex items-center gap-1">
                <button @click="prev()" :disabled="page===1"
                    class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Anterior</button>
                <button @click="next()" :disabled="page===totalPages"
                    class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Siguiente</button>
            </div>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="confirmacion.open" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 z-[10001] flex items-center justify-center bg-black/65 dark:bg-black/75 backdrop-blur-sm"
            @click.self="cancelarConfirmacion()" @keydown.window.escape="cancelarConfirmacion()">
            <div x-show="confirmacion.open" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative z-50 w-full max-w-md mx-4 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                @click.stop>
                <div :class="confirmAccent('header')" class="px-4 py-3 font-semibold flex items-center gap-2">
                    <i :class="confirmIcon()"></i>
                    <span x-text="confirmTitle()"></span>
                </div>
                <div class="px-5 py-5 text-sm text-gray-600 dark:text-gray-300 space-y-3">
                    <p x-text="confirmMessage()"></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Solo podrás realizar esta acción una vez.</p>
                </div>
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/40 flex justify-end gap-2">
                    <button @click="cancelarConfirmacion()"
                        class="px-4 py-2 rounded-md text-sm font-medium bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition"
                        :disabled="accion.loading">
                        Cancelar
                    </button>
                    <button @click="confirmarAccion()" :disabled="accion.loading"
                        class="px-4 py-2 rounded-md text-sm font-semibold transition"
                        :class="[confirmAccent('button'), {'opacity-60 cursor-not-allowed': accion.loading}]">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="feedback.open" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 z-[10002] flex items-center justify-center bg-black/70 dark:bg-black/80 backdrop-blur-sm"
            @click.self="closeFeedback()" @keydown.window.escape="closeFeedback()">
            <div x-show="feedback.open" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative z-50 w-full max-w-sm mx-4 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                @click.stop>
                <div :class="feedback.variant==='success' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-200'"
                    class="px-4 py-3 font-semibold flex items-center gap-2">
                    <i :class="feedback.variant==='success' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                    <span x-text="feedback.title"></span>
                </div>
                <div class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300" x-text="feedback.message"></div>
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/40 flex justify-end">
                    <button @click="closeFeedback()"
                        class="px-4 py-2 rounded-md text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white transition">Aceptar</button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
if (typeof window.cotizacionesCliente === 'undefined') {
    window.cotizacionesCliente = function() {
        return {
            page: 1,
            pageSize: 8,
            loading: false,
            accion: {
                loading: false,
                id: null
            },
            feedback: {
                open: false,
                title: '',
                message: '',
                variant: 'success'
            },
            confirmacion: {
                open: false,
                estado: null,
                cotizacion: null
            },
            filtros: {
                search: '',
                estado: '',
                desde: '',
                hasta: ''
            },
            estados: [], 
            datos: [],
            async init() {
                this.loading = true;
                try {
                    const res = await fetch('/cliente/cotizaciones-data', {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    const j = await res.json();
                    const arr = j.data || [];
                    this.datos = Array.isArray(arr) ? arr : [];
                    const map = {};
                    this.datos.forEach(d => {
                        const cod = d.estado_codigo || '';
                        const nom = d.estado_nombre || '';
                        const key = cod || nom;
                        if (key && !map[key]) {
                            map[key] = {
                                codigo: cod || nom,
                                nombre: nom || cod
                            };
                        }
                    });
                    this.estados = Object.values(map);
                } catch (e) {
                    this.datos = [];
                } finally {
                    this.loading = false;
                }
                const debounce = (fn, ms = 300) => {
                    let h;
                    return (...a) => {
                        clearTimeout(h);
                        h = setTimeout(() => fn(...a), ms);
                    };
                };
                this.$watch('filtros.search', debounce(() => {
                    this.page = 1;
                }));
                this.$watch('filtros.estado', () => {
                    this.page = 1;
                });
                this.$watch('filtros.desde', () => {
                    this.page = 1;
                });
                this.$watch('filtros.hasta', () => {
                    this.page = 1;
                });
            },
            get filtradas() {
                const s = (this.filtros.search || '').toLowerCase();
                const est = (this.filtros.estado || '').toLowerCase();
                return this.datos.filter(d => {
                    let estadoOk = true;
                    if (est) {
                        const estadoCodigo = String(d.estado_codigo || '').toLowerCase();
                        const estadoNombre = String(d.estado_nombre || '').toLowerCase();
                        
                        switch(est) {
                            case 'brd':
                                estadoOk = ['brd', 'borrador', 'pendiente'].includes(estadoCodigo) || 
                                          ['borrador', 'pendiente'].includes(estadoNombre);
                                break;
                            case 'apb':
                                estadoOk = ['apb', 'aprobada', 'aprobado'].includes(estadoCodigo) || 
                                          ['aprobada', 'aprobado', 'aprobadas', 'aprobados'].includes(estadoNombre);
                                break;
                            case 'rec':
                                estadoOk = ['rec', 'rechazada', 'rechazado'].includes(estadoCodigo) || 
                                          ['rechazada', 'rechazado', 'rechazadas', 'rechazados'].includes(estadoNombre);
                                break;
                            case 'ven':
                                estadoOk = ['ven', 'vencida', 'vencido'].includes(estadoCodigo) || 
                                          ['vencida', 'vencido', 'vencidas', 'vencidos'].includes(estadoNombre);
                                break;
                            default:
                                estadoOk = estadoCodigo === est || estadoNombre === est;
                        }
                    }
                    
                    const textoOk = !s || String(d.codigo || '').toLowerCase().includes(s);
                    const desdeOk = !this.filtros.desde || String(d.fecha || '') >= this.filtros.desde;
                    const hastaOk = !this.filtros.hasta || String(d.fecha || '') <= this.filtros.hasta;
                    return estadoOk && textoOk && desdeOk && hastaOk;
                });
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.filtradas.length / this.pageSize));
            },
            get paginadas() {
                const start = (this.page - 1) * this.pageSize;
                return this.filtradas.slice(start, start + this.pageSize);
            },
            get inicioPagina() {
                return this.filtradas.length === 0 ? 0 : ((this.page - 1) * this.pageSize + 1);
            },
            get finPagina() {
                return Math.min(this.filtradas.length, this.page * this.pageSize);
            },
            prev() {
                if (this.page > 1) this.page--;
            },
            next() {
                if (this.page < this.totalPages) this.page++;
            },
            resetFiltros() {
                this.filtros = {
                    search: '',
                    estado: '',
                    desde: '',
                    hasta: ''
                };
                this.page = 1;
            },
            puedeGestionar(c) {
                const code = (c && c.estado_codigo) ? String(c.estado_codigo).toUpperCase() : '';
                const name = (c && c.estado_nombre) ? String(c.estado_nombre).toLowerCase() : '';
                const finales = ['APB', 'REC'];
                const finalesNombre = ['aprobada', 'aprobado', 'rechazada', 'rechazado'];
                if (finales.includes(code)) return false;
                if (finalesNombre.includes(name)) return false;
                return true;
            },
            estadoBadge(c) {
                const code = (c && c.estado_codigo) ? String(c.estado_codigo).toUpperCase() : '';
                const name = (c && c.estado_nombre) ? String(c.estado_nombre) : '';
                const byCode = {
                    'BRD': 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                    'APB': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                    'REC': 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    'VEN': 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                } [code];
                if (byCode) return byCode;
                return {
                    'borrador': 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                    'pendiente': 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                    'aprobada': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                    'rechazada': 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    'vencida': 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                } [name.toLowerCase()] || 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
            },
            formatHNL(n) {
                const v = Number(n || 0);
                try {
                    return new Intl.NumberFormat('es-HN', {
                        style: 'currency',
                        currency: 'HNL'
                    }).format(v);
                } catch (e) {
                    return 'L. ' + v.toFixed(2);
                }
            },
            showFeedback(title, message, variant = 'success') {
                this.feedback.title = title || '';
                this.feedback.message = message || '';
                this.feedback.variant = variant || 'success';
                this.feedback.open = true;
                this.updateScrollLock();
            },
            closeFeedback() {
                this.feedback.open = false;
                this.updateScrollLock();
            },
            updateScrollLock() {
                const target = document.documentElement;
                if (!target) return;
                const shouldLock = !!(this.feedback.open || this.confirmacion.open);
                target.classList.toggle('overflow-hidden', shouldLock);
            },
            solicitarConfirmacion(c, estado) {
                if (!this.puedeGestionar(c) || this.accion.loading) return;
                this.confirmacion.cotizacion = c;
                this.confirmacion.estado = estado;
                this.confirmacion.open = true;
                this.updateScrollLock();
            },
            cancelarConfirmacion() {
                this.confirmacion.open = false;
                this.confirmacion.estado = null;
                this.confirmacion.cotizacion = null;
                this.updateScrollLock();
            },
            confirmarAccion() {
                const c = this.confirmacion.cotizacion;
                const estado = this.confirmacion.estado;
                if (!c || !estado) return;
                this.confirmacion.open = false;
                this.confirmacion.estado = null;
                this.confirmacion.cotizacion = null;
                this.updateScrollLock();
                this.cambiarEstado(c, estado);
            },
            confirmTitle() {
                const est = String(this.confirmacion.estado || '').toLowerCase();
                if (est.startsWith('aprob')) return 'Confirmar aprobación';
                if (est.startsWith('rech')) return 'Confirmar rechazo';
                return 'Confirmar acción';
            },
            confirmMessage() {
                const est = String(this.confirmacion.estado || '').toLowerCase();
                const accion = est.startsWith('aprob') ? 'aprobar' : est.startsWith('rech') ? 'rechazar' :
                    'gestionar';
                const codigo = this.confirmacion.cotizacion?.codigo;
                const objetivo = codigo ? `la cotización ${codigo}` : 'esta cotización';
                return `¿Está seguro de ${accion} ${objetivo}? Esta acción no se puede deshacer.`;
            },
            confirmIcon() {
                const est = String(this.confirmacion.estado || '').toLowerCase();
                if (est.startsWith('aprob')) return 'fas fa-check-circle';
                if (est.startsWith('rech')) return 'fas fa-times-circle';
                return 'fas fa-exclamation-circle';
            },
            confirmAccent(part = 'header') {
                const est = String(this.confirmacion.estado || '').toLowerCase();
                if (est.startsWith('aprob')) {
                    return part === 'header' ?
                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200' :
                        'bg-emerald-600 hover:bg-emerald-700 text-white';
                }
                if (est.startsWith('rech')) {
                    return part === 'header' ?
                        'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-200' :
                        'bg-red-600 hover:bg-red-700 text-white';
                }
                return part === 'header' ?
                    'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200' :
                    'bg-blue-600 hover:bg-blue-700 text-white';
            },
            async cambiarEstado(c, estado) {
                if (!c || !c.id) return;
                if (this.accion.loading) return;
                this.accion.loading = true;
                this.accion.id = c.id;
                try {
                    const tokenEl = document.querySelector('meta[name="csrf-token"]');
                    const csrf = tokenEl ? tokenEl.getAttribute('content') : '';
                    const res = await fetch(`/cliente/cotizaciones/${c.id}/cambiar-estado`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            estado
                        }),
                        credentials: 'same-origin'
                    });
                    if (res.ok) {
                        if (String(estado).toLowerCase() === 'aprobada') {
                            c.estado_nombre = 'Aprobada';
                            c.estado_codigo = 'APB';
                            this.showFeedback('Cotización aprobada',
                                'La cotización fue aprobada correctamente.', 'success');
                        } else if (String(estado).toLowerCase() === 'rechazada') {
                            c.estado_nombre = 'Rechazada';
                            c.estado_codigo = 'REC';
                            this.showFeedback('Cotización rechazada',
                                'La cotización fue rechazada correctamente.', 'success');
                        }
                    } else {
                        let msg = 'No se pudo cambiar el estado de la cotización.';
                        try {
                            const err = await res.json();
                            const detail = err?.message || err?.error || err?.errors?.estado?. [0];
                            if (detail) msg = detail;
                        } catch (parseErr) {}
                        this.showFeedback('Error', msg, 'error');
                    }
                } catch (e) {
                    console.error(e);
                    this.showFeedback('Error', 'Ocurrió un problema al procesar la solicitud.', 'error');
                } finally {
                    this.accion.loading = false;
                    this.accion.id = null;
                    this.updateScrollLock();
                }
            },
            get resumen() {
                const total = this.datos.length;
                const aprob = this.datos.filter(d => String(d.estado_codigo || '').toUpperCase() === 'APB' ||
                    String(d.estado_nombre || '').toLowerCase() === 'aprobada').length;
                const pend = this.datos.filter(d => String(d.estado_codigo || '').toUpperCase() === 'BRD' ||
                    String(d.estado_nombre || '').toLowerCase() === 'pendiente').length;
                const rec = this.datos.filter(d => String(d.estado_codigo || '').toUpperCase() === 'REC' || [
                    'rechazada', 'rechazado'
                ].includes(String(d.estado_nombre || '').toLowerCase())).length;
                const ven = this.datos.filter(d => String(d.estado_codigo || '').toUpperCase() === 'VEN' || [
                    'vencida', 'vencido'
                ].includes(String(d.estado_nombre || '').toLowerCase())).length;
                return {
                    total,
                    aprobadas: aprob,
                    borrador: pend,
                    vencidas: ven,
                    rechazadas: rec,
                    pendientes: pend
                };
            }
        }
    };
}
</script>
@endsection