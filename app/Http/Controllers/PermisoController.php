<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use App\Http\Requests\StorePermisoRequest;
use App\Http\Requests\UpdatePermisoRequest;
use App\Http\Resources\PermisoResource;

class PermisoController extends Controller
{
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
        $data['creado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_creacion'] = now();
        $permiso = Permiso::create($data)->load(['rol:id_rol_pk,rol','objeto:id_objetos_pk,nombre_objeto']);
        return (new PermisoResource($permiso))->response()->setStatusCode(201);
    }

    /** Detalle por ID */
    public function show($id)
    {
        $permiso = Permiso::with(['rol:id_rol_pk,rol','objeto:id_objetos_pk,nombre_objeto'])->find($id);
        if (!$permiso) return response()->json(['error' => 'Permiso no encontrado'], 404);
        return (new PermisoResource($permiso))->response();
    }


    /** Actualizar por ID */
    public function update(UpdatePermisoRequest $request, $id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) return response()->json(['error' => 'Permiso no encontrado'], 404);
        $data = $request->validated();
        $data['modificado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_modificacion'] = now();
        $permiso->update($data);
        $permiso->load(['rol:id_rol_pk,rol','objeto:id_objetos_pk,nombre_objeto']);
        return (new PermisoResource($permiso))->response();
    }


    /** Eliminar por ID */
    public function destroy($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) return response()->json(['error' => 'Permiso no encontrado'], 404);
        $permiso->delete();
        return response()->json(['message' => 'Permiso eliminado']);
    }

}
