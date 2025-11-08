@extends('layouts.reporte')

@section('title', 'Reporte de Agencias')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="AGENCIAS" :logoSize="96" />
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Agencias</h2>
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Horario</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Dirección</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Ciudad</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Departamento</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">País</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Clientes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agencias as $agencia)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->id_agencias_pk }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->nombre_agencia }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->horario_agencia }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->direccion ? ($agencia->direccion->direccion_completa ?: trim(($agencia->direccion->calle ?? '') . ' ' . ($agencia->direccion->numero ?? '') . ' ' . ($agencia->direccion->colonia ?? ''))) : '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->direccion->ciudad->nombre_ciudad ?? '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->direccion->ciudad->departamento->nombre_departamento ?? '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->direccion->ciudad->departamento->pais->nombre_pais ?? '-' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $agencia->clientes && $agencia->clientes->count() ? $agencia->clientes->take(3)->pluck('nombre')->join(', ') . ($agencia->clientes->count() > 3 ? ' +' . ($agencia->clientes->count() - 3) : '') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="border border-gray-300 py-4 px-3 text-center nunito-regular text-gray-500">No hay agencias que coincidan con los filtros aplicados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mb-8">
                <h3 class="text-lg nunito-bold text-gray-800 mb-4">Estadísticas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600">{{ $total }}</div>
                        <div class="text-sm text-blue-800 nunito-regular">Total de Agencias</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600">{{ $agencias->where('clientes', '!=', null)->where('clientes', '!=', [])->count() }}</div>
                        <div class="text-sm text-green-800 nunito-regular">Agencias con Clientes</div>
                    </div>
                </div>
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
