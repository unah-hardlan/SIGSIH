@extends('cliente.layouts.app')
@section('title','Órdenes de Servicio - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="ordenesCliente()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 serif">Órdenes de Servicio</h1>
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">Portal
                Cliente</span>
            <span>/</span>
            <span class="text-gray-600 dark:text-gray-300">Órdenes</span>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Total Órdenes -->
        <div
            class="w-full min-h-[96px] bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 text-base">
            <div class="flex items-center justify-between h-full">
                <div>
                    <p class="text-base font-medium opacity-90">Total Órdenes</p>
                    <p class="text-2xl md:text-3xl font-bold mt-2" x-text="totalOrdenes"></p>
                </div>
                <div class="bg-white/20 p-4 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Abiertas -->
        <div
            class="w-full min-h-[96px] bg-gradient-to-r from-emerald-800 to-green-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 text-base">
            <div class="flex items-center justify-between h-full">
                <div>
                    <p class="text-base font-medium opacity-90">Abiertas</p>
                    <p class="text-2xl md:text-3xl font-bold mt-2" x-text="abiertasCount"></p>
                </div>
                <div class="bg-white/20 p-4 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-folder-open text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Cerradas -->
        <div
            class="w-full min-h-[96px] bg-gradient-to-r from-slate-800 to-slate-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 text-base">
            <div class="flex items-center justify-between h-full">
                <div>
                    <p class="text-base font-medium opacity-90">Cerradas</p>
                    <p class="text-2xl md:text-3xl font-bold mt-2" x-text="cerradasCount"></p>
                </div>
                <div class="bg-white/20 p-4 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-4">
        <div class="flex flex-col xl:flex-row gap-4">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <input x-model.debounce.400ms="filtros.search" type="text" placeholder="Buscar número o técnico..."
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-10 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
                <select x-model="filtros.estado"
                    class="w-40 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                    <option value="">Estado</option>
                    <template x-for="e in estados" :key="e">
                        <option :value="e" x-text="e"></option>
                    </template>
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

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Número</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Fecha Creada</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Fecha Recepción</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Técnico</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="6"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">
                                No hay órdenes que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="o in paginadas" :key="o.numero">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.numero"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.fecha_creada"></td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide"
                                    :class="estadoBadge(o.estado)" x-text="o.estado"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.fecha_recepcion"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.tecnico"></td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="'/cliente/detalle-orden?orden=' + o.id" target="_blank" rel="noopener"
                                        data-no-spa
                                        class="px-3 py-1.5 rounded-md text-[11px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition inline-flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>Ver</span>
                                    </a>
                                    <button @click="calificarOrden(o)" x-show="isCalificable(o)" x-cloak
                                        class="px-3 py-1.5 rounded-md text-[11px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition inline-flex items-center gap-1">
                                        <i class="fas fa-star"></i>
                                        <span>Calificar</span>
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

    <!-- Modal Calificación (dentro del mismo scope x-data) -->
    <div x-show="showRateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showRateModal=false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-5">
            <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-100">Calificar servicio</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Selecciona una calificación para la orden <span
                    x-text="selected?.numero || ''"></span>.</p>
            <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                <label
                    class="flex items-center gap-2 px-3 py-2 rounded border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                    <input type="radio" name="rate" value="excelente" x-model="rateValue">
                    <span>Excelente</span>
                </label>
                <label
                    class="flex items-center gap-2 px-3 py-2 rounded border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                    <input type="radio" name="rate" value="bueno" x-model="rateValue">
                    <span>Bueno</span>
                </label>
                <label
                    class="flex items-center gap-2 px-3 py-2 rounded border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                    <input type="radio" name="rate" value="regular" x-model="rateValue">
                    <span>Regular</span>
                </label>
                <label
                    class="flex items-center gap-2 px-3 py-2 rounded border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                    <input type="radio" name="rate" value="deficiente" x-model="rateValue">
                    <span>Deficiente</span>
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <button @click="showRateModal=false" class="px-3 py-1.5 rounded border text-sm">Cancelar</button>
                <button @click="submitRate()" :disabled="!rateValue"
                    class="px-3 py-1.5 rounded bg-emerald-600 text-white text-sm disabled:opacity-50">Confirmar</button>
            </div>
        </div>

        <style>
        [x-cloak] {
            display: none !important
        }
        </style>
    </div>

    <!-- Modal Confirmación de Calificación -->
    <div x-show="showRatedModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showRatedModal=false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-sm p-5 text-center">
            <div
                class="mx-auto mb-3 w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-300">
                <i class="fas fa-check text-lg"></i>
            </div>
            <h3 class="text-lg font-semibold mb-1 text-gray-800 dark:text-gray-100">¡Calificación registrada!</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Gracias por calificar el servicio. Tu opinión nos
                ayuda a mejorar.</p>
            <button @click="showRatedModal=false"
                class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-sm">Aceptar</button>
        </div>
    </div>
</div>

<script>
// Definir la función globalmente para que pueda ser usada por Alpine.js en navegación SPA
if (typeof window.ordenesCliente === 'undefined') {
    window.ordenesCliente = function() {
        return {
            page: 1,
            pageSize: 8,
            loading: false,
            showRateModal: false,
            showRatedModal: false,
            rateValue: '',
            selected: null,
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
                    const res = await fetch('/cliente/ordenes-data', {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    const j = await res.json();
                    const arr = j.data || [];
                    this.datos = Array.isArray(arr) ? arr : [];
                    // Derivar catálogo de estados únicos
                    const uniq = {};
                    this.datos.forEach(d => {
                        const k = (d.estado || '').trim();
                        if (k && !uniq[k]) uniq[k] = k;
                    });
                    this.estados = Object.values(uniq);
                } catch (e) {
                    this.datos = [];
                    this.estados = [];
                } finally {
                    this.loading = false;
                }
                // Watchers para reiniciar paginación
                const debounce = (fn, ms = 300) => {
                    let h;
                    return (...a) => {
                        clearTimeout(h);
                        h = setTimeout(() => fn(...a), ms);
                    }
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
                return this.datos.filter(d => {
                    const s = this.filtros.search.toLowerCase();
                    const estadoOk = !this.filtros.estado || d.estado === this.filtros.estado;
                    const textoOk = !s || d.numero.toLowerCase().includes(s) || d.tecnico.toLowerCase()
                        .includes(s);
                    const desdeOk = !this.filtros.desde || d.fecha_creada >= this.filtros.desde;
                    const hastaOk = !this.filtros.hasta || d.fecha_creada <= this.filtros.hasta;
                    return estadoOk && textoOk && desdeOk && hastaOk;
                });
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.filtradas.length / this.pageSize));
            },
            get totalOrdenes() {
                // Total basado en resultados filtrados (más útil para el usuario)
                return this.filtradas.length;
            },
            get abiertasCount() {
                // Consideramos "Abiertas" como estados no finales (Programada/En Proceso/Abierta)
                const open = ['programada', 'en proceso', 'abierta'];
                return this.filtradas.filter(d => open.includes(String(d.estado || '').toLowerCase())).length;
            },
            get cerradasCount() {
                // Consideramos "Cerradas" = Finalizada, Cancelada, Cerrada
                const closed = ['finalizada', 'cancelada', 'cerrada'];
                return this.filtradas.filter(d => closed.includes(String(d.estado || '').toLowerCase())).length;
            },
            get paginadas() {
                const s = (this.page - 1) * this.pageSize;
                return this.filtradas.slice(s, s + this.pageSize);
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
            estadoBadge(e) {
                const key = String(e || '').toLowerCase();
                return {
                    'programada': 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                    'en proceso': 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                    'finalizada': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                    'cancelada': 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    'abierta': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                    'cerrada': 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200'
                } [key] || 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
            },
            // Mostrar botón Calificar si el estado es final/cerrado con varios aliases
            isCalificable(o) {
                const n = String((o && o.estado) || '').toLowerCase();
                if (!n) return false;
                if (o && o.calificada === true) return false;
                // Coincidencias comunes: cerrada/o, finalizada/o, resuelta/o
                if (n.includes('cerrad') || n.includes('finaliz') || n.includes('resuelt')) return true;
                // Otros alias posibles: completada/o, concluida/o
                if (n.includes('complet') || n.includes('conclu')) return true;
                return false;
            },
            calificarOrden(o) {
                this.selected = o;
                this.rateValue = '';
                this.showRateModal = true;
            },
            async submitRate() {
                if (!this.selected || !this.selected.id || !this.rateValue) return;
                try {
                    const tokenEl = document.querySelector('meta[name="csrf-token"]');
                    const csrf = tokenEl ? tokenEl.getAttribute('content') : '';
                    const res = await fetch(`/cliente/ordenes/${this.selected.id}/calificar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            calificacion: this.rateValue
                        }),
                        credentials: 'same-origin'
                    });
                    const j = await res.json().catch(() => ({}));
                    if (!res.ok || j.success === false) {
                        alert('No se pudo calificar la orden');
                        return;
                    }
                    // éxito
                    this.showRateModal = false;
                    this.showRatedModal = true;
                    try {
                        const id = this.selected.id;
                        const idx = this.datos.findIndex(d => d.id === id);
                        if (idx >= 0) this.datos[idx].calificada = true;
                        this.selected.calificada = true;
                    } catch (_) {}
                } catch (e) {
                    console.error(e);
                    alert('No se pudo calificar la orden');
                }
            }
        }
    };
}
</script>
@endsection