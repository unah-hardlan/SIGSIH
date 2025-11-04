@extends('cliente.layouts.app')
@section('title','Facturación - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="facturasCliente()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Facturación</h1>
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">Portal
                Cliente</span>
            <span>/</span>
            <span class="text-gray-600 dark:text-gray-300">Facturas</span>
        </div>
    </div>

    <!-- Tarjetas resumen (mismo estilo que Órdenes) -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Total -->
        <div
            class="w-full min-h-[96px] bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 text-base">
            <div class="flex items-center justify-between h-full">
                <div>
                    <p class="text-base font-medium opacity-90">Total</p>
                    <p class="text-2xl md:text-3xl font-bold mt-2" x-text="totalFacturas"></p>
                </div>
                <div class="bg-white/20 p-4 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pagadas -->
        <div
            class="w-full min-h-[96px] bg-gradient-to-r from-emerald-800 to-green-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 text-base">
            <div class="flex items-center justify-between h-full">
                <div>
                    <p class="text-base font-medium opacity-90">Pagadas</p>
                    <p class="text-2xl md:text-3xl font-bold mt-2" x-text="pagadasCount"></p>
                </div>
                <div class="bg-white/20 p-4 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div
            class="w-full min-h-[96px] bg-gradient-to-r from-slate-800 to-slate-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 text-base">
            <div class="flex items-center justify-between h-full">
                <div>
                    <p class="text-base font-medium opacity-90">Pendientes</p>
                    <p class="text-2xl md:text-3xl font-bold mt-2" x-text="pendientesCount"></p>
                </div>
                <div class="bg-white/20 p-4 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-sm md:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-4">
        <div class="flex flex-col xl:flex-row gap-4">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <input x-model.debounce.400ms="filtros.search" type="text" placeholder="Buscar # factura..."
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
                    class="text-xs px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">Reset</button>
            </div>
        </div>
    </div>

    <!-- Tabla (facturas + detalles) -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Factura</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Fecha</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            OC</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Subtotal</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Impuesto</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Descuento</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Total</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="9"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">
                                No hay facturas.</td>
                        </tr>
                    </template>
                    <template x-for="f in paginadas" :key="f.numero">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="f.numero"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="f.fecha"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="f.oc"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="'$' + f.subtotal.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="'$' + f.impuesto.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="'$' + f.descuento.toLocaleString()"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="'$' + f.total.toLocaleString()"></td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide"
                                    :class="estadoBadge(f.estado)" x-text="f.estado"></span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="verDetalle(f)"
                                        class="px-2.5 py-1.5 rounded-md text-[11px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition flex items-center gap-1"><i
                                            class="fas fa-eye"></i><span>Ver</span></button>
                                    <a :href="`/cliente/formato-factura/${f.id}`" target="_blank" rel="noopener" data-no-spa
                                        class="px-2.5 py-1.5 rounded-md text-[11px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition flex items-center gap-1">
                                        <i class="fas fa-file-invoice"></i><span>Ver factura</span>
                                    </a>
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
                    class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Prev</button>
                <button @click="next()" :disabled="page===totalPages"
                    class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Factura -->
    <template x-teleport="body">
        <div x-show="modalFactura" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 flex items-center justify-center z-[9999] bg-black/50 backdrop-blur-sm"
            @click.self="modalFactura=false" @keydown.window.escape="modalFactura=false">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-4xl mx-auto max-h-[90vh] overflow-y-auto"
                @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Detalle de Factura</h3>
                    <button @click="modalFactura=false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"><i
                            class="fas fa-times"></i></button>
                </div>

                <template x-if="facturaActual">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400">Factura</label>
                                <p class="text-gray-900 dark:text-gray-100 font-semibold" x-text="facturaActual.numero">
                                </p>
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
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal</label>
                                <p class="text-gray-900 dark:text-gray-100"
                                    x-text="'$' + facturaActual.subtotal.toLocaleString()"></p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400">Impuesto</label>
                                <p class="text-gray-900 dark:text-gray-100"
                                    x-text="'$' + facturaActual.impuesto.toLocaleString()"></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Detalle de
                                líneas</label>
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
                                                <td class="px-3 py-2 text-right"
                                                    x-text="'$' + d.precio_unitario.toLocaleString()"></td>
                                                <td class="px-3 py-2 text-right" x-text="d.cantidad"></td>
                                                <td class="px-3 py-2 text-right"
                                                    x-text="'$' + d.impuesto.toLocaleString()"></td>
                                                <td class="px-3 py-2 text-right"
                                                    x-text="'$' + d.total_linea.toLocaleString()"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <button @click="modalFactura=false"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition">Cerrar</button>
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
                page: 1,
                pageSize: 8,
                filtros: {
                    search: '',
                    estado: '',
                    desde: '',
                    hasta: ''
                },
                estados: ['Pagada', 'Pendiente', 'Vencida', 'Anulada'],
                datos: [],
                modalFactura: false,
                facturaActual: null,
                async init() {
                    try {
                        const res = await fetch('/cliente/facturas-data', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            const payload = await res.json();
                            this.datos = Array.isArray(payload) ? payload : (payload.data || []);
                        }
                    } catch (e) {
                        /* noop */
                    }
                },
                get filtradas() {
                    return this.datos.filter(d => {
                        const s = this.filtros.search.toLowerCase();
                        const eOk = !this.filtros.estado || d.estado === this.filtros.estado;
                        const sOk = !s || d.numero.toLowerCase().includes(s);
                        const dOk = !this.filtros.desde || d.fecha >= this.filtros.desde;
                        const hOk = !this.filtros.hasta || d.fecha <= this.filtros.hasta;
                        return eOk && sOk && dOk && hOk;
                    });
                },
                async verDetalle(f) {
                    this.facturaActual = {
                        ...f,
                        detalles: Array.isArray(f.detalles) ? f.detalles : []
                    };
                    this.modalFactura = true;
                    if (!f.id) return;
                    try {
                        const res = await fetch(`/cliente/facturas/${f.id}/data`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            const det = await res.json();
                            // Mantener cabecera existente y solo cargar detalles si vienen
                            if (det && det.detalles) {
                                this.facturaActual.detalles = det.detalles;
                            }
                        }
                    } catch (e) {
                        /* noop */
                    }
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
                    return {
                        'Pagada': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                        'Pendiente': 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                        'Vencida': 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                        'Anulada': 'bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
                    } [e] || 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200';
                },
                // Contadores para tarjetas resumen
                get totalFacturas() {
                    return this.datos.length;
                },
                get pagadasCount() {
                    return this.datos.filter(d => d.estado === 'Pagada').length;
                },
                get pendientesCount() {
                    return this.datos.filter(d => d.estado === 'Pendiente').length;
                }
            }
        };
    }
</script>
@endsection