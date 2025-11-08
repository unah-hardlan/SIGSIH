@extends('cliente.layouts.app')
@section('title','Solicitudes - Cliente')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 mt-16" x-data="solicitudesCliente()" x-init="init()">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold serif tracking-tight text-gray-900 dark:text-gray-100">Solicitudes de Soporte</h1>
    </div>

    <div
        class="text-gray-700 dark:text-gray-300 dark:bg-gray-800 mb-4 serif border border-gray-400 p-4 bg-indigo-200 rounded-md">
        Las solicitudes de soporte le permiten reportar problemas técnicos de su empresa u organización. Nuestro equipo
        de soporte está comprometido a resolver sus inquietudes de manera efectiva. Por favor, proporcione detalles
        claros y específicos al crear una solicitud. Una vez sea aprobada se lo notificaremos para propocionarle una
        cotización de su solicitud.
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-5 serif">
        <div class="bg-gradient-to-r from-amber-600 to-amber-800 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">En espera</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.enEspera"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-indigo-600 to-indigo-900 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Asignadas</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.asignadas"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">En proceso</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.enProceso"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-spinner text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-700 to-red-900 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Rechazadas</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.rechazadas"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Finalizadas</p>
                    <p class="text-3xl font-bold mt-2" x-text="resumen.finalizadas"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <button @click="modalNueva = true"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
        <i class="fas fa-plus"></i>
        Nueva Solicitud
    </button>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input x-model.debounce.400ms="filtros.search" type="text"
                    placeholder="Buscar por asunto o descripción..."
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200" />
            </div>
            <select x-model="filtros.estado"
                class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200">
                <option value="">Todos los estados</option>
                <option value="Pendiente">En espera</option>
                <option value="En Proceso">Asignada</option>
                <option value="Resuelta">En proceso</option>
                <option value="Rechazada">Rechazadas</option>
                <option value="Cerrada">Finalizadas</option>
            </select>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Número de Solicitud</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Nombre de la Solicitud</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Descripción del Problema</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-serif">
                            Estado de la Solicitud</th>
                    </tr>
                </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-300 dark:divide-gray-700">
                    <template x-for="solicitud in solicitudesFiltradas" :key="solicitud.id">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito">
                                <span x-text="
                                    solicitud.numero_solicitud_cliente_fmt ||
                                    (() => { const d = new Date(); const ymd = d.toISOString().slice(0,10).replace(/-/g,''); return 'CLI-' + ymd + '-' + String(solicitud.id ?? solicitud.numero_solicitud_cliente ?? '').toString(); })()
                                "></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="solicitud.nombre_solicitud"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left text-gray-900 dark:text-gray-100 font-nunito"
                                x-text="solicitud.descripcion_problema"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-left font-nunito">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                    :class="{
                                        'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': solicitud.estado?.toLowerCase().includes('espera') || solicitud.estado?.toLowerCase().includes('pendiente'),
                                        'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300': solicitud.estado?.toLowerCase().includes('asignada') || solicitud.estado?.toLowerCase().includes('asignado'),
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': solicitud.estado?.toLowerCase().includes('proceso'),
                                        'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300': solicitud.estado?.toLowerCase().includes('rechazada') || solicitud.estado?.toLowerCase().includes('rechazado'),
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': solicitud.estado?.toLowerCase().includes('finalizada') || solicitud.estado?.toLowerCase().includes('finalizado') || solicitud.estado?.toLowerCase().includes('resuelta') || solicitud.estado?.toLowerCase().includes('cerrada'),
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300': !solicitud.estado || (!solicitud.estado?.toLowerCase().includes('espera') && !solicitud.estado?.toLowerCase().includes('pendiente') && !solicitud.estado?.toLowerCase().includes('asignada') && !solicitud.estado?.toLowerCase().includes('asignado') && !solicitud.estado?.toLowerCase().includes('proceso') && !solicitud.estado?.toLowerCase().includes('rechazada') && !solicitud.estado?.toLowerCase().includes('rechazado') && !solicitud.estado?.toLowerCase().includes('finalizada') && !solicitud.estado?.toLowerCase().includes('finalizado') && !solicitud.estado?.toLowerCase().includes('resuelta') && !solicitud.estado?.toLowerCase().includes('cerrada'))
                                    }" 
                                    x-text="solicitud.estado">
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="solicitudesFiltradas.length === 0">
                        <td colspan="4"
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center font-nunito">
                            No se encontraron solicitudes.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="modalNueva" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 flex items-center justify-center z-[9999] bg-black/50 backdrop-blur-sm"
            @click.self="modalNueva = false" @keydown.window.escape="modalNueva = false" style="margin: 0;">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-2xl mx-auto max-h-[90vh] overflow-y-auto"
                @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Nueva Solicitud de Soporte</h3>
                    <button @click="modalNueva = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="crearSolicitud" class="space-y-4">
                    <div>
                        <label for="nombre_solicitud"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre de la
                            Solicitud</label>
                        <input id="nombre_solicitud" x-model="nuevaSolicitud.nombre_solicitud" type="text"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200"
                            placeholder="Ingrese el nombre de la solicitud" required />
                    </div>
                    <div>
                        <label for="descripcion_problema"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción del
                            Problema</label>
                        <textarea id="descripcion_problema" x-model="nuevaSolicitud.descripcion_problema"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/60 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 dark:text-gray-200"
                            placeholder="Describa el problema" rows="4" required></textarea>
                    </div>
                    <div>
                        <label for="correo_contacto"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Correo de
                            contacto</label>
                        <input id="correo_contacto" x-model="nuevaSolicitud.correo_contacto" type="email"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm text-gray-600 dark:text-gray-400 cursor-not-allowed"
                            value="{{ $correoContacto ?: auth()->user()->correo_electronico }}" readonly />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Este correo se toma de tu perfil de contacto, lo usaremos para comunicarnos contigo sobre esta solicitud. O puedes ver tus notificaciones en cuanto tu solicitud sea aprobada.
                        </p>
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

        <div x-show="modalDetalle" x-cloak x-transition.opacity.duration.300ms
            class="fixed inset-0 flex items-center justify-center z-[9999] bg-black/70 dark:bg-black/80"
            @click.self="modalDetalle = false" @keydown.window.escape="modalDetalle = false" style="margin: 0;">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-6 w-11/12 max-w-2xl mx-auto max-h-[90vh] overflow-y-auto"
                @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Detalle de Solicitud</h3>
                    <button @click="modalDetalle = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <template x-if="solicitudActual">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nº Solicitud
                                ACF</label>
                            <p class="text-gray-900 dark:text-gray-100 font-semibold"
                                x-text="solicitudActual.numero_solicitud_acf_fmt || solicitudActual.numero_solicitud_acf">
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nº Solicitud
                                Cliente</label>
                            <p class="text-gray-900 dark:text-gray-100"
                                x-text="solicitudActual.numero_solicitud_cliente_fmt || solicitudActual.numero_solicitud_cliente">
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Descripción del
                                Problema</label>
                            <p class="text-gray-900 dark:text-gray-100" x-text="solicitudActual.descripcion_problema">
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Estado</label>
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full mt-1" :class="{
                                        'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300': solicitudActual.estado === 'Pendiente',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': solicitudActual.estado === 'En Proceso',
                                        'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': solicitudActual.estado === 'Resuelta',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300': solicitudActual.estado === 'Cerrada'
                                    }" x-text="solicitudActual.estado">
                                </span>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400">Contacto</label>
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
            solicitudes: [],
            filtros: {
                search: '',
                estado: ''
            },
            modalNueva: false,
            modalDetalle: false,
            solicitudActual: null,
            nuevaSolicitud: {
                nombre_solicitud: '',
                descripcion_problema: '',
                correo_contacto: '{{ $correoContacto ?: auth()->user()->correo_electronico }}'
            },

            get resumen() {
                const norm = (v) => (v ?? '').toString().trim().toLowerCase();
                const enEspera = this.solicitudes.filter(s => ['pendiente','en espera','espera'].includes(norm(s.estado))).length;
                const asignadas = this.solicitudes.filter(s => ['asignada','asignadas','asignado','asignados'].includes(norm(s.estado))).length;
                const enProceso = this.solicitudes.filter(s => ['en proceso','proceso'].includes(norm(s.estado))).length;
                const rechazadas = this.solicitudes.filter(s => ['rechazada','rechazadas','rechazado','rechazados'].includes(norm(s.estado))).length;
                const finalizadas = this.solicitudes.filter(s => ['finalizada','finalizadas','finalizado','finalizados','resuelta','resueltas','resuelto','resueltos','cerrada','cerradas','cerrado','cerrados'].includes(norm(s.estado))).length;
                return {
                    total: this.solicitudes.length,
                    enEspera,
                    asignadas,
                    enProceso,
                    rechazadas,
                    finalizadas
                };
            },

            get solicitudesFiltradas() {
                const sTerm = (this.filtros.search || '').toString().toLowerCase();
                const norm = (v) => (v ?? '').toString().toLowerCase();
                return this.solicitudes.filter(solicitud => {
                    const hay = [
                        norm(solicitud.descripcion_problema),
                        norm(solicitud.numero_solicitud_acf_fmt ?? solicitud.numero_solicitud_acf),
                        norm(solicitud.numero_solicitud_cliente_fmt ?? solicitud
                            .numero_solicitud_cliente),
                        norm(solicitud.nombre_solicitud)
                    ].some(v => v.includes(sTerm));
                    
                    let matchEstado = true;
                    if (this.filtros.estado) {
                        const estadoSolicitud = norm(solicitud.estado);
                        switch(this.filtros.estado) {
                            case 'Pendiente': 
                                matchEstado = ['pendiente', 'en espera', 'espera'].some(e => estadoSolicitud.includes(e));
                                break;
                            case 'En Proceso': 
                                matchEstado = ['asignada', 'asignado', 'asignadas', 'asignados'].some(e => estadoSolicitud.includes(e));
                                break;
                            case 'Resuelta': 
                                matchEstado = ['en proceso', 'proceso'].some(e => estadoSolicitud.includes(e));
                                break;
                            case 'Rechazada': 
                                matchEstado = ['rechazada', 'rechazado', 'rechazadas', 'rechazados'].some(e => estadoSolicitud.includes(e));
                                break;
                            case 'Cerrada':
                                matchEstado = ['finalizada', 'finalizado', 'finalizadas', 'finalizados', 'resuelta', 'resuelto', 'resueltas', 'resueltos', 'cerrada', 'cerrado', 'cerradas', 'cerrados'].some(e => estadoSolicitud.includes(e));
                                break;
                            default:
                                matchEstado = true;
                        }
                    }
                    
                    return (!sTerm || hay) && matchEstado;
                });
            },

            async init() {
                try {
                    const res = await fetch('/cliente/solicitudes-data', {
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    this.solicitudes = json.data || [];
                } catch (e) {
                    console.error(e);
                }
            },

            verDetalle(solicitud) {
                this.solicitudActual = solicitud;
                this.modalDetalle = true;
            },

            async crearSolicitud() {
                try {
                    const res = await fetch('/cliente/solicitudes', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(this.nuevaSolicitud)
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success) throw new Error(json.message || 'Error');
                    this.solicitudes.unshift(json.data);
                    window.showToast?.('Solicitud creada y ticket generado', 'success');
                    this.modalNueva = false;
                    this.nuevaSolicitud = {
                        nombre_solicitud: '',
                        descripcion_problema: '',
                        correo_contacto: '{{ $correoContacto ?: auth()->user()->correo_electronico }}'
                    };
                } catch (e) {
                    console.error(e);
                    window.showToast?.(e.message || 'No se pudo crear la solicitud', 'error');
                }
            }
        };
    };
}
</script>
@endsection