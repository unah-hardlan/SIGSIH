@extends('cliente.layouts.app')
@section('title','Solicitudes - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="solicitudesCliente()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Solicitudes de Soporte</h1>
        <button @click="modalNueva = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Nueva Solicitud
        </button>
    </div>

    <!-- Tarjetas resumen -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="resumen.total"></p>
                </div>
                <div class="text-blue-500/30 dark:text-blue-400/30">
                    <i class="fas fa-inbox text-3xl"></i>
                </div>
            </div>
        </div>
        
        <div class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Pendientes</p>
                    <p class="mt-2 text-2xl font-bold text-orange-600 dark:text-orange-400" x-text="resumen.pendientes"></p>
                </div>
                <div class="text-orange-500/30 dark:text-orange-400/30">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">En Proceso</p>
                    <p class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="resumen.proceso"></p>
                </div>
                <div class="text-blue-500/30 dark:text-blue-400/30">
                    <i class="fas fa-sync text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Resueltas</p>
                    <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400" x-text="resumen.resueltas"></p>
                </div>
                <div class="text-green-500/30 dark:text-green-400/30">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input x-model.debounce.400ms="filtros.search" type="text" placeholder="Buscar por asunto o descripción..." 
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
            </div>
            <select x-model="filtros.estado" class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="En Proceso">En Proceso</option>
                <option value="Resuelta">Resuelta</option>
                <option value="Cerrada">Cerrada</option>
            </select>
        </div>
    </div>

    <!-- Lista de solicitudes -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nº Solicitud ACF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nº Solicitud Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descripción del Problema</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="solicitud in solicitudesFiltradas" :key="solicitud.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100" x-text="solicitud.numero_solicitud_acf"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" x-text="solicitud.numero_solicitud_cliente"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100 max-w-md" x-text="solicitud.descripcion_problema"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="{
                                        'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300': solicitud.estado === 'Pendiente',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': solicitud.estado === 'En Proceso',
                                        'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': solicitud.estado === 'Resuelta',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300': solicitud.estado === 'Cerrada'
                                    }"
                                    x-text="solicitud.estado">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" x-text="solicitud.contacto"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button @click="verDetalle(solicitud)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="solicitudesFiltradas.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No hay solicitudes registradas</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nueva Solicitud -->
    <div x-show="modalNueva" x-cloak x-transition.opacity.duration.300ms
        class="fixed inset-0 flex items-center justify-center z-[10000] bg-black/70 dark:bg-black/80"
        @click.self="modalNueva = false" @keydown.window.escape="modalNueva = false">
        <div x-show="modalNueva" x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-2xl mx-auto max-h-[90vh] overflow-y-auto" 
            @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Nueva Solicitud de Soporte</h3>
                <button @click="modalNueva = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form @submit.prevent="crearSolicitud" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Asunto *</label>
                    <input x-model="nuevaSolicitud.asunto" type="text" required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" 
                        placeholder="Ej: Problema con el sistema de facturación">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descripción *</label>
                    <textarea x-model="nuevaSolicitud.descripcion" rows="4" required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" 
                        placeholder="Describe detalladamente el problema que estás experimentando..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prioridad *</label>
                        <select x-model="nuevaSolicitud.prioridad" required
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                            <option value="">Selecciona...</option>
                            <option value="Baja">Baja</option>
                            <option value="Media">Media</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Categoría *</label>
                        <select x-model="nuevaSolicitud.categoria" required
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                            <option value="">Selecciona...</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Facturación">Facturación</option>
                            <option value="Consulta">Consulta</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="modalNueva = false" 
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm transition-colors">
                        Crear Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detalle Solicitud -->
    <div x-show="modalDetalle" x-cloak x-transition.opacity.duration.300ms
        class="fixed inset-0 flex items-center justify-center z-[10000] bg-black/70 dark:bg-black/80"
        @click.self="modalDetalle = false" @keydown.window.escape="modalDetalle = false">
        <div x-show="modalDetalle" x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-2xl mx-auto max-h-[90vh] overflow-y-auto" 
            @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Detalle de Solicitud</h3>
                <button @click="modalDetalle = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <template x-if="solicitudActual">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nº Solicitud ACF</label>
                        <p class="text-gray-900 dark:text-gray-100 font-semibold" x-text="solicitudActual.numero_solicitud_acf"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nº Solicitud Cliente</label>
                        <p class="text-gray-900 dark:text-gray-100" x-text="solicitudActual.numero_solicitud_cliente"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Descripción del Problema</label>
                        <p class="text-gray-900 dark:text-gray-100" x-text="solicitudActual.descripcion_problema"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Estado</label>
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full mt-1"
                                :class="{
                                    'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300': solicitudActual.estado === 'Pendiente',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': solicitudActual.estado === 'En Proceso',
                                    'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': solicitudActual.estado === 'Resuelta',
                                    'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300': solicitudActual.estado === 'Cerrada'
                                }"
                                x-text="solicitudActual.estado">
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Contacto</label>
                            <p class="text-gray-900 dark:text-gray-100 mt-1" x-text="solicitudActual.contacto"></p>
                        </div>
                    </div>
                </div>
            </template>
            
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="modalDetalle = false" 
                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof window.solicitudesCliente === 'undefined') {
    window.solicitudesCliente = function() {
        return {
            solicitudes: [
                { id: 1, numero_solicitud_acf: 'ACF-001', numero_solicitud_cliente: 'CLI-2025-001', descripcion_problema: 'Error al generar reporte de inventario', estado: 'Pendiente', contacto: 'Juan Pérez' },
                { id: 2, numero_solicitud_acf: 'ACF-002', numero_solicitud_cliente: 'CLI-2025-002', descripcion_problema: 'Consulta sobre facturación electrónica', estado: 'Resuelta', contacto: 'María García' },
                { id: 3, numero_solicitud_acf: 'ACF-003', numero_solicitud_cliente: 'CLI-2025-003', descripcion_problema: 'Problema de acceso al sistema', estado: 'En Proceso', contacto: 'Carlos López' }
            ],
            filtros: {
                search: '',
                estado: ''
            },
            modalNueva: false,
            modalDetalle: false,
            solicitudActual: null,
            nuevaSolicitud: {
                asunto: '',
                descripcion: '',
                prioridad: '',
                categoria: ''
            },

            get resumen() {
                return {
                    total: this.solicitudes.length,
                    pendientes: this.solicitudes.filter(s => s.estado === 'Pendiente').length,
                    proceso: this.solicitudes.filter(s => s.estado === 'En Proceso').length,
                    resueltas: this.solicitudes.filter(s => s.estado === 'Resuelta').length
                };
            },

            get solicitudesFiltradas() {
                return this.solicitudes.filter(solicitud => {
                    const matchSearch = !this.filtros.search || 
                        solicitud.descripcion_problema.toLowerCase().includes(this.filtros.search.toLowerCase()) ||
                        solicitud.numero_solicitud_acf.toLowerCase().includes(this.filtros.search.toLowerCase()) ||
                        solicitud.numero_solicitud_cliente.toLowerCase().includes(this.filtros.search.toLowerCase());
                    const matchEstado = !this.filtros.estado || solicitud.estado === this.filtros.estado;
                    return matchSearch && matchEstado;
                });
            },

            verDetalle(solicitud) {
                this.solicitudActual = solicitud;
                this.modalDetalle = true;
            },

            crearSolicitud() {
                const nuevaSol = {
                    id: this.solicitudes.length + 1,
                    numero_solicitud_acf: `ACF-${String(this.solicitudes.length + 1).padStart(3, '0')}`,
                    ...this.nuevaSolicitud,
                    estado: 'Pendiente'
                };
                this.solicitudes.unshift(nuevaSol);
                this.modalNueva = false;
                this.nuevaSolicitud = { asunto: '', descripcion: '', prioridad: '', categoria: '' };
                
                // Aquí irá la petición AJAX al backend cuando esté listo
                console.log('Nueva solicitud creada:', nuevaSol);
            }
        };
    };
}
</script>
@endsection
