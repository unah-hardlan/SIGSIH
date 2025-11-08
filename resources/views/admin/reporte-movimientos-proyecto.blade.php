@extends('layouts.reporte')

@section('title', 'Reporte de Movimientos de Proyecto')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="Movimientos de Proyecto" :logoSize="96" />
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Movimientos Financieros</h2>
            <div class="mb-6 p-4 bg-gray-50 rounded">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="text-center">
                        <span class="nunito-bold text-gray-700">Total Ingresos: </span>
                        <span class="nunito-regular">{{ $totalIngresos }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-green-700">Suma Ingresos: </span>
                        <span class="nunito-regular">L. {{ number_format($sumaIngresos, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-gray-700">Total Gastos: </span>
                        <span class="nunito-regular">{{ $totalGastos }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-red-700">Suma Gastos: </span>
                        <span class="nunito-regular">L. {{ number_format($sumaGastos, 2) }}</span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <span class="nunito-bold text-lg {{ $balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        Balance: L. {{ number_format($balance, 2) }}
                    </span>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg nunito-bold text-green-700 mb-4 border-b border-green-200 pb-2">INGRESOS</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300 mb-4">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Proyecto</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Categoría</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Monto</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ingresos as $i)
                                <tr>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $i->id_ingresos_pk }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $i->proyecto->nombre_proyecto ?? '—' }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $i->nombre_ingreso }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $i->categoria->nombre_categoria ?? '—' }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($i->fecha_ingreso)</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular text-green-700">L. {{ number_format($i->monto_ingreso, 2) }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $i->descripcion_ingreso }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="border border-gray-300 py-4 px-3 text-center text-gray-500">Sin ingresos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg nunito-bold text-red-700 mb-4 border-b border-red-200 pb-2">GASTOS</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Proyecto</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Categoría</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Monto</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gastos as $g)
                                <tr>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $g->id_gasto_pk }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $g->proyecto->nombre_proyecto ?? '—' }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $g->nombre_gasto }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $g->categoria->nombre_categoria ?? '—' }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($g->fecha_gasto)</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular text-red-700">L. {{ number_format($g->monto_gasto, 2) }}</td>
                                    <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $g->descripcion_gasto }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="border border-gray-300 py-4 px-3 text-center text-gray-500">Sin gastos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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