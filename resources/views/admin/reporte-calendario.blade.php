@extends('layouts.reporte')

@section('title', 'Reporte de Calendario')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <!-- Header del reporte -->
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="CALENDARIO" :logoSize="96" />
            <!-- Título del reporte -->
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Eventos del Calendario</h2>
            <!-- Tabla de Calendario -->
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha/Hora</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Cliente</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Agencia</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Tipo Mantenimiento</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calendarios as $calendario)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $calendario->id_calendario_pk }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $calendario->descripcion_calendario }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ \Carbon\Carbon::parse($calendario->fecha)->format('d/m/Y H:i') }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $calendario->cliente ? $calendario->cliente->nombre : '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $calendario->agencia ? $calendario->agencia->nombre_agencia : '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $calendario->tipoMantenimiento ? $calendario->tipoMantenimiento->tipo_mantenimiento : '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $calendario->estado ? $calendario->estado->nombre : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="border border-gray-300 py-4 px-3 text-center nunito-regular text-gray-500">No hay eventos que coincidan con los filtros aplicados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Estadísticas -->
            <div class="mb-8">
                <h3 class="text-lg nunito-bold text-gray-800 mb-4">Estadísticas</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600">{{ $total }}</div>
                        <div class="text-sm text-blue-800 nunito-regular">Total de Eventos</div>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-yellow-600">{{ $pendientes }}</div>
                        <div class="text-sm text-yellow-800 nunito-regular">Pendientes</div>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-orange-600">{{ $enEjecucion }}</div>
                        <div class="text-sm text-orange-800 nunito-regular">En progreso</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600">{{ $completados }}</div>
                        <div class="text-sm text-green-800 nunito-regular">Completados</div>
                    </div>
                </div>
            </div>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">Agencia Central</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">Pendiente</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">5</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">Reunión Mensual de Evaluación</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">30/08/2025 16:00</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">Agencia Central</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">Programado</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Botones de acción -->
            <div class="mt-6 flex justify-center gap-4 no-print">
                <button onclick="window.print()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg nunito-bold transition">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <button onclick="window.close()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg nunito-bold transition">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
