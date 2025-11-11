@extends('layouts.reporte')

@section('title', 'Reporte de Proyecto BAC')

@push('styles')
<style>
    .glass-noise {
        position: relative;
        overflow: hidden;
    }

    .glass-noise::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('https://www.transparenttextures.com/patterns/noise-lines.png');
        opacity: 0.03;
        pointer-events: none;
        z-index: 0;
    }

    .glass-noise>* {
        position: relative;
        z-index: 1;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body,
        .page-container {
            background: #fff !important;
        }

        .report-card {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
            background: #fff !important;
            backdrop-filter: none !important;
        }

        .glass-noise::before {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-100 p-4 sm:p-6 lg:p-8 print:bg-white page-container">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white/60 backdrop-blur-xl rounded-lg shadow-lg border border-black/5 p-6 sm:p-8 lg:p-10 report-card glass-noise">

            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="Proyecto BAC" :logoSize="96" />

            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 my-12">
                @foreach ($cards ?? [] as $card)
                <article class="relative bg-white bg-pattern-dots rounded-lg shadow-sm border-l-4 {{ $card['borderColor'] }} p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ $card['title'] }}</h3>
                            <div class="w-10 h-10 flex items-center justify-center rounded-full {{ $card['bgColor'] }} {{ $card['textColor'] }}">
                                <i class="fas {{ $card['icon'] }}"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-3xl font-bold text-gray-800">{{ $card['value'] }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">{{ $card['sub'] }}</p>
                </article>
                @endforeach
            </section>

            <div class="space-y-10">
                <section class="avoid-break">
                    <div class="border-b border-gray-200 pb-4 mb-5">
                        <h2 class="text-xl font-semibold text-gray-800">Detalle de Ingresos</h2>
                    </div>
                    <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-emerald-500 to-emerald-600">
                                <tr class="text-left text-xs font-medium text-white uppercase tracking-wider">
                                    <th scope="col" class="px-6 py-4">Nombre</th>
                                    <th scope="col" class="px-6 py-4">Fecha</th>
                                    <th scope="col" class="px-6 py-4 text-right">Monto</th>
                                    <th scope="col" class="px-6 py-4">Descripción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white/80 backdrop-blur-sm text-sm text-gray-700 divide-y divide-gray-100">
                                <tr class="hover:bg-emerald-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Pago inicial</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">2025-07-20</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-emerald-600">L. 15,000.00</td>
                                    <td class="px-6 py-4 text-gray-500">Primer pago del Proyecto Alpha</td>
                                </tr>
                                <tr class="hover:bg-emerald-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Segundo pago</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">2025-07-25</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-emerald-600">L. 14,230.00</td>
                                    <td class="px-6 py-4 text-gray-500">Segundo pago del Proyecto Beta</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="avoid-break">
                    <div class="border-b border-gray-200 pb-4 mb-5">
                        <h2 class="text-xl font-semibold text-gray-800">Detalle de Gastos</h2>
                    </div>
                    <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-rose-500 to-rose-600">
                                <tr class="text-left text-xs font-medium text-white uppercase tracking-wider">
                                    <th scope="col" class="px-6 py-4">Nombre</th>
                                    <th scope="col" class="px-6 py-4">Fecha</th>
                                    <th scope="col" class="px-6 py-4 text-right">Monto</th>
                                    <th scope="col" class="px-6 py-4">Descripción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white/80 backdrop-blur-sm text-sm text-gray-700 divide-y divide-gray-100">
                                <tr class="hover:bg-rose-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Compra de software</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">2025-07-22</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-rose-600">L. 5,500.00</td>
                                    <td class="px-6 py-4 text-gray-500">Licencias de software de desarrollo</td>
                                </tr>
                                <tr class="hover:bg-rose-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Alquiler de oficina</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">2025-07-26</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-rose-600">L. 10,483.00</td>
                                    <td class="px-6 py-4 text-gray-500">Pago de alquiler mensual</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-6 avoid-break mt-12 pt-8 border-t border-black/5">
                <div class="relative glass-noise bg-white/60 backdrop-blur-xl border border-black/5 rounded-lg p-4">
                    <h3 class="text-center text-sm font-medium text-gray-600 mb-4">Desglose de Ingresos</h3>
                    <canvas id="incomeChart" height="200"></canvas>
                </div>
                <div class="relative glass-noise bg-white/60 backdrop-blur-xl border border-black/5 rounded-lg p-4">
                    <h3 class="text-center text-sm font-medium text-gray-600 mb-4">Desglose de Gastos</h3>
                    <canvas id="expenseChart" height="200"></canvas>
                </div>
            </section>

            <div class="report-print-controls no-print">
                <button onclick="window.print()" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-black text-white px-6 py-2 rounded-md font-semibold text-sm transition">
                    <i class="fas fa-print"></i>Imprimir
                </button>
                <button onclick="window.close()" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md font-semibold text-sm transition">
                    <i class="fas fa-times"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#374151';

        const incomeData = {
            labels: ["Pago inicial", "Segundo pago"],
            datasets: [{
                data: [15000, 14230],
                backgroundColor: "rgba(16, 185, 129, 0.7)",
                borderColor: "rgb(16, 185, 129)",
                borderWidth: 2,
                borderRadius: 4,
            }],
        };
        const expenseData = {
            labels: ["Compra de software", "Alquiler de oficina"],
            datasets: [{
                data: [5500, 10483],
                backgroundColor: "rgba(225, 29, 72, 0.7)",
                borderColor: "rgb(225, 29, 72)",
                borderWidth: 2,
                borderRadius: 4,
            }],
        };
        const options = {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
        };

        new Chart(document.getElementById("incomeChart"), {
            type: "bar",
            data: incomeData,
            options
        });
        new Chart(document.getElementById("expenseChart"), {
            type: "bar",
            data: expenseData,
            options
        });
    });
</script>
@endpush