@extends('cliente.layouts.app')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="ordenesCliente()" x-init="init()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 serif">Órdenes de Servicio</h1>
    </div>

    <div
        class="text-gray-700 dark:text-gray-300 dark:bg-gray-800 mb-4 serif border border-gray-400 p-4 bg-indigo-200 rounded-md">
        Las órdenes de servicio es el trabajo que se esta o estará realizando en su empresa. Desde esta sección puede revisar el estado de sus órdenes de servicio, filtrar por diferentes criterios y calificar el servicio recibido una vez finalizado. Haga clic en el botón "Ver" para abrir los detalles de la orden en una nueva pestaña. 
    </div>

    <div class="grid gap-2 grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 serif">
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Total Órdenes</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="totalOrdenes"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-clipboard-list text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Abiertas</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="abiertasCount"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-folder-open text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-slate-700 to-slate-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 col-span-2 sm:col-span-1">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Cerradas</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="cerradasCount"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-check-circle text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 sm:p-4 space-y-3 sm:space-y-4">
        <div class="flex flex-col gap-3 sm:gap-4">
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <div class="flex-1 flex items-center gap-2">
                    <div class="relative flex-1">
                        <input x-model.debounce.400ms="filtros.search" type="text" placeholder="Buscar número o técnico..."
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-10 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                    </div>
                    <select x-model="filtros.estado"
                        class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-2 sm:px-3 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200 whitespace-nowrap">
                        <option value="">Estado</option>
                        <template x-for="e in estados" :key="e">
                            <option :value="e" x-text="e"></option>
                        </template>
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
                            Número</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Fecha Creada</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Recepción</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Técnico</th>
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
                                No hay órdenes que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="o in paginadas" :key="o.numero">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.numero"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.fecha_creada"></td>
                            <td
                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide"
                                    :class="estadoBadge(o.estado)" x-text="o.estado"></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.fecha_recepcion"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="o.tecnico"></td>
                            <td
                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 font-nunito text-center">
                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                    <a :href="'/cliente/detalle-orden?orden=' + o.id" target="_blank" rel="noopener"
                                        data-no-spa
                                        class="px-2 py-1 rounded-md text-[10px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition inline-flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i>
                                        <span>Ver</span>
                                    </a>
                                    <button @click="calificarOrden(o)" x-show="isCalificable(o)" x-cloak
                                        class="px-2 py-1 rounded-md text-[10px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition inline-flex items-center gap-1">
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
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay órdenes que coincidan.</p>
            </div>
        </template>
        <template x-for="o in paginadas" :key="o.numero">
            <div class="bg-white dark:bg-gray-800 border border-gray-400/80 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Número</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="o.numero"></p>
                        </div>
                        <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide flex-shrink-0"
                            :class="estadoBadge(o.estado)" x-text="o.estado"></span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fechas</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                            Creada: <span x-text="o.fecha_creada"></span>
                        </p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                            Recepción: <span x-text="o.fecha_recepcion"></span>
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Técnico</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 mt-1" x-text="o.tecnico"></p>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <a :href="'/cliente/detalle-orden?orden=' + o.id" target="_blank" rel="noopener"
                            data-no-spa
                            class="flex-1 px-2 py-2 rounded-md text-[10px] bg-blue-600 hover:bg-blue-700 text-white font-medium transition inline-flex items-center justify-center gap-1">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Ver</span>
                        </a>
                        <button @click="calificarOrden(o)" x-show="isCalificable(o)" x-cloak
                            class="flex-1 px-2 py-2 rounded-md text-[10px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition inline-flex items-center justify-center gap-1">
                            <i class="fas fa-star"></i>
                            <span>Calificar</span>
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
        <div x-show="showRateModal" 
             x-cloak 
             x-transition.opacity.duration.300ms
             class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/70 dark:bg-black/80 backdrop-blur-sm"
             @click.self="showRateModal=false"
             @keydown.window.escape="showRateModal=false"
             style="margin: 0;">
            <div x-show="showRateModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="relative z-50 bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 p-5"
                 @click.stop>
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
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showRatedModal" 
             x-cloak 
             x-transition.opacity.duration.300ms
             class="fixed inset-0 z-[10001] flex items-center justify-center bg-black/70 dark:bg-black/80 backdrop-blur-sm"
             @click.self="showRatedModal=false"
             @keydown.window.escape="showRatedModal=false"
             style="margin: 0;">
            <div x-show="showRatedModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="relative z-50 bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-sm mx-4 p-5 text-center"
                 @click.stop>
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
    </template>

    <style>
    [x-cloak] {
        display: none !important
    }
    </style>
</div>

@endsection