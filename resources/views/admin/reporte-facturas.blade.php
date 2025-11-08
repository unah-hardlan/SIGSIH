@extends('layouts.reporte')

@section('title', 'Reporte de Facturas')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="FACTURAS" :logoSize="96" />
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Facturas</h2>
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">No. Factura</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Cliente</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Subtotal</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ISV (15%)</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Total</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $factura->numero }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $factura->cliente->nombre ?? 'Sin cliente' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">L. {{ number_format($factura->subtotal, 2) }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">L. {{ number_format($factura->impuesto, 2) }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">L. {{ number_format($factura->total, 2) }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $factura->estadoFactura->nombre ?? 'Sin estado' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border border-gray-300 py-8 text-center text-gray-500 nunito-regular">No hay facturas para mostrar</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mb-8">
                <h3 class="text-lg nunito-bold text-gray-800 mb-4 text-center">Detalle de Servicios Facturados</h3>
                
                @forelse($facturas as $factura)
                    <div class="mb-6 border rounded-lg p-4">
                        <h4 class="nunito-bold text-gray-700 mb-3">{{ $factura->numero }} - {{ $factura->cliente->nombre ?? 'Sin cliente' }}</h4>
                        <table class="w-full border-collapse border border-gray-300 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border border-gray-300 py-1 px-2 text-left nunito-bold">Descripción</th>
                                    <th class="border border-gray-300 py-1 px-2 text-left nunito-bold">Cantidad</th>
                                    <th class="border border-gray-300 py-1 px-2 text-left nunito-bold">Precio Unit.</th>
                                    <th class="border border-gray-300 py-1 px-2 text-left nunito-bold">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($factura->detalles as $detalle)
                                    <tr>
                                        <td class="border border-gray-300 py-1 px-2 nunito-regular">{{ $detalle->descripcion }}</td>
                                        <td class="border border-gray-300 py-1 px-2 nunito-regular">{{ $detalle->cantidad }}</td>
                                        <td class="border border-gray-300 py-1 px-2 nunito-regular">L. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="border border-gray-300 py-1 px-2 nunito-regular">L. {{ number_format($detalle->total_linea, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="border border-gray-300 py-2 px-2 text-center text-gray-500 nunito-regular">Sin detalles</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @empty
                    <div class="mb-6 border rounded-lg p-4 text-center text-gray-500 nunito-regular">
                        No hay facturas con detalles para mostrar
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded">
                <h3 class="text-lg nunito-bold text-gray-800 mb-3 text-center">Resumen de Facturación</h3>
                <div class="flex justify-center gap-8 text-sm">
                    <div class="text-center">
                        <span class="nunito-bold text-gray-700">Total Facturas: </span>
                        <span class="nunito-regular">{{ $totalFacturas }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-green-700">Pagadas: </span>
                        <span class="nunito-regular">L. {{ number_format($pagadas, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-yellow-700">Pendientes: </span>
                        <span class="nunito-regular">L. {{ number_format($pendientes, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-red-700">Anuladas: </span>
                        <span class="nunito-regular">L. {{ number_format($anuladas, 2) }}</span>
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