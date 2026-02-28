@extends('layouts.reporte')

@section('title', 'Reporte de Usuarios')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="Usuarios" :logoSize="96" />
            
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Usuarios</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Nombre</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Usuario</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Correo</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Rol</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Estado</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Fecha Creación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $u)
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $u->id_usuario_pk }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $u->nombre_usuario }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $u->usuario }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $u->correo_electronico }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $u->rol->rol ?? '—' }}</td>
                                <td class="border border-gray-300 py-2 px-3 text-center">
                                    @if($u->estado_usuario==='ACTIVO')
                                        <span class="text-green-700 nunito-bold">Activo</span>
                                    @elseif($u->estado_usuario==='BLOQUEADO')
                                        <span class="text-yellow-700 nunito-bold">Bloqueado</span>
                                    @else
                                        <span class="text-red-700 nunito-bold">Inactivo</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($u->fecha_creacion)</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="border border-gray-300 py-4 px-3 text-center text-gray-500">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded">
                <div class="flex justify-center gap-8 text-sm">
                    <div class="text-center">
                        <span class="nunito-bold text-gray-700">Total: </span>
                        <span class="nunito-regular">{{ $total }} usuarios</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-green-700">Activos: </span>
                        <span class="nunito-regular">{{ $activos }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-red-700">Inactivos: </span>
                        <span class="nunito-regular">{{ $inactivos }}</span>
                    </div>
                    <div class="text-center">
                        <span class="nunito-bold text-yellow-700">Bloqueados: </span>
                        <span class="nunito-regular">{{ $bloqueados }}</span>
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
