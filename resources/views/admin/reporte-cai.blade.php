@extends('layouts.reporte')

@section('title', 'Reporte de CAI')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="CAI" :logoSize="96" />
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de CAI Registrados</h2>

            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-blue-600">{{ $total }}</div>
                    <div class="text-sm text-gray-600">Total CAI</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-green-600">{{ $activos }}</div>
                    <div class="text-sm text-gray-600">Activos</div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-orange-600">{{ $agotados }}</div>
                    <div class="text-sm text-gray-600">Agotados</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-gray-600">{{ $cerrados }}</div>
                    <div class="text-sm text-gray-600">Cerrados</div>
                </div>
            </div>

            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">CAI</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Rango Inicial</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Rango Final</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Vencimiento</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cais as $cai)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $cai->id_cai_pk }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $cai->codigo }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $cai->rango_inicio }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $cai->rango_fin }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $cai->fecha_limite ? \Carbon\Carbon::parse($cai->fecha_limite)->format('d/m/Y') : 'N/A' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                @if($cai->estadoCai)
                                <span class="px-2 py-1 rounded text-xs font-medium
                                        @if(strtolower($cai->estadoCai->codigo) === 'act') bg-green-100 text-green-800
                                        @elseif(strtolower($cai->estadoCai->codigo) === 'cai-agt') bg-orange-100 text-orange-800
                                        @elseif(strtolower($cai->estadoCai->codigo) === 'cai-cer') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                    {{ $cai->estadoCai->nombre }}
                                </span>
                                @else
                                N/A
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="border border-gray-300 py-4 px-3 text-center text-gray-500">
                                No se encontraron registros de CAI
                            </td>
                        </tr>
                        @endforelse
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