@extends('cliente.layouts.app')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 mt-16" x-data="ticketsCliente()" x-init="init()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 serif">Tickets</h1>
        
    </div>

    <div
        class="text-gray-700 dark:text-gray-300 dark:bg-gray-800 mb-4 serif border border-gray-400 p-4 bg-indigo-200 rounded-md">
        Los tickets son las asignaciones del servicio que has solicitado y que nuestro equipo técnico está gestionando. Aquí puedes ver el estado actual de cada ticket, incluyendo detalles como la descripción del problema, el técnico asignado y las fechas relevantes.
    </div>

    <div class="grid gap-2 grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 serif">
        <div class="bg-gradient-to-r from-amber-600 to-amber-800 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Pendientes</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.pendientes"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-hourglass text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">En Proceso</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.enProceso"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-spinner text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-indigo-600 to-indigo-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Asignados</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.asignados"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-user-check text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Resueltos</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.resueltos"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-check-circle text-base sm:text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-slate-700 to-slate-900 text-white rounded-lg p-3 sm:p-6 shadow-lg hover:shadow-xl transition-shadow duration-300 col-span-2 sm:col-span-2 md:col-span-1">
            <div class="flex flex-col items-start gap-2 sm:gap-4">
                <div class="flex-1 w-full">
                    <p class="text-xs sm:text-sm font-medium opacity-90 truncate">Cerrados</p>
                    <p class="text-xl sm:text-3xl font-bold mt-1 sm:mt-2" x-text="resumen.cerrados"></p>
                </div>
                <div class="bg-white/20 p-1.5 sm:p-3 rounded-full ml-auto">
                    <i class="fas fa-check-double text-base sm:text-2xl"></i>
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
                        <option value="">Todos</option>
                        <template x-for="e in estados" :key="e">
                            <option :value="e" x-text="e"></option>
                        </template>
                    </select>
                </div>
                <button @click="resetFiltros()"
                    class="text-xs px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition whitespace-nowrap">Reiniciar</button>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-2 items-start sm:items-center">
    
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
                            Fecha</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Técnico</th>
                        <th scope="col"
                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Descripción</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-if="filtradas.length === 0">
                        <tr>
                            <td colspan="5"
                                class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">
                                No hay tickets que coincidan.</td>
                        </tr>
                    </template>
                    <template x-for="t in paginadas" :key="t.id">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito" x-text="t.numero"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito" x-text="t.fecha_creacion"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito">
                                <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide" :class="estadoBadge(t.estado)" x-text="t.estado"></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito" x-text="t.tecnico"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-left text-gray-900 dark:text-gray-100 font-nunito" x-text="t.descripcion"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex flex-col sm:flex-row items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-xs bg-gray-50 dark:bg-gray-900/40 gap-3 sm:gap-0" x-show="filtradas.length > pageSize">
            <div class="text-gray-600 dark:text-gray-400" x-text="'Mostrando ' + inicioPagina + '-' + finPagina + ' de ' + filtradas.length"></div>
            <div class="flex items-center gap-1">
                <button @click="prev()" :disabled="page===1" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs">Anterior</button>
                <button @click="next()" :disabled="page===totalPages" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-xs">Siguiente</button>
            </div>
        </div>
    </div>

    <div class="space-y-3 md:hidden">
        <template x-if="filtradas.length === 0">
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay tickets que coincidan.</p>
            </div>
        </template>
        <template x-for="t in paginadas" :key="t.id">
            <div class="bg-white dark:bg-gray-800 border border-gray-400/80 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Número</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="t.numero"></p>
                        </div>
                        <span class="px-2 py-1 rounded text-[10px] font-semibold tracking-wide flex-shrink-0" :class="estadoBadge(t.estado)" x-text="t.estado"></span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 mt-1" x-text="t.fecha_creacion"></p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Técnico</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 mt-1" x-text="t.tecnico"></p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descripción</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 line-clamp-3" x-text="t.descripcion"></p>
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
</div>

@endsection