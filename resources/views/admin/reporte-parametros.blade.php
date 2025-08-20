@extends('layouts.reporte')

@section('title', 'Reporte de Parámetros')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <!-- Header del reporte -->
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="PARÁMETROS" :logoSize="96" />
            
            <!-- Título del reporte -->
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Parámetros del Sistema</h2>
            
            <!-- Tabla de datos -->
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Parámetro</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Valor</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Creado por</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Creación</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Modificado por</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Modificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($parametros ?? []) as $p)
                            @if(is_object($p))
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->parametro }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->valor }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->creado_por ?? '-' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->fecha_creacion ?? '-' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->modificado_por ?? '-' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->fecha_modificacion ?? '-' }}</td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="border border-gray-300 py-4 px-3 text-center text-gray-500">Sin datos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Resumen simple -->
            <div class="mt-6 p-4 bg-gray-50 rounded text-center text-sm">
                <span class="nunito-bold text-gray-700">Total parámetros: </span>
                <span class="nunito-regular">{{ $total ?? (is_countable($parametros ?? []) ? count($parametros ?? []) : 0) }}</span>
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
