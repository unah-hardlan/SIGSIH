@extends('cliente.layouts.app')
@section('title','Solicitudes - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 mt-16" x-data="solicitudesCliente()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold serif tracking-tight text-gray-900 dark:text-gray-100">Solicitudes de Soporte</h1>
    </div>

    <div class="text-gray-700 dark:text-gray-300 dark:bg-gray-800 mb-4 serif border border-gray-400 p-4 bg-indigo-200 rounded-md">
        Las solicitudes de soporte le permiten reportar problemas técnicos de su empresa u organización. Nuestro equipo de soporte está comprometido a resolver sus inquietudes de manera efectiva. Por favor, proporcione detalles claros y específicos al crear una solicitud. Una vez sea aprobada se lo notificaremos para propocionarle una cotización de su solicitud.
    </div>

     <button @click="modalNueva = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Nueva Solicitud
        </button>

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
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Número de Solicitud</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Nombre de la Solicitud</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Descripción del Problema</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">Estado de la Solicitud</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-for="solicitud in solicitudesFiltradas" :key="solicitud.id">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="solicitud.numero_solicitud_cliente"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="solicitud.nombre_solicitud"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="solicitud.descripcion_problema"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-nunito" x-text="solicitud.estado"></td>
                        </tr>
                    </template>
                    <tr x-show="solicitudesFiltradas.length === 0">
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">No se encontraron solicitudes.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <template x-teleport="body">
        <!-- Modal Nueva Solicitud -->
        <div x-show="modalNueva" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 flex items-center justify-center z-[9999] bg-black/50 backdrop-blur-sm"
            @click.self="modalNueva = false" @keydown.window.escape="modalNueva = false"
            style="margin: 0;">
            <div x-transition:enter="transition ease-out duration-300" 
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
                        <label for="nombre_solicitud" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre de la Solicitud</label>
                        <input id="nombre_solicitud" x-model="nuevaSolicitud.nombre_solicitud" type="text" 
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" 
                            placeholder="Ingrese el nombre de la solicitud" required />
                    </div>
                    <div>
                        <label for="descripcion_problema" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción del Problema</label>
                        <textarea id="descripcion_problema" x-model="nuevaSolicitud.descripcion_problema" 
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" 
                            placeholder="Describa el problema" rows="4" required></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="modalNueva = false" 
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Cancelar</button>
                        <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Detalle Solicitud -->
        <div x-show="modalDetalle" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 flex items-center justify-center z-[9999] bg-black/70 dark:bg-black/80"
            @click.self="modalDetalle = false" @keydown.window.escape="modalDetalle = false"
            style="margin: 0;">
            <div x-transition:enter="transition ease-out duration-300" 
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
    </template>
</div>

<script>
if (typeof window.solicitudesCliente === 'undefined') {
    window.solicitudesCliente = function() {
        return {
            solicitudes: [
                { id: 1, numero_solicitud_acf: 'ACF-001', numero_solicitud_cliente: 'CLI-2025-001', nombre_solicitud: 'Reporte de Inventario', descripcion_problema: 'Error al generar reporte de inventario', estado: 'Pendiente', contacto: 'Juan Pérez' },
                { id: 2, numero_solicitud_acf: 'ACF-002', numero_solicitud_cliente: 'CLI-2025-002', nombre_solicitud: 'Consulta Facturación', descripcion_problema: 'Consulta sobre facturación electrónica', estado: 'Resuelta', contacto: 'María García' },
                { id: 3, numero_solicitud_acf: 'ACF-003', numero_solicitud_cliente: 'CLI-2025-003', nombre_solicitud: 'Acceso al Sistema', descripcion_problema: 'Problema de acceso al sistema', estado: 'En Proceso', contacto: 'Carlos López' }
            ],
            filtros: {
                search: '',
                estado: ''
            },
            modalNueva: false,
            modalDetalle: false,
            solicitudActual: null,
            nuevaSolicitud: {
                nombre_solicitud: '',
                descripcion_problema: ''
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
                    numero_solicitud_cliente: `CLI-2025-${String(this.solicitudes.length + 1).padStart(3, '0')}`,
                    descripcion_problema: this.nuevaSolicitud.descripcion_problema,
                    estado: 'Pendiente',
                    contacto: 'Usuario Actual'
                };
                this.solicitudes.unshift(nuevaSol);
                this.modalNueva = false;
                this.nuevaSolicitud = { nombre_solicitud: '', descripcion_problema: '' };
                
                console.log('Nueva solicitud creada:', nuevaSol);
            }
        };
    };
}
</script>
@endsection