@extends('layouts.reporte')

@section('title', 'Reporte de Solicitudes')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <!-- Header del reporte -->
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="SOLICITUDES" :logoSize="96" />

            <h2 class="text-xl nunito-bold text-gray-800 mb-2 text-center">Listado de Solicitudes</h2>
            @php
                $ordenarPor = isset($ordenarPor) && (is_string($ordenarPor) || is_numeric($ordenarPor)) ? (string) $ordenarPor : '';
                $search = isset($search) ? (string) $search : '';
                $estadoSolicitud = isset($estadoSolicitud) ? (string) $estadoSolicitud : '';
            @endphp
            @if(!empty($search) || !empty($estadoSolicitud) || !empty($ordenarPor))
                <div class="text-xs text-gray-600 nunito-regular mb-6 text-center">
                    @if(!empty($search))<span class="mr-3">Buscar: <span class="nunito-bold">{{ $search }}</span></span>@endif
                    @if(!empty($estadoSolicitud))<span class="mr-3">Estado: <span class="nunito-bold">{{ $estadoSolicitud }}</span></span>@endif
                    @if(!empty($ordenarPor))
                        @php
                            $labels = [
                                'estado_solicitud' => 'Estado',
                                'cliente' => 'Cliente',
                                'solicitud_acf' => 'Solicitud ACF',
                                'solicitud_cliente' => 'Solicitud Cliente',
                            ];
                            $ordenLbl = $labels[$ordenarPor] ?? $ordenarPor;
                        @endphp
                        <span class="mr-3">Ordenado por: <span class="nunito-bold">{{ $ordenLbl }}</span></span>
                    @endif
                </div>
            @endif
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Cliente</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">N° Solicitud ACF</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">N° Solicitud Cliente</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Contacto</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rows = collect($solicitudes ?? []); @endphp
                        @forelse($rows as $row)
                            @php
                                // $row puede ser stdClass; convertir a array para acceso seguro
                                $r = is_array($row) ? $row : (is_object($row) ? get_object_vars($row) : []);
                            @endphp
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r['cliente_nombre'] ?? '—' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r['numero_solicitud_acf'] ?? '—' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r['numero_solicitud_cliente'] ?? '—' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r['descripcion_problema'] ?? '—' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r['valor_contacto'] ?? '—' }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r['estado_nombre'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border border-gray-300 py-4 px-3 text-center text-gray-600 nunito-regular">No se encontraron solicitudes con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Sección Contactos eliminada del reporte de Solicitudes por no requerirse -->

            <!-- Botones de acción (sticky on-screen, hidden in print) -->
            <div class="report-print-controls no-print">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg nunito-bold transition">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg nunito-bold transition">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
