@extends('layouts.reporte')

@section('title', 'Reporte Financiero de Proyecto')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="Proyecto Financiero" :logoSize="96" />

            <h2 class="text-xl nunito-bold text-gray-800 mb-2 text-center">{{ $proyecto->nombre_proyecto }}</h2>
            <p class="text-sm text-gray-600 mb-6 text-center">
                @if($proyecto->ordenServicio)
                    Orden de Servicio: {{ $proyecto->ordenServicio->numero_orden_servicio }}
                @endif
            </p>

            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-green-50 p-6 rounded-lg border text-center">
                    <div class="text-3xl font-bold text-green-600">{{ number_format($totalIngresos, 2) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Ingresos</div>
                    <div class="text-xs text-gray-500 mt-1">Total recibido</div>
                </div>
                <div class="bg-red-50 p-6 rounded-lg border text-center">
                    <div class="text-3xl font-bold text-red-600">{{ number_format($totalGastos, 2) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Gastos</div>
                    <div class="text-xs text-gray-500 mt-1">Total gastado</div>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg border text-center">
                    <div class="text-3xl font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($balance, 2) }}
                    </div>
                    <div class="text-sm text-gray-600 mt-1">Balance</div>
                    <div class="text-xs text-gray-500 mt-1">Saldo neto</div>
                </div>
            </div>

            <h3 class="text-lg nunito-bold text-gray-800 mb-4 border-b border-gray-300 pb-2">Historial de Movimientos</h3>

            <div class="space-y-3">
                @forelse($movimientos as $movimiento)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <!-- Icono según tipo -->
                            @if($movimiento['tipo'] === 'ingreso')
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-arrow-up text-green-600"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-arrow-down text-red-600"></i>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">{{ $movimiento['nombre'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $movimiento['categoria'] }}</p>
                                @if($movimiento['descripcion'])
                                    <p class="text-xs text-gray-500 mt-1">{{ $movimiento['descripcion'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="font-bold text-lg {{ $movimiento['tipo'] === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                            L {{ number_format($movimiento['monto'], 2) }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>No se encontraron movimientos financieros para este proyecto.</p>
                </div>
                @endforelse
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

        .hover\:bg-gray-50:hover {
            background-color: transparent !important;
        }
    }

    @page {
        size: A4;
        margin: 1cm;
    }
</style>
@endsection