@extends('layouts.reporte')

@section('title', 'Reporte de Productos')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="Productos" :logoSize="96" />

            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Productos</h2>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-blue-600">{{ $total }}</div>
                    <div class="text-sm text-gray-600">Total Productos</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($totalValor, 2) }}</div>
                    <div class="text-sm text-gray-600">Valor Total (L.)</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border">
                    <div class="text-2xl font-bold text-yellow-600">{{ number_format($promedioPrecio, 2) }}</div>
                    <div class="text-sm text-gray-600">Precio Promedio (L.)</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">SKU</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Tipo Producto</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Precio Venta</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Stock Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $producto->id_producto_pk }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $producto->sku }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $producto->nombre_producto }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ $producto->tipoProducto ? $producto->tipoProducto->nombre_tipo_producto : 'N/A' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ number_format($producto->precio_venta, 2) }} L.
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $producto->stock_minimo }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="border border-gray-300 py-4 px-3 text-center text-gray-500">
                                No se encontraron productos
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