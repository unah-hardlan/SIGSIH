@extends('cliente.layouts.app')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="cotizacionesCliente()" x-init="init()">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 serif">Cotizaciones</h1>

    <div
        class="text-gray-700 dark:text-gray-300 dark:bg-gray-800 mb-4 serif border border-gray-400 p-4 bg-indigo-200 rounded-md">
        Las cotizaciones es la propuesta comercial que le hemos preparado. Desde esta sección puede revisarlas, aprobarlas o rechazarlas según su conveniencia. Puede hacer clic en el botón "Ver" para abrir la cotización en una nueva pestaña y revisarla en detalle. Si está de acuerdo con los términos, puede aprobarla haciendo clic en "Aprobar". Si no está de acuerdo, puede rechazarla haciendo clic en "Rechazar". Recuerde que una vez aprobada o rechazada, la cotización no podrá ser modificada. 
    </div>

    <div class="grid gap-2 grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 serif">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Total</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.total"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-layer-group text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-700 to-green-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Aprobadas</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.aprobadas"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-check-circle text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-amber-600 to-amber-800 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Borrador</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.borrador"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-file-pen text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Rechazadas</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.rechazadas"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-times-circle text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-slate-600 to-slate-800 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 col-span-2 sm:col-span-2 md:col-span-1">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Vencidas</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.vencidas"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-calendar-times text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 sm:p-4 space-y-3 sm:space-y-4 serif">
        <div class="flex flex-col gap-3 sm:gap-4">
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <div class="flex-1 flex items-center gap-2">
                    <div class="relative flex-1">
                        <input x-model.debounce.400ms="filtros.search" type="text"
                            placeholder="Buscar código..."
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-10 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                    </div>
                    <select x-model="filtros.estado"
                        class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-2 sm:px-3 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200 whitespace-nowrap">
                        <option value="">Todos</option>
                        <option value="BRD">Borrador</option>
                        <option value="APB">Aprobada</option>
                        <option value="REC">Rechazada</option>
                        <option value="VEN">Vencida</option>
                    </select>
                </div>
                <button @click="resetFiltros()"
                    class="text-xs px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition whitespace-nowrap">Reiniciar</button>
            </div>
<span class="text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">Desde:</span>
<div class="relative flex-1 sm:flex-none">
    <input x-model="filtros.desde" type="date"
        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 pl-2 pr-8 py-2 text-xs sm:text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none appearance-none [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:w-full" />
        
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-gray-400">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
</div>

<span class="text-gray-400 text-xs hidden sm:inline">→</span>
<span class="text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap sm:hidden">Hasta:</span>
<span class="text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap hidden sm:inline">Hasta:</span>

<div class="relative flex-1 sm:flex-none">
    <input x-model="filtros.hasta" type="date"
        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 pl-2 pr-8 py-2 text-xs sm:text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none appearance-none [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:w-full" />
        
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-gray-400">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hidden md:block">
        <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Código</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Fecha</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Válido Hasta</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Total</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="6"
                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">
                                No hay cotizaciones que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="c in paginadas" :key="c.codigo">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="c.codigo"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="c.fecha"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="c.valido_hasta"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="formatHNL(c.total)"></td>
                            <td
                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide"
                                    :class="estadoBadge(c)" x-text="c.estado_nombre || '—'"></span>
                            </td>
                            <td
                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 font-nunito text-center">
                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                    <a :href="'/cliente/detalle-cotizacion?id='+c.id" target="_blank" rel="noopener"
                                        data-no-spa
                                        class="px-2 py-1 rounded-md text-[10px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition inline-flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>Ver</span>
                                    </a>
                                    <button @click="solicitarConfirmacion(c, 'aprobada')" x-show="puedeGestionar(c)"
                                        :disabled="accion.loading && accion.id === c.id"
                                        :class="{'opacity-60 cursor-not-allowed': accion.loading && accion.id === c.id}"
                                        class="px-2 py-1 rounded-md text-[10px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition inline-flex items-center gap-1 disabled:hover:bg-emerald-600">
                                        <i class="fas fa-check"></i>
                                        <span>Aprobar</span>
                                    </button>
                                    <button @click="solicitarConfirmacion(c, 'rechazada')" x-show="puedeGestionar(c)"
                                        :disabled="accion.loading && accion.id === c.id"
                                        :class="{'opacity-60 cursor-not-allowed': accion.loading && accion.id === c.id}"
                                        class="px-2 py-1 rounded-md text-[10px] bg-red-600 hover:bg-red-700 text-white font-medium transition inline-flex items-center gap-1 disabled:hover:bg-red-600">
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
        <div class="flex flex-col sm:flex-row items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-xs bg-gray-50 dark:bg-gray-900/40 gap-3 sm:gap-0"
            x-show="filtradas.length > pageSize">
            <div class="text-gray-600 dark:text-gray-400"
                x-text="'Mostrando ' + inicioPagina + '-' + finPagina + ' de ' + filtradas.length"></div>
            <div class="flex items-center gap-1">
                <button @click="prev()" :disabled="page===1"
                    class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs">Anterior</button>
                <button @click="next()" :disabled="page===totalPages"
                    class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs">Siguiente</button>
            </div>
        </div>
    </div>

    <div class="space-y-3 md:hidden">
        <template x-if="filtradas.length === 0">
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay cotizaciones que coincidan.</p>
            </div>
        </template>
        <template x-for="c in paginadas" :key="c.codigo">
            <div class="bg-white dark:bg-gray-800 border border-gray-400/80 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Código</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="c.codigo"></p>
                        </div>
                        <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide flex-shrink-0" :class="estadoBadge(c)" x-text="c.estado_nombre || '—'"></span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fechas</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                            <span x-text="c.fecha"></span> → <span x-text="c.valido_hasta"></span>
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="formatHNL(c.total)"></p>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <a :href="'/cliente/detalle-cotizacion?id='+c.id" target="_blank" rel="noopener"
                            data-no-spa
                            class="flex-1 px-2 py-2 rounded-md text-[10px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition inline-flex items-center justify-center gap-1">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Ver</span>
                        </a>
                        <button @click="solicitarConfirmacion(c, 'aprobada')" x-show="puedeGestionar(c)"
                            :disabled="accion.loading && accion.id === c.id"
                            class="flex-1 px-2 py-2 rounded-md text-[10px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition inline-flex items-center justify-center gap-1 disabled:opacity-60">
                            <i class="fas fa-check"></i>
                            <span>Aprobar</span>
                        </button>
                        <button @click="solicitarConfirmacion(c, 'rechazada')" x-show="puedeGestionar(c)"
                            :disabled="accion.loading && accion.id === c.id"
                            class="flex-1 px-2 py-2 rounded-md text-[10px] bg-red-600 hover:bg-red-700 text-white font-medium transition inline-flex items-center justify-center gap-1 disabled:opacity-60">
                            <i class="fas fa-times"></i>
                            <span>Rechazar</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
        
        <div class="flex flex-col gap-3 items-center py-4" x-show="filtradas.length > pageSize">
            <div class="text-xs text-gray-600 dark:text-gray-400" x-text="'Mostrando ' + inicioPagina + '-' + finPagina + ' de ' + filtradas.length"></div>
            <div class="flex items-center gap-1">
                <button @click="prev()" :disabled="page===1" class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs">Anterior</button>
                <button @click="next()" :disabled="page===totalPages" class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs">Siguiente</button>
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

@endsection