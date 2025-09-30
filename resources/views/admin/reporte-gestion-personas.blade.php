@extends('layouts.reporte')

@section('title', 'Reporte de Gestión de Personas')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <!-- Header del reporte -->
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="GESTION DE PERSONAS" :logoSize="96" />
            <!-- Título del reporte -->
            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">Listado de Personas Registradas</h2>
            <!-- Tabla de Gestión de Personas (dinámica) -->
            <?php
                // $rows viene del controlador PersonaController@reporte
                // Fallback mínimo por si la ruta llama directamente la vista
                if(!isset($rows)) { $rows = []; }
            ?>
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ID</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Primer Nombre</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Segundo Nombre</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Primer Apellido</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Segundo Apellido</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">DNI</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Cargo</th>
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Género</th>
                            
                            <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($rows ?? []) as $p)
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->id_persona_pk ?? $p['id'] ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->primer_nombre ?? $p['primer_nombre'] ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->segundo_nombre ?? $p['segundo_nombre'] ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->primer_apellido ?? $p['primer_apellido'] ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->segundo_apellido ?? $p['segundo_apellido'] ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->dni ?? $p['dni'] ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->cargo ?? $p['cargo'] ?? '' }}</td>
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ ($p->genero->genero ?? null) ?? ($p['genero']['genero'] ?? '') }}</td>
                            
                            <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $p->id_usuario_fk ?? $p['id_usuario_fk'] ?? '' }}</td>
                        </tr>
                        @endforeach
                        @if(empty($rows))
                        <tr><td colspan="11" class="border border-gray-300 py-4 px-3 text-center text-gray-500 nunito-regular">Sin resultados</td></tr>
                        @endif
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
