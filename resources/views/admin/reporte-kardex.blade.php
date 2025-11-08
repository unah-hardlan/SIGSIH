@extends('layouts.reporte')

@section('title', 'Reporte de Kardex')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="Kardex" :logoSize="96" />
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Movimientos de Kardex</h2>
            <div class="grid grid-cols-1 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-blue-600">{{ $total }}</div>
                    <div class="text-sm text-gray-600">Total Movimientos</div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Producto</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Tipo Movimiento</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Cantidad</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Movimiento</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Origen</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kardex as $movimiento)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $movimiento->id_kardex_pk }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ $movimiento->producto ? $movimiento->producto->nombre_producto : 'N/A' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($movimiento->tipoMovimiento && $movimiento->tipoMovimiento->nombre_tipo_movimiento == 'Entrada')
                                        bg-green-100 text-green-800
                                    @elseif($movimiento->tipoMovimiento && $movimiento->tipoMovimiento->nombre_tipo_movimiento == 'Salida')
                                        bg-red-100 text-red-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $movimiento->tipoMovimiento ? $movimiento->tipoMovimiento->nombre_tipo_movimiento : 'N/A' }}
                                </span>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ number_format($movimiento->cantidad, 2) }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ $movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ $movimiento->origen ? $movimiento->origen->nombre_origen : 'N/A' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $movimiento->motivo ?: 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="border border-gray-300 py-4 px-3 text-center text-gray-500">
                                No se encontraron movimientos de kardex
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
<style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white !important;
        }

        .shadow-sm {
            box-shadow: none !important;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }

    @page {
        size: landscape;
        margin: 1cm;
    }
</style>
@endsection
