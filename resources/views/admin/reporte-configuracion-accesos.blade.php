@extends('layouts.reporte')

@section('title', 'Reporte de Configuración de Accesos')

@section('content')
<div class="min-h-screen bg-white p-6 flex justify-center items-start">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <x-admin.reportes-header :fecha="$fecha" :modulo="$modulo" titulo="CONFIGURACION DE ACCESOS AL SISTEMA"
                :logoSize="96" />

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

            <h2 class="text-xl nunito-bold text-gray-800 mb-6 text-center">
                @if(($seccion ?? '')==='roles') Lista de Roles @elseif(($seccion ?? '')==='objetos') Objetos del Sistema
                @elseif(($seccion ?? '')==='asignar') Asignación de Roles a Usuarios @elseif(($seccion ??
                '')==='gestion') Gestión de Permisos @else Configuración de Accesos @endif
            </h2>


            <div class="grid grid-cols-1 gap-4 mb-6">
                <div
                    class="rounded-lg p-5 text-center flex flex-col items-center justify-center space-y-1 {{ $theme['bg'] }} border {{ $theme['border'] }}">

                    <div class="text-4xl nunito-bold {{ $theme['num'] }}">{{ $theme['value'] }}</div>
                    <div class="text-sm nunito-bold tracking-wide {{ $theme['label'] }}">{{ $theme['labelText'] }}</div>
                </div>
            </div>

            @if(($seccion ?? '')==='gestion')
            <div class="mb-6">
                <h3 class="text-lg nunito-bold text-gray-800 mb-3">Gestión de Roles y Permisos</h3>
                @if(isset($rol) && $rol)
                <p class="mb-2 text-sm">Rol seleccionado: <span class="nunito-bold">{{ $rol->rol }}</span></p>
                @else
                <p class="mb-2 text-sm text-gray-600">No se seleccionó un rol. Usa la pantalla para elegir uno y
                    re-generar.</p>
                @endif
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">Objeto
                                </th>
                                @foreach(($permColumns ?? []) as $c)
                                <th class="border border-gray-300 py-2 px-3 text-center nunito-bold text-gray-700">
                                    {{ $c['label'] }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>

                            @forelse(($matriz ?? collect()) as $row)
                            @if(!empty($row['is_header']))
                            <tr>
                                <td colspan="{{ $cols }}"
                                    class="bg-gray-50 border border-gray-300 py-2 px-3 text-gray-700 nunito-bold uppercase">
                                    {{ $row['title'] ?? '' }}
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $row['objeto'] }}</td>
                                @foreach(($permColumns ?? []) as $c)
                                <td class="border border-gray-300 py-2 px-3 text-center">
                                    {!! !empty($row[$c['field']]) ? '<span class="text-green-600">✔</span>' : '<span
                                        class="text-gray-400">—</span>' !!}
                                </td>
                                @endforeach
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Sin datos</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if(($seccion ?? '')==='roles')
            <div class="mb-6">
                <h3 class="text-lg nunito-bold text-gray-800 mb-3">Lista de Roles</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ROL
                                </th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">
                                    DESCRIPCIÓN</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">CREADO
                                    POR</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">FECHA
                                    DE CREACIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($roles ?? collect()) as $r)
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r->rol }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r->descripcion_rol }}
                                </td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $r->creado_por }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($r->fecha_creacion)
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-500">Sin resultados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if(($seccion ?? '')==='asignar')
            <div class="mb-6">
                <h3 class="text-lg nunito-bold text-gray-800 mb-3">Asignación de Roles a Usuarios</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">USUARIO
                                </th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">NOMBRE
                                </th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">ROL
                                    ASIGNADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($usuarios ?? collect()) as $u)
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $u->usuario }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $u->nombre_usuario }}
                                </td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ optional($u->rol)->rol }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-500">Sin resultados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if(($seccion ?? '')==='objetos')
            <div class="mb-6">
                <h3 class="text-lg nunito-bold text-gray-800 mb-3">Objetos del Sistema</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">NOMBRE
                                </th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">
                                    DESCRIPCIÓN</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">TIPO
                                </th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">CREADO
                                    POR</th>
                                <th class="border border-gray-300 py-2 px-3 text-left nunito-bold text-gray-700">FECHA
                                    CREACIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($objetos ?? collect()) as $o)
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $o->nombre_objeto }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $o->descripcion_objeto }}
                                </td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">
                                    {{ optional($o->tipoObjeto)->nombre_tipo_objeto }}
                                </td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">{{ $o->creado_por }}</td>
                                <td class="border border-gray-300 py-2 px-3 nunito-regular">@fecha($o->fecha_creacion)
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Sin resultados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-6">
                <h3 class="text-lg nunito-bold text-gray-800 mb-3">RESUMEN EJECUTIVO</h3>
                <div class="text-sm nunito-regular text-gray-700 space-y-3">
                    @if(($seccion ?? '')==='roles')
                    <p>Listado de roles: <strong>{{ $stats['rolesFiltered'] }}</strong> de {{ $stats['rolesTotal'] }}
                        @if(($q ?? null)) (filtro “{{ $q }}”) @endif @if(($sort ?? null)) — orden:
                        <strong>{{ $sort }}</strong> ({{ $direction ?? 'asc' }}) @endif.
                    </p>
                    @elseif(($seccion ?? '')==='objetos')
                    <p>Objetos: <strong>{{ $stats['objetosFiltered'] }}</strong> de {{ $stats['objetosTotal'] }} @if(($q
                        ?? null)) (filtro “{{ $q }}”) @endif @if(request()->filled('id_tipo_objetos_fk')) — tipo:
                        <strong>{{ request('id_tipo_objetos_fk') }}</strong> @endif @if(($sort ?? null)) — orden:
                        <strong>{{ $sort }}</strong> ({{ $direction ?? 'asc' }}) @endif.
                    </p>
                    @elseif(($seccion ?? '')==='asignar')
                    <p>Usuarios con rol: <strong>{{ $stats['usuariosConRolFiltered'] }}</strong> de
                        {{ $stats['usuariosConRolTotal'] }} @if(($q ?? null)) (filtro “{{ $q }}”) @endif
                        @if(request()->filled('id_rol_fk')) — rol: <strong>{{ request('id_rol_fk') }}</strong> @endif
                        @if(($sort ?? null)) — orden: <strong>{{ $sort }}</strong> ({{ $direction ?? 'asc' }}) @endif.
                    </p>
                    @elseif(($seccion ?? '')==='gestion')
                    <p>Matriz de permisos para el rol @if(isset($rol) && $rol) <strong>{{ $rol->rol }}</strong> @else
                        <em>no seleccionado</em> @endif.
                    </p>
                    @if(isset($rol) && $rol)
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <li>Objetos con permiso de Crear: <strong>{{ $stats['permResumen']['insercion'] }}</strong></li>
                        <li>Objetos con permiso de Ver: <strong>{{ $stats['permResumen']['consultar'] }}</strong></li>
                        <li>Objetos con permiso de Editar: <strong>{{ $stats['permResumen']['actualizar'] }}</strong>
                        </li>
                        <li>Objetos con permiso de Eliminar: <strong>{{ $stats['permResumen']['eliminacion'] }}</strong>
                        </li>
                        <li>Objetos con todos los permisos: <strong>{{ $stats['permResumen']['todo'] }}</strong></li>
                        <li>Objetos sin ningún permiso: <strong>{{ $stats['permResumen']['ninguno'] }}</strong></li>
                    </ul>
                    @endif
                    @else
                    <p>Resumen del módulo de Configuración de Accesos.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection