@extends('cliente.layouts.app')
@section('title','Cotizaciones - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="cotizacionesCliente()">
    <!-- Tarjetas resumen -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 serif">
        <!-- Total Cotizaciones -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total Cotizaciones</p>
                    <p class="text-3xl font-bold mt-2">25</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Cotizaciones Aprobadas -->
        <div class="bg-gradient-to-r from-green-700 to-green-900 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Aprobadas</p>
                    <p class="text-3xl font-bold mt-2">18</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Cotizaciones Pendientes -->
        <div class="bg-gradient-to-r from-cyan-800 to-cyan-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Pendientes</p>
                    <p class="text-3xl font-bold mt-2">7</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-hourglass-half text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-4 serif">
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
                <button @click="resetFiltros()" class="text-xs px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">Reiniciar</button>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Código</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Válido Hasta</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Subtotal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">No hay cotizaciones que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="c in paginadas" :key="c.codigo">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="c.codigo"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="c.fecha"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="c.valido_hasta"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="'$' + c.subtotal.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="'$' + c.total.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide" :class="estadoBadge(c.estado)" x-text="c.estado"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito text-center">
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
                <button @click="prev()" :disabled="page===1" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Anterior</button>
                <button @click="next()" :disabled="page===totalPages" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Siguiente</button>
            </div>
        </div>
    </div>
</div>

<script>
// Definir la función globalmente para que pueda ser usada por Alpine.js en navegación SPA
if (typeof window.cotizacionesCliente === 'undefined') {
    window.cotizacionesCliente = function() {
        return {
            page: 1,
            pageSize: 8,
            filtros: { search:'', estado:'', desde:'', hasta:'' },
            estados: ['Pendiente','Aprobada','Rechazada','Vencida'],
            datos: Array.from({length:19}).map((_,i)=>({
                codigo: 'COT-' + String(1000+i),
                fecha: new Date(Date.now()- (i*86400000)).toISOString().substring(0,10),
                valido_hasta: new Date(Date.now() + (30*86400000)).toISOString().substring(0,10),
                subtotal: Math.round(400 + Math.random()*4000),
                total: Math.round(500 + Math.random()*5000),
                estado: ['Pendiente','Aprobada','Rechazada'][i%3]
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
                    {key:'total',label:'Total',valor: total},
                    {key:'aprob',label:'Aprobadas',valor: aprob},
                    {key:'pend',label:'Pendientes',valor: pend},
                ];
            }
        }
    };
}
</script>
@endsection