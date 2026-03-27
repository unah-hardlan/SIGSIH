@extends('layouts.reporte')

@section('title', 'Reporte de Proyectos')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="Proyectos" :logoSize="96" />

            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Proyectos</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Inicio</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Estimada Fin</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Fin Real</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Orden de Servicio</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proyectos as $p)
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->id_proyecto_pk }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->nombre_proyecto }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($p->fecha_inicio_proyecto)</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($p->fecha_estimada_fin_proyecto)</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($p->fecha_finalizacion_proyecto)</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->ordenServicio->numero_orden_servicio ?? '—' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->descripcion_proyecto }}</td>
                                <td class="border border-gray-300 py-2 px-3 text-center">
                                    @php
                                        $estadoRaw = $p->estadoProyecto->codigo
                                            ?? $p->estadoProyecto->nombre
                                            ?? $p->estadoProyecto->nombre_estado
                                            ?? '';
                                        $estadoCode = strtoupper(trim((string) $estadoRaw));
                                    @endphp
                                    @if(in_array($estadoCode, ['AC', 'ACTIVO'], true))
                                        <span class="text-green-700 nunito-bold">Activo</span>
                                    @elseif(in_array($estadoCode, ['FIN', 'FINALIZADO'], true))
                                        <span class="text-blue-700 nunito-bold">Finalizado</span>
                                    @else
                                        <span class="text-red-700 nunito-bold">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="border border-gray-300 py-4 px-3 text-center text-gray-500">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded">
                <div class="flex justify-center gap-8 text-sm">
                    <div class="text-center">
                        <span class="nunito-bold text-gray-700">Total: </span>
                        <span class="nunito-regular">{{ $total }} proyectos</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-green-700">Activos: </span>
                        <span class="nunito-regular">{{ $activos }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-blue-700">Finalizados: </span>
                        <span class="nunito-regular">{{ $finalizados }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-red-700">Inactivos: </span>
                        <span class="nunito-regular">{{ $inactivos }}</span>
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