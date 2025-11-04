@extends('cliente.layouts.app')
@section('title','Tickets - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="ticketsCliente()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 serif">Tickets</h1>
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">Portal Cliente</span>
            <span>/</span>
            <span class="text-gray-600 dark:text-gray-300">Tickets</span>
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
                            Fecha Creación</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Técnico</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Descripción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="5"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">
                                No hay tickets que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="t in paginadas" :key="t.id">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="t.numero"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="t.fecha_creacion"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide" :class="estadoBadge(t.estado)" x-text="t.estado"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="t.tecnico"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="t.descripcion"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-xs bg-gray-50 dark:bg-gray-900/40" x-show="filtradas.length > pageSize">
            <div class="text-gray-600 dark:text-gray-400" x-text="'Mostrando ' + inicioPagina + '-' + finPagina + ' de ' + filtradas.length"></div>
            <div class="flex items-center gap-1">
                <button @click="prev()" :disabled="page===1" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Anterior</button>
                <button @click="next()" :disabled="page===totalPages" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Siguiente</button>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof window.ticketsCliente === 'undefined') {
        window.ticketsCliente = function() {
            return {
                page: 1,
                pageSize: 10,
                loading: false,
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
                        const res = await fetch('/cliente/tickets-data', {
                            headers: {
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });
                        const j = await res.json();
                        const arr = j.data || [];
                        this.datos = Array.isArray(arr) ? arr : [];
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
                        const s = (this.filtros.search || '').toLowerCase();
                        const estadoOk = !this.filtros.estado || d.estado === this.filtros.estado;
                        const textoOk = !s || d.numero.toLowerCase().includes(s) || (d.tecnico || '').toLowerCase().includes(s);
                        const desdeOk = !this.filtros.desde || d.fecha_creacion >= this.filtros.desde;
                        const hastaOk = !this.filtros.hasta || d.fecha_creacion <= this.filtros.hasta;
                        return estadoOk && textoOk && desdeOk && hastaOk;
                    });
                },
                get totalPages() {
                    return Math.max(1, Math.ceil(this.filtradas.length / this.pageSize));
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
                    const k = String(e || '').toLowerCase();
                    return {
                        'pendiente': 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                        'en proceso': 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                        'asignado': 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
                        'cerrado': 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                        'resuelto': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                    } [k] || 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
                }
            }
        }
    }
</script>
@endsection