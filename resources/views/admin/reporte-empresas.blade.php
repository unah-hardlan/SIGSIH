@extends('layouts.reporte')

@section('title', 'Reporte de Empresas')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <!-- Header del reporte -->
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="EMPRESAS" :logoSize="96" />

            <h2 class="text-xl nunito-bold text-gray-800 mb-2 text-center">Listado de Empresas Cliente</h2>
            @php
            // Normalizar colección si no se pasa
            $empresas = $empresas ?? collect();
            @endphp

            <!-- Resumen de filtros -->
            <div class="text-xs text-gray-600 mb-4 space-y-1 nunito-regular">
                <div><span class="font-semibold">Búsqueda:</span> {{ $search ? e($search) : '—' }}</div>
                <div><span class="font-semibold">Estado:</span> {{ $estadoEmpresa ? ucfirst($estadoEmpresa) : 'Todos' }}</div>
                <div><span class="font-semibold">Ordenar por:</span> {{ $ordenarPor ? str_replace('_',' ', $ordenarPor) : 'fecha_registro (desc)' }}</div>
                <div><span class="font-semibold">Generado:</span> {{ $fechaGeneracion ? e($fechaGeneracion) : now()->toDateTimeString() }}</div>
            </div>

            <!-- Tabla de Empresas Cliente -->
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre Empresa</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Registro</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Ciudad</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Departamento</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">País</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Oficina</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $e)
                        @php
                        $direc = $e->direccion;
                        $ciudad = $direc->ciudad ?? null;
                        $departamento = $ciudad->departamento ?? null;
                        $pais = $departamento->pais ?? null;
                        $nombre = $e->nombreEmpresa->nombre_empresa ?? '—';
                        $descripcion = $e->nombreEmpresa->descripcion_empresa ?? '';
                        @endphp
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $nombre }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $descripcion }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ optional($e->fecha_registro)->format('Y-m-d') }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $ciudad->nombre_ciudad ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $departamento->nombre_departamento ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $pais->nombre_pais ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $e->oficina->nombre_oficina ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ strtolower($e->estado_empresa)==='activo' ? 'Activo':'Inactivo' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="border border-gray-300 py-4 px-3 text-center text-gray-500 italic nunito-regular">Sin resultados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <h2 class="text-xl nunito-bold text-gray-800 mb-2 text-center">Empresas Registradas</h2>
            @php $nombresEmpresa = $nombresEmpresa ?? collect(); @endphp
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre Empresa</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nombresEmpresa as $ne)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $ne->nombre_empresa }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $ne->descripcion_empresa }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="border border-gray-300 py-4 px-3 text-center text-gray-500 italic nunito-regular">Sin resultados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h2 class="text-xl nunito-bold text-gray-800 mb-2 text-center">Oficinas de Empresa</h2>
            @php $oficinasEmpresa = $oficinasEmpresa ?? collect(); @endphp
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre Oficina</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($oficinasEmpresa as $of)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $of->nombre_oficina }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td class="border border-gray-300 py-4 px-3 text-center text-gray-500 italic nunito-regular">Sin resultados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Botones de acción -->
            <div class="mt-6 flex justify-center gap-4 no-print">
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