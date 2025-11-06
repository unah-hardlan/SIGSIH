<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Requests\StorePermisoRequest;
use App\Http\Requests\UpdatePermisoRequest;
use App\Http\Resources\PermisoResource;
use App\Services\BitacoraService;
use App\Support\AdminModuleRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PermisoController extends Controller
{
    public function __construct(private BitacoraService $bitacora) {}
    /** Listado con búsqueda, filtros, orden y paginación */
    public function index(Request $request)
    {
        $query = Permiso::query()->with(['rol:id_rol_pk,rol', 'objeto:id_objetos_pk,nombre_objeto']);

        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('rol', function ($w) use ($q) {
                    $w->where('rol', 'like', "%$q%");
                })->orWhereHas('objeto', function ($w) use ($q) {
                    $w->where('nombre_objeto', 'like', "%$q%")
                        ->orWhere('descripcion_objeto', 'like', "%$q%");
                });
            });
        }

        if ($request->filled('id_rol_fk')) {
            $query->where('id_rol_fk', (int) $request->input('id_rol_fk'));
        }
        if ($request->filled('id_objeto_fk')) {
            $query->where('id_objeto_fk', (int) $request->input('id_objeto_fk'));
        }

        $sortable = [
            'rol' => 'id_rol_fk',
            'objeto' => 'id_objeto_fk',
            'creado' => 'fecha_creacion',
            'modificado' => 'fecha_modificacion',
        ];
        $sort = $request->input('sort', 'rol');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortable[$sort] ?? 'id_rol_fk', $direction);

        if ($request->boolean('all', false)) {
            $collection = $query->get();
            return PermisoResource::collection($collection)->additional([
                'meta' => [
                    'page' => 1,
                    'per_page' => $collection->count(),
                    'total' => $collection->count(),
                    'last_page' => 1,
                ],
            ]);
        }

        $perPage = (int) $request->input('per_page', 10);
        $paginator = $query->paginate($perPage);
        return PermisoResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /** Crear */
    public function store(StorePermisoRequest $request)
    {
        $data = $request->validated();
        if ($response = $this->guardAdminSecurity((int) ($data['id_rol_fk'] ?? 0), (int) ($data['id_objeto_fk'] ?? 0), $data)) {
            return $response;
        }
        $data['creado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_creacion'] = now();
        $permiso = Permiso::create($data)->load(['rol:id_rol_pk,rol', 'objeto:id_objetos_pk,nombre_objeto']);
        try {
            $flags = sprintf(
                '[Ver:%s, Leer:%s, Crear:%s, Editar:%s, Eliminar:%s]',
                $permiso->permiso_ver ? 'Sí' : 'No',
                $permiso->permiso_consultar ? 'Sí' : 'No',
                $permiso->permiso_insercion ? 'Sí' : 'No',
                $permiso->permiso_actualizar ? 'Sí' : 'No',
                $permiso->permiso_eliminacion ? 'Sí' : 'No'
            );
            $desc = 'Asignación de permisos al rol ' . $permiso->rol->rol . ' sobre ' . $permiso->objeto->nombre_objeto . ' ' . $flags;
            $this->bitacora->logFor('Permisos', 'Insertar', $desc);
        } catch (\Throwable $e) {
        }
        return (new PermisoResource($permiso))->response()->setStatusCode(201);
    }

    /** Detalle por ID */
    public function show($id)
    {
        $permiso = Permiso::with(['rol:id_rol_pk,rol', 'objeto:id_objetos_pk,nombre_objeto'])->find($id);
        if (!$permiso) return response()->json(['error' => 'Permiso no encontrado'], 404);
        return (new PermisoResource($permiso))->response();
    }



    private function guardAdminSecurity(int $rolId, int $objetoId, array $payload = [], bool $isDelete = false): ?JsonResponse
    {
        $rol = Rol::select('id_rol_pk', 'rol')->find($rolId);
        if (!$rol || !$this->isAdministradorRole($rol->rol)) {
            return null;
        }

        $objeto = Objeto::select('id_objetos_pk', 'nombre_objeto')->find($objetoId);
        if (!$objeto) {
            return null;
        }

        $moduleKey = AdminModuleRegistry::moduleKeyForObjectName($objeto->nombre_objeto ?? '');
        if ($moduleKey !== 'seguridad') {
            return null;
        }

        if ($isDelete || $this->payloadRevokesSecurity($payload)) {
            return response()->json([
                'error' => 'El rol Administrador debe conservar los permisos del módulo Seguridad.',
            ], 422);
        }

        return null;
    }

    private function payloadRevokesSecurity(array $payload): bool
    {
        foreach (['permiso_ver', 'permiso_consultar', 'permiso_insercion', 'permiso_actualizar', 'permiso_eliminacion'] as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] === false) {
                return true;
            }
        }

        return false;
    }

    private function isAdministradorRole(?string $rolNombre): bool
    {
        if (!$rolNombre) {
            return false;
        }

        return Str::of($rolNombre)->ascii()->lower()->trim() === 'administrador';
    }

    /** Actualizar por ID */
    public function update(UpdatePermisoRequest $request, $id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) return response()->json(['error' => 'Permiso no encontrado'], 404);
        $data = $request->validated();
        $rolTarget = (int) ($data['id_rol_fk'] ?? $permiso->id_rol_fk);
        $objTarget = (int) ($data['id_objeto_fk'] ?? $permiso->id_objeto_fk);
        if ($response = $this->guardAdminSecurity($rolTarget, $objTarget, $data)) {
            return $response;
        }
        $data['modificado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_modificacion'] = now();
        $permiso->update($data);
        $permiso->load(['rol:id_rol_pk,rol', 'objeto:id_objetos_pk,nombre_objeto']);
        try {
            $flags = sprintf(
                '[Ver:%s, Leer:%s, Crear:%s, Editar:%s, Eliminar:%s]',
                $permiso->permiso_ver ? 'Sí' : 'No',
                $permiso->permiso_consultar ? 'Sí' : 'No',
                $permiso->permiso_insercion ? 'Sí' : 'No',
                $permiso->permiso_actualizar ? 'Sí' : 'No',
                $permiso->permiso_eliminacion ? 'Sí' : 'No'
            );
            $desc = 'Actualización de permisos del rol ' . $permiso->rol->rol . ' sobre ' . $permiso->objeto->nombre_objeto . ' ' . $flags;
            $this->bitacora->logFor('Permisos', 'Actualizar', $desc);
        } catch (\Throwable $e) {
        }
        return (new PermisoResource($permiso))->response();
    }


    /** Eliminar por ID */
    public function destroy($id)
    {
        $permiso = Permiso::with(['rol:id_rol_pk,rol', 'objeto:id_objetos_pk,nombre_objeto'])->find($id);
        if (!$permiso) return response()->json(['error' => 'Permiso no encontrado'], 404);
        if ($response = $this->guardAdminSecurity((int) $permiso->id_rol_fk, (int) $permiso->id_objeto_fk, [], true)) {
            return $response;
        }
        $permiso->delete();
        try {
            $desc = 'Eliminación de permisos del rol ' . $permiso->rol->rol . ' sobre ' . $permiso->objeto->nombre_objeto;
            $this->bitacora->logFor('Permisos', 'Eliminar', $desc);
        } catch (\Throwable $e) {
        }
        return response()->json(['message' => 'Permiso eliminado']);
    }

    /**
     * Upsert por combinación (rol,objeto): actualiza o crea una fila única con los flags recibidos.
     */
    public function upsertForRoleObject($idRol, $idObjeto, Request $request)
    {
        // Validaciones de flags opcionales; si no vienen, no se modifican
        $validated = $request->validate([
            'permiso_insercion' => 'sometimes|boolean',
            'permiso_consultar' => 'sometimes|boolean',
            'permiso_ver' => 'sometimes|boolean',
            'permiso_actualizar' => 'sometimes|boolean',
            'permiso_eliminacion' => 'sometimes|boolean',
        ]);

        // Validar existencia de llaves foráneas para evitar 500
        $rolExists = \App\Models\Rol::where('id_rol_pk', (int)$idRol)->exists();
        if (!$rolExists) return response()->json(['error' => 'Rol no encontrado'], 404);
        $objExists = \App\Models\Objeto::where('id_objetos_pk', (int)$idObjeto)->exists();
        if (!$objExists) return response()->json(['error' => 'Objeto no encontrado'], 404);

        if ($response = $this->guardAdminSecurity((int) $idRol, (int) $idObjeto, $validated)) {
            return $response;
        }

        // Traer existente por clave compuesta
        $permiso = Permiso::where('id_rol_fk', (int)$idRol)
            ->where('id_objeto_fk', (int)$idObjeto)
            ->first();

        if ($permiso) {
            // Actualizar usando query por clave compuesta para evitar dependencia del PK
            $update = $validated;
            $update['modificado_por'] = auth()->user()->usuario ?? 'system';
            $update['fecha_modificacion'] = now();
            Permiso::where('id_rol_fk', (int)$idRol)
                ->where('id_objeto_fk', (int)$idObjeto)
                ->update($update);
            $permiso = Permiso::where('id_rol_fk', (int)$idRol)
                ->where('id_objeto_fk', (int)$idObjeto)
                ->first();
        } else {
            // Crear con defaults false para flags no provistos
            $payload = array_merge([
                'id_rol_fk' => (int)$idRol,
                'id_objeto_fk' => (int)$idObjeto,
                'permiso_ver' => false,
                'permiso_insercion' => false,
                'permiso_consultar' => false,
                'permiso_actualizar' => false,
                'permiso_eliminacion' => false,
            ], $validated);
            $permiso = Permiso::create($payload);
        }

        $permiso->load(['rol:id_rol_pk,rol', 'objeto:id_objetos_pk,nombre_objeto']);
        try {
            $flags = sprintf(
                '[Ver:%s, Leer:%s, Crear:%s, Editar:%s, Eliminar:%s]',
                $permiso->permiso_ver ? 'Sí' : 'No',
                $permiso->permiso_consultar ? 'Sí' : 'No',
                $permiso->permiso_insercion ? 'Sí' : 'No',
                $permiso->permiso_actualizar ? 'Sí' : 'No',
                $permiso->permiso_eliminacion ? 'Sí' : 'No'
            );
            $desc = 'Actualización de permisos (upsert) del rol ' . $permiso->rol->rol . ' sobre ' . $permiso->objeto->nombre_objeto . ' ' . $flags;
            $this->bitacora->logFor('Permisos', 'Actualizar', $desc);
        } catch (\Throwable $e) {
        }
        return (new PermisoResource($permiso))->response();
    }
}
