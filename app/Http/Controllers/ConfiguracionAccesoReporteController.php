<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\Objeto;
use App\Models\Permiso;
use App\Models\Usuario;

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
        $permColumns = [
            ['field' => 'permiso_insercion', 'label' => 'Insertar'],
            ['field' => 'permiso_consultar', 'label' => 'Consultar'],
            ['field' => 'permiso_actualizar', 'label' => 'Actualizar'],
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
            // Matriz de permisos para un rol específico
            $rolId = (int)($request->input('rol_id') ?? $request->input('id_rol_fk'));
            if ($rolId) {
                $rol = Rol::find($rolId);
            }
            if (!$rol && ($rolName = $request->input('rol'))) {
                $rol = Rol::where('rol', $rolName)->first();
            }
            if ($rol) {
                $objs = Objeto::orderBy('nombre_objeto', 'asc')->get(['id_objetos_pk', 'nombre_objeto']);
                $perms = Permiso::where('id_rol_fk', $rol->id_rol_pk)->get()->keyBy('id_objeto_fk');
                $matriz = $objs->map(function ($o) use ($perms) {
                    $p = $perms->get($o->id_objetos_pk);
                    return [
                        'objeto' => $o->nombre_objeto,
                        'permiso_insercion' => (bool)optional($p)->permiso_insercion,
                        'permiso_consultar' => (bool)optional($p)->permiso_consultar,
                        'permiso_actualizar' => (bool)optional($p)->permiso_actualizar,
                        'permiso_eliminacion' => (bool)optional($p)->permiso_eliminacion,
                    ];
                });
                // Resumen de permisos
                $stats['permResumen']['insercion'] = $matriz->where('permiso_insercion', true)->count();
                $stats['permResumen']['consultar'] = $matriz->where('permiso_consultar', true)->count();
                $stats['permResumen']['actualizar'] = $matriz->where('permiso_actualizar', true)->count();
                $stats['permResumen']['eliminacion'] = $matriz->where('permiso_eliminacion', true)->count();
                $stats['permResumen']['todo'] = $matriz->filter(function ($row) {
                    return $row['permiso_insercion'] && $row['permiso_consultar'] && $row['permiso_actualizar'] && $row['permiso_eliminacion'];
                })->count();
                $stats['permResumen']['ninguno'] = $matriz->filter(function ($row) {
                    return !$row['permiso_insercion'] && !$row['permiso_consultar'] && !$row['permiso_actualizar'] && !$row['permiso_eliminacion'];
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
            'fecha', 'modulo', 'seccion', 'roles', 'objetos', 'usuarios', 'matriz', 'permColumns', 'rol', 'sort', 'direction', 'q', 'stats'
        ));
    }
}
