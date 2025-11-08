@extends('layouts.reporte')

@section('title', 'Reporte de Empresas')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="EMPRESAS" :logoSize="96" />

            <h2 class="text-xl nunito-bold text-gray-800 mb-2 text-center">Listado de Empresas</h2>
            @php
            $empresas = $empresas ?? collect();
            @endphp

            @php
            $ordenLabelMap = [
            'nombre_empresa' => 'nombre (comercial)',
            'estado_empresa' => 'estado',
            'fecha_registro' => 'fecha_registro (desc por defecto)'
            ];
            $ordenKey = (isset($ordenarPor) && is_string($ordenarPor) && $ordenarPor !== '') ? $ordenarPor : null;
            $ordenLabel = $ordenKey ? ($ordenLabelMap[$ordenKey] ?? str_replace('_',' ', $ordenKey)) : 'fecha_registro
            (desc)';
            @endphp
            <div class="text-xs text-gray-600 mb-4 space-y-1 nunito-regular">
                <div><span class="font-semibold">Búsqueda:</span> {{ $search ? e($search) : '—' }}</div>
                <div><span class="font-semibold">Estado:</span> {{ $estadoEmpresa ? ucfirst($estadoEmpresa) : 'Todos' }}
                </div>
                <div><span class="font-semibold">Ordenar por:</span> {{ $ordenLabel }}</div>
                <div><span class="font-semibold">Generado:</span>
                    {{ $fechaGeneracion ? e($fechaGeneracion) : now()->toDateTimeString() }}
                </div>
            </div>

            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre
                                Comercial</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Razón
                                Social</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">RTN</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción
                            </th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha
                                Registro</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Horario
                            </th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $e)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ $e->nombre_comercial ?? '—' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $e->razon_social ?? '—' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $e->rtn ?? '—' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ $e->descripcion_empresa ?? '' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ $e->fecha_registro ? \Illuminate\Support\Carbon::parse($e->fecha_registro)->format('Y-m-d') : '\u2014' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $e->horario_atencion ?? '' }}
                            </td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                {{ strtolower((string) $e->estado_cliente)==='activo' ? 'Activo' : 'Inactivo' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7"
                                class="border border-gray-300 py-4 px-3 text-center text-gray-500 italic nunito-regular">
                                Sin resultados</td>
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