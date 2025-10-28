@extends('cliente.layouts.app')
@section('title','Facturación - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="facturasCliente()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Facturación</h1>
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">Portal Cliente</span>
            <span>/</span>
            <span class="text-gray-600 dark:text-gray-300">Facturas</span>
        </div>
    </div>

    <!-- Resumen -->
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
            </div>
        </template>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-4">
        <div class="flex flex-col xl:flex-row gap-4">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <input x-model.debounce.400ms="filtros.search" type="text" placeholder="Buscar # factura..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-10 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
                <select x-model="filtros.estado" class="w-40 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                    <option value="">Estado</option>
                    <template x-for="e in estados" :key="e"><option :value="e" x-text="e"></option></template>
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

    <!-- Tabla (facturas + detalles) -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Factura</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">OC</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Subtotal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Impuesto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Descuento</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">No hay facturas.</td>
                        </tr>
                    </template>
                    <template x-for="f in paginadas" :key="f.numero">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="f.numero"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="f.fecha"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="f.oc"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="'$' + f.subtotal.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="'$' + f.impuesto.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="'$' + f.descuento.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="'$' + f.total.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide" :class="estadoBadge(f.estado)" x-text="f.estado"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="verDetalle(f)" class="px-2.5 py-1.5 rounded-md text-[11px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition flex items-center gap-1"><i class="fas fa-eye"></i><span>Ver</span></button>
                                    <button class="px-2.5 py-1.5 rounded-md text-[11px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition flex items-center gap-1"><i class="fas fa-file-download"></i><span>PDF</span></button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-xs bg-gray-50 dark:bg-gray-900/40" x-show="filtradas.length > pageSize">
            <div class="text-gray-600 dark:text-gray-400" x-text="'Mostrando ' + inicioPagina + '-' + finPagina + ' de ' + filtradas.length"></div>
            <div class="flex items-center gap-1">
                <button @click="prev()" :disabled="page===1" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Prev</button>
                <button @click="next()" :disabled="page===totalPages" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Factura -->
    <template x-teleport="body">
        <div x-show="modalFactura" x-cloak x-transition.opacity.duration.300ms class="fixed inset-0 flex items-center justify-center z-[9999] bg-black/50 backdrop-blur-sm" @click.self="modalFactura=false" @keydown.window.escape="modalFactura=false">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-4xl mx-auto max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Detalle de Factura</h3>
                    <button @click="modalFactura=false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"><i class="fas fa-times"></i></button>
                </div>

                <template x-if="facturaActual">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Factura</label>
                                <p class="text-gray-900 dark:text-gray-100 font-semibold" x-text="facturaActual.numero"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha</label>
                                <p class="text-gray-900 dark:text-gray-100" x-text="facturaActual.fecha"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">OC</label>
                                <p class="text-gray-900 dark:text-gray-100" x-text="facturaActual.oc"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal</label>
                                <p class="text-gray-900 dark:text-gray-100" x-text="'$' + facturaActual.subtotal.toLocaleString()"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Impuesto</label>
                                <p class="text-gray-900 dark:text-gray-100" x-text="'$' + facturaActual.impuesto.toLocaleString()"></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Detalle de líneas</label>
                            <div class="overflow-x-auto mt-2">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Servicio</th>
                                            <th class="px-3 py-2 text-right">Precio Unit.</th>
                                            <th class="px-3 py-2 text-right">Cantidad</th>
                                            <th class="px-3 py-2 text-right">Impuesto</th>
                                            <th class="px-3 py-2 text-right">Total Línea</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="d in facturaActual.detalles" :key="d.id">
                                            <tr>
                                                <td class="px-3 py-2" x-text="d.descripcion"></td>
                                                <td class="px-3 py-2 text-right" x-text="'$' + d.precio_unitario.toLocaleString()"></td>
                                                <td class="px-3 py-2 text-right" x-text="d.cantidad"></td>
                                                <td class="px-3 py-2 text-right" x-text="'$' + d.impuesto.toLocaleString()"></td>
                                                <td class="px-3 py-2 text-right" x-text="'$' + d.total_linea.toLocaleString()"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <button @click="modalFactura=false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition">Cerrar</button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
// Definir la función globalmente para que pueda ser usada por Alpine.js en navegación SPA
if (typeof window.facturasCliente === 'undefined') {
    window.facturasCliente = function() {
        return {
            page:1,pageSize:8,
            filtros:{search:'',estado:'',desde:'',hasta:''},
            estados:['Pagada','Pendiente','Vencida','Anulada'],
            datos: Array.from({length:25}).map((_,i)=>({
                numero:'FAC-'+(500+i),
                fecha:new Date(Date.now() - (i*43200000)).toISOString().substring(0,10),
                oc: 'OC-' + (1000 + i),
                subtotal: Math.round(100 + Math.random()*4000),
                impuesto: Math.round(10 + Math.random()*500),
                descuento: Math.round(Math.random()*200),
                total: 0, // calculado abajo
                estado:['Pagada','Pendiente','Vencida'][i%3],
                detalles: [
                    { id: 1, descripcion: 'Servicio A', precio_unitario: 150, cantidad: 2, impuesto: 15, total_linea: 315 },
                    { id: 2, descripcion: 'Servicio B', precio_unitario: 200, cantidad: 1, impuesto: 20, total_linea: 220 }
                ]
            })).map(f=>{ f.total = f.subtotal + f.impuesto - f.descuento; return f; }),
            modalFactura: false,
            facturaActual: null,
            get filtradas(){
                return this.datos.filter(d=>{
                    const s=this.filtros.search.toLowerCase();
                    const eOk=!this.filtros.estado||d.estado===this.filtros.estado;
                    const sOk=!s||d.numero.toLowerCase().includes(s);
                    const dOk=!this.filtros.desde||d.fecha>=this.filtros.desde;
                    const hOk=!this.filtros.hasta||d.fecha<=this.filtros.hasta;
                    return eOk&&sOk&&dOk&&hOk;
                });
            },
            verDetalle(f){ this.facturaActual = f; this.modalFactura = true; },
            get totalPages(){return Math.max(1,Math.ceil(this.filtradas.length/this.pageSize));},
            get paginadas(){const s=(this.page-1)*this.pageSize;return this.filtradas.slice(s,s+this.pageSize);},
            get inicioPagina(){return this.filtradas.length===0?0:((this.page-1)*this.pageSize+1);},
            get finPagina(){return Math.min(this.filtradas.length,this.page*this.pageSize);},
            prev(){if(this.page>1)this.page--;},next(){if(this.page<this.totalPages)this.page++;},
            resetFiltros(){this.filtros={search:'',estado:'',desde:'',hasta:''};this.page=1;},
            estadoBadge(e){return {
                'Pagada':'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                'Pendiente':'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                'Vencida':'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                'Anulada':'bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
            }[e]||'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200';},
            get resumen(){
                const total=this.datos.length;
                const pagadas=this.datos.filter(d=>d.estado==='Pagada').length;
                const pendiente=this.datos.filter(d=>d.estado==='Pendiente').length;
                const monto='$'+this.datos.reduce((a,b)=>a+b.monto,0).toLocaleString();
                return [
                    {key:'tot',label:'Total',valor:total,icon:'fas fa-file-invoice'},
                    {key:'pag',label:'Pagadas',valor:pagadas,icon:'fas fa-check-circle'},
                    {key:'pen',label:'Pendientes',valor:pendiente,icon:'fas fa-hourglass-half'},
                    {key:'mon',label:'Importe Total',valor:monto,icon:'fas fa-hand-holding-usd'}
                ];
            }
        }
    };
}
</script>
@endsection