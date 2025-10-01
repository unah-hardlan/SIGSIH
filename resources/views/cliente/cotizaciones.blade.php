@extends('cliente.layouts.app')
@section('title','Cotizaciones - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-8" x-data="cotizacionesCliente()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Cotizaciones</h1>
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">Portal Cliente</span>
            <span>/</span>
            <span class="text-gray-600 dark:text-gray-300">Cotizaciones</span>
        </div>
    </div>

    <!-- Tarjetas resumen -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <template x-for="card in resumen" :key="card.key">
            <div class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400" x-text="card.label"></p>
                        <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="card.valor"></p>
                    </div>
                    <div class="text-blue-500/30 dark:text-blue-400/30">
                        <i :class="card.icon + ' text-3xl'"></i>
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1" x-show="card.delta">
                    <i class="fas fa-arrow-up text-emerald-500" x-show="card.delta > 0"></i>
                    <i class="fas fa-arrow-down text-red-500" x-show="card.delta < 0"></i>
                    <span x-text="Math.abs(card.delta) + '%'"></span>
                    <span class="ml-1">vs mes anterior</span>
                </p>
            </div>
        </template>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <input x-model.debounce.400ms="filtros.search" type="text" placeholder="Buscar código o descripción..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-10 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
                <select x-model="filtros.estado" class="w-40 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                    <option value="">Estado</option>
                    <template x-for="e in estados" :key="e">
                        <option :value="e" x-text="e"></option>
                    </template>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input x-model="filtros.desde" type="date" class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                <span class="text-gray-400 text-xs">→</span>
                <input x-model="filtros.hasta" type="date" class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                <button @click="resetFiltros()" class="text-xs px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">Reset</button>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700/60 text-gray-700 dark:text-gray-200 text-[11px] uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Código</th>
                        <th class="px-4 py-2 text-left font-semibold">Fecha</th>
                        <th class="px-4 py-2 text-left font-semibold">Estado</th>
                        <th class="px-4 py-2 text-right font-semibold">Total</th>
                        <th class="px-4 py-2 text-center font-semibold">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-xs text-gray-500 dark:text-gray-400">No hay cotizaciones que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="c in paginadas" :key="c.codigo">
                        <tr class="border-t border-gray-100 dark:border-gray-700/60 hover:bg-blue-50 dark:hover:bg-gray-700/60 transition">
                            <td class="px-4 py-2 font-mono text-xs" x-text="c.codigo"></td>
                            <td class="px-4 py-2" x-text="c.fecha"></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide" :class="estadoBadge(c.estado)" x-text="c.estado"></span>
                            </td>
                            <td class="px-4 py-2 text-right font-semibold" x-text="'$' + c.total.toLocaleString()"></td>
                            <td class="px-4 py-2 text-center">
                                <button class="px-3 py-1.5 rounded-md text-[11px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition flex items-center gap-1 mx-auto">
                                    <i class="fas fa-eye"></i>
                                    <span>Ver</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <!-- Paginación simple -->
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-xs bg-gray-50 dark:bg-gray-900/40" x-show="filtradas.length > pageSize">
            <div class="text-gray-600 dark:text-gray-400" x-text="'Mostrando ' + inicioPagina + '-' + finPagina + ' de ' + filtradas.length"></div>
            <div class="flex items-center gap-1">
                <button @click="prev()" :disabled="page===1" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Prev</button>
                <button @click="next()" :disabled="page===totalPages" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Next</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cotizacionesCliente(){
    return {
        page: 1,
        pageSize: 8,
        filtros: { search:'', estado:'', desde:'', hasta:'' },
        estados: ['Pendiente','Aprobada','Rechazada','Vencida'],
        datos: Array.from({length:19}).map((_,i)=>({
            codigo: 'COT-' + String(1000+i),
            fecha: new Date(Date.now()- (i*86400000)).toISOString().substring(0,10),
            estado: ['Pendiente','Aprobada','Rechazada'][i%3],
            total: Math.round(500 + Math.random()*5000)
        })),
        get filtradas(){
            return this.datos.filter(d=>{
                const s = this.filtros.search.toLowerCase();
                const estadoOk = !this.filtros.estado || d.estado === this.filtros.estado;
                const textoOk = !s || d.codigo.toLowerCase().includes(s);
                const desdeOk = !this.filtros.desde || d.fecha >= this.filtros.desde;
                const hastaOk = !this.filtros.hasta || d.fecha <= this.filtros.hasta;
                return estadoOk && textoOk && desdeOk && hastaOk;
            });
        },
        get totalPages(){ return Math.max(1, Math.ceil(this.filtradas.length/ this.pageSize)); },
        get paginadas(){
            const start = (this.page-1)*this.pageSize;
            return this.filtradas.slice(start, start+this.pageSize);
        },
        get inicioPagina(){ return this.filtradas.length ===0 ? 0 : ( (this.page-1)*this.pageSize + 1); },
        get finPagina(){ return Math.min(this.filtradas.length, this.page*this.pageSize); },
        prev(){ if(this.page>1) this.page--; },
        next(){ if(this.page<this.totalPages) this.page++; },
        resetFiltros(){ this.filtros={search:'',estado:'',desde:'',hasta:''}; this.page=1; },
        estadoBadge(e){
            return {
                'Pendiente':'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                'Aprobada':'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                'Rechazada':'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                'Vencida':'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
            }[e] || 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
        },
        get resumen(){
            const total = this.datos.length;
            const aprob = this.datos.filter(d=>d.estado==='Aprobada').length;
            const pend = this.datos.filter(d=>d.estado==='Pendiente').length;
            return [
                {key:'total',label:'Total',valor: total,icon:'fas fa-layer-group',delta:5},
                {key:'aprob',label:'Aprobadas',valor: aprob,icon:'fas fa-check-circle',delta:2},
                {key:'pend',label:'Pendientes',valor: pend,icon:'fas fa-hourglass-half',delta:-3},
                {key:'importe',label:'Importe Estimado',valor: '$' + this.datos.reduce((a,b)=>a+b.total,0).toLocaleString(),icon:'fas fa-dollar-sign',delta:8},
            ];
        }
    }
}
</script>
@endpush
@endsection