@extends('layouts.reporte')

@section('title', 'Reporte de Bitácora')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="BITACORA" :logoSize="96" />
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Registro de Eventos del Sistema</h2>
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Evento</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Usuario</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Objeto</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Acción</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Creado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($items)
                        @forelse($items as $b)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($b['fecha_evento'] ?? $b->fecha_evento)</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $b['usuario']['usuario'] ?? $b->usuario->usuario ?? '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $b['objeto']['nombre_objeto'] ?? $b->objeto->nombre_objeto ?? '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $b['accion'] ?? $b->accion }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $b['descripcion'] ?? $b->descripcion ?? '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $b['creado_por'] ?? $b->creado_por ?? ($b['usuario']['usuario'] ?? $b->usuario->usuario ?? '-') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="border border-gray-300 py-2 px-3 text-center text-gray-500 nunito-regular">Sin datos</td>
                        </tr>
                        @endforelse
                        @else
                        <tr>
                            <td colspan="6" class="border border-gray-300 py-2 px-3 text-center text-gray-500 nunito-regular">Sin datos</td>
                        </tr>
                        @endisset
                    </tbody>
                </table>
            </div>
            <div class="report-print-controls no-print">
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