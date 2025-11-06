<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\Objeto;
use App\Models\Permiso;
use App\Models\Usuario;
use Illuminate\Support\Str;

class ConfiguracionAccesoReporteController extends Controller
{
    /**
     * Renderiza el reporte dinámico por sección del módulo Configuración de Acceso.
     */
    public function reporte(Request $request)
    {
        $seccion = $request->input('seccion'); // gestion | roles | objetos | asignar
        $fecha = $request->input('fecha', now()->format('d-M-Y'));
        $modulo = 'configuracion-acceso';

        // Parámetros comunes
        $q = $request->input('q');
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Datasets por sección
        $roles = collect();
        $objetos = collect();
        $usuarios = collect();
        $matriz = collect();
        // Alinear columnas con la UI (Ver, Crear, Editar, Eliminar)
        $permColumns = [
            ['field' => 'permiso_ver', 'label' => 'Ver'],
            ['field' => 'permiso_consultar', 'label' => 'Leer'],
            ['field' => 'permiso_insercion', 'label' => 'Crear'],
            ['field' => 'permiso_actualizar', 'label' => 'Editar'],
            ['field' => 'permiso_eliminacion', 'label' => 'Eliminar'],
        ];
        $rol = null;
        $stats = [
            'rolesFiltered' => 0,
            'rolesTotal' => Rol::count(),
            'objetosFiltered' => 0,
            'objetosTotal' => Objeto::count(),
            'usuariosConRolFiltered' => 0,
            'usuariosConRolTotal' => Usuario::whereNotNull('id_rol_fk')->count(),
            // Solo para gestión
            'permResumen' => [
                'insercion' => 0,
                'consultar' => 0,
                'actualizar' => 0,
                'eliminacion' => 0,
                'todo' => 0,
                'ninguno' => 0,
            ],
        ];

        if ($seccion === 'roles') {
            $sortable = ['rol' => 'rol', 'descripcion' => 'descripcion_rol', 'creado' => 'fecha_creacion'];
            $rolesQ = Rol::query();
            if ($q) {
                $rolesQ->where(function ($sub) use ($q) {
                    $sub->where('rol', 'like', "%$q%")
                        ->orWhere('descripcion_rol', 'like', "%$q%");
                });
            }
            if ($sort && isset($sortable[$sort])) {
                $rolesQ->orderBy($sortable[$sort], $direction);
            } else {
                $rolesQ->orderBy('id_rol_pk', 'asc');
            }
            $roles = $rolesQ->get();
            $stats['rolesFiltered'] = (clone $rolesQ)->count();
            $stats['objetosFiltered'] = $stats['objetosTotal'];
            $stats['usuariosConRolFiltered'] = $stats['usuariosConRolTotal'];
        } elseif ($seccion === 'objetos') {
            $sortable = [
                'id' => 'id_objetos_pk',
                'nombre' => 'nombre_objeto',
                'descripcion' => 'descripcion_objeto',
                'tipo' => 'id_tipo_objetos_fk',
                'creado' => 'fecha_creacion',
                'modificado' => 'fecha_modificacion',
            ];
            $objetosQ = Objeto::query()->with('tipoObjeto');
            if ($q) {
                $objetosQ->where(function ($sub) use ($q) {
                    $sub->where('nombre_objeto', 'like', "%$q%")
                        ->orWhere('descripcion_objeto', 'like', "%$q%");
                });
            }
            if ($request->filled('id_tipo_objetos_fk')) {
                $objetosQ->where('id_tipo_objetos_fk', (int) $request->input('id_tipo_objetos_fk'));
            }
            if ($sort && isset($sortable[$sort])) {
                $objetosQ->orderBy($sortable[$sort], $direction);
            } else {
                $objetosQ->orderBy('id_objetos_pk', 'asc');
            }
            $objetos = $objetosQ->get();
            $stats['rolesFiltered'] = $stats['rolesTotal'];
            $stats['objetosFiltered'] = (clone $objetosQ)->count();
            $stats['usuariosConRolFiltered'] = $stats['usuariosConRolTotal'];
        } elseif ($seccion === 'asignar') {
            $sortable = [
                'usuario' => 'usuario',
                'nombre' => 'nombre_usuario',
                'correo' => 'correo_electronico',
                'creado' => 'fecha_creacion',
            ];
            $usuariosQ = Usuario::query()->with('rol:id_rol_pk,rol');
            if ($q) {
                $usuariosQ->where(function ($sub) use ($q) {
                    $sub->where('usuario', 'like', "%$q%")
                        ->orWhere('nombre_usuario', 'like', "%$q%")
                        ->orWhere('correo_electronico', 'like', "%$q%");
                });
            }
            if ($request->filled('id_rol_fk')) {
                $usuariosQ->where('id_rol_fk', (int) $request->input('id_rol_fk'));
            }
            if ($sort && isset($sortable[$sort])) {
                $usuariosQ->orderBy($sortable[$sort], $direction);
            } else {
                $usuariosQ->orderBy('id_usuario_pk', 'asc');
            }
            $usuariosQ->whereNotNull('id_rol_fk');
            $usuarios = $usuariosQ->get();
            $stats['rolesFiltered'] = $stats['rolesTotal'];
            $stats['objetosFiltered'] = $stats['objetosTotal'];
            $stats['usuariosConRolFiltered'] = (clone $usuariosQ)->count();
        } elseif ($seccion === 'gestion') {
            // Matriz de permisos para un rol específico (agrupada y ordenada por módulos del sidebar)
            $rolId = (int)($request->input('rol_id') ?? $request->input('id_rol_fk'));
            if ($rolId) {
                $rol = Rol::find($rolId);
            }
            if (!$rol && ($rolName = $request->input('rol'))) {
                $rol = Rol::where('rol', $rolName)->first();
            }
            if ($rol) {
                // Configuración para ordenar/agrupar como el sidebar
                $SIDEBAR_ORDER = [
                    ['title' => 'Seguridad', 'items' => ['Usuarios', 'Parámetros', 'Parametros', 'Configuración de accesos', 'Configuracion de accesos']],
                    ['title' => 'Clientes', 'items' => ['Empresas', 'Cotizaciones', 'Solicitudes', 'Órdenes de Servicios', 'Ordenes de Servicios']],
                    ['title' => 'Proyectos', 'items' => ['Proyectos', 'Gestión de proyectos', 'Gestion de proyectos', 'Vista de proyectos']],
                    ['title' => 'Tickets', 'items' => ['Gestión de tickets', 'Gestion de tickets', 'Tickets']],
                    ['title' => 'Calendario', 'items' => ['Agencias', 'Calendario', 'Gestión de Calendario', 'Gestion de Calendario']],
                    ['title' => 'Facturación', 'items' => ['Facturas', 'CAI', 'Facturacion']],
                    ['title' => 'Reportes', 'items' => ['Gestión de Reportes', 'Gestion de Reportes', 'Reportes']],
                    ['title' => 'Inventario', 'items' => ['Productos', 'Kardex']],
                    ['title' => 'Administración', 'items' => ['Gestión de personas', 'Gestion de personas', 'Mi perfil', 'Perfil', 'Profile', 'Bitácora', 'Bitacora', 'Gestión de base de datos', 'Gestion de base de datos', 'Administracion']],
                    ['title' => 'Mantenimiento', 'items' => ['Mantenimiento del Sistema', 'Mantenimiento del sistema']],
                    ['title' => 'Catalogo', 'items' => [
                        'Acciones Realizadas',
                        'Administración de Facturas',
                        'Administracion de Facturas',
                        'Categorias de Ingresos y Gastos',
                        'Categorías de Ingresos y Gastos',
                        'Estados CAI',
                        'Estados de Proyecto',
                        'Estados de Solicitud',
                        'Estados de Tickets',
                        'Estados del Calendario',
                        'Género',
                        'Genero',
                        'Perfiles',
                        'Servicio Factura',
                        'Servicios Realizados',
                        'Tipo de Movimiento',
                        'Tipo de Objeto',
                        'Tipo de Personas',
                        'Tipo de Producto',
                        'Tipo de Visita',
                        'Ubicaciones'
                    ]],
                ];
                $HIDDEN_FALLBACK_GROUPS = collect(['configuracion', 'modulo']);
                $norm = function ($s) {
                    return Str::of($s ?? '')->lower()->ascii()->trim()->value();
                };

                $objs = Objeto::with('tipoObjeto:id_tipo_objeto_pk,nombre_tipo_objeto')
                    ->orderBy('nombre_objeto', 'asc')
                    ->get(['id_objetos_pk', 'nombre_objeto', 'id_tipo_objetos_fk']);
                $perms = Permiso::where('id_rol_fk', $rol->id_rol_pk)->get()->keyBy('id_objeto_fk');

                $buildRow = function ($o) use ($perms) {
                    $p = $perms->get($o->id_objetos_pk);
                    return [
                        'objeto' => $o->nombre_objeto,
                        'permiso_ver' => (bool)optional($p)->permiso_ver,
                        'permiso_insercion' => (bool)optional($p)->permiso_insercion,
                        'permiso_consultar' => (bool)optional($p)->permiso_consultar,
                        'permiso_actualizar' => (bool)optional($p)->permiso_actualizar,
                        'permiso_eliminacion' => (bool)optional($p)->permiso_eliminacion,
                    ];
                };

                $assigned = collect();
                $mat = collect();

                foreach ($SIDEBAR_ORDER as $mod) {
                    $moduleTitle = $mod['title'];
                    $moduleTitleNorm = $norm($moduleTitle);
                    $labelOrder = collect($mod['items'])->map($norm);
                    $moduleObj = $objs->first(function ($o) use ($norm, $moduleTitleNorm) {
                        return $norm($o->nombre_objeto) === $moduleTitleNorm;
                    });

                    $rows = collect();
                    foreach ($objs as $o) {
                        if ($assigned->has($o->id_objetos_pk)) continue;
                        $n = $norm($o->nombre_objeto);
                        if ($labelOrder->contains($n)) {
                            // Evitar incluir el objeto del MÓDULO como submódulo
                            if ($moduleObj && $o->id_objetos_pk === $moduleObj->id_objetos_pk) continue;
                            $rows->push($buildRow($o));
                            $assigned->put($o->id_objetos_pk, true);
                        }
                    }
                    if ($rows->isNotEmpty()) {
                        $mat->push(['is_header' => true, 'title' => Str::upper($moduleTitle)]);
                        foreach ($rows as $r) {
                            $mat->push($r);
                        }
                    }
                }

                // Fallback: agrupar el resto por tipo
                $rest = $objs->reject(fn($o) => $assigned->has($o->id_objetos_pk));
                if ($rest->isNotEmpty()) {
                    $byTipo = $rest->groupBy(function ($o) {
                        return optional($o->tipoObjeto)->nombre_tipo_objeto ?? 'Otros';
                    });
                    foreach ($byTipo as $tname => $arr) {
                        if ($HIDDEN_FALLBACK_GROUPS->contains($norm($tname))) continue;
                        $mat->push(['is_header' => true, 'title' => Str::upper($tname ?: 'Otros')]);
                        foreach ($arr->sortBy('nombre_objeto') as $o) {
                            $mat->push($buildRow($o));
                        }
                    }
                }

                $matriz = $mat;
                // Resumen de permisos
                $stats['permResumen']['insercion'] = $matriz->where('permiso_insercion', true)->count();
                $stats['permResumen']['consultar'] = $matriz->where('permiso_consultar', true)->count();
                $stats['permResumen']['actualizar'] = $matriz->where('permiso_actualizar', true)->count();
                $stats['permResumen']['eliminacion'] = $matriz->where('permiso_eliminacion', true)->count();
                $stats['permResumen']['todo'] = $matriz->filter(function ($row) {
                    return !empty($row['objeto']) && $row['permiso_insercion'] && $row['permiso_consultar'] && $row['permiso_actualizar'] && $row['permiso_eliminacion'];
                })->count();
                $stats['permResumen']['ninguno'] = $matriz->filter(function ($row) {
                    return !empty($row['objeto']) && !$row['permiso_insercion'] && !$row['permiso_consultar'] && !$row['permiso_actualizar'] && !$row['permiso_eliminacion'];
                })->count();
            } else {
                // Sin rol elegido, devolver vacío
                $matriz = collect();
            }
            // Cajas muestran totales del sistema
            $stats['rolesFiltered'] = $stats['rolesTotal'];
            $stats['objetosFiltered'] = $stats['objetosTotal'];
            $stats['usuariosConRolFiltered'] = $stats['usuariosConRolTotal'];
        }

        return view('admin.reporte-configuracion-accesos', compact(
            'fecha',
            'modulo',
            'seccion',
            'roles',
            'objetos',
            'usuarios',
            'matriz',
            'permColumns',
            'rol',
            'sort',
            'direction',
            'q',
            'stats'
        ));
    }
}
