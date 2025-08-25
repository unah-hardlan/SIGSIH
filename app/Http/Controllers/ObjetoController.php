<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreObjetoRequest;
use App\Http\Requests\UpdateObjetoRequest;
use App\Http\Resources\ObjetoResource;

class ObjetoController extends Controller
{
    /**
     * Listado con búsqueda, orden y paginación.
     */
    public function index(Request $request)
    {
        $query = Objeto::query();

        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre_objeto', 'like', "%$q%")
                    ->orWhere('descripcion_objeto', 'like', "%$q%");
            });
        }

        if ($request->filled('id_tipo_objetos_fk')) {
            $query->where('id_tipo_objetos_fk', (int) $request->input('id_tipo_objetos_fk'));
        }

        $sortable = [
            'id' => 'id_objetos_pk',
            'nombre' => 'nombre_objeto',
            'descripcion' => 'descripcion_objeto',
            'tipo' => 'id_tipo_objetos_fk',
            'creado' => 'fecha_creacion',
            'modificado' => 'fecha_modificacion',
        ];
        $sort = $request->input('sort', 'id');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortable[$sort] ?? 'id_objetos_pk', $direction);

        if ($request->boolean('all', false)) {
            $collection = $query->get();
            return ObjetoResource::collection($collection)->additional([
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
        return ObjetoResource::collection($paginator)->additional([
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /** Crear */
    public function store(StoreObjetoRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_creacion'] = now();
        $objeto = Objeto::create($data);
        return (new ObjetoResource($objeto))->response()->setStatusCode(201);
    }

    /** Detalle */
    public function show($id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) return response()->json(['error' => 'Objeto no encontrado'], 404);
        return (new ObjetoResource($objeto))->response();
    }

    /** Actualizar */
    public function update(UpdateObjetoRequest $request, $id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) return response()->json(['error' => 'Objeto no encontrado'], 404);
        $data = $request->validated();
        $data['modificado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_modificacion'] = now();
        $objeto->update($data);
        return (new ObjetoResource($objeto))->response();
    }

    /** Eliminar */
    public function destroy($id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) return response()->json(['error' => 'Objeto no encontrado'], 404);
        $objeto->delete();
        return response()->json(['message' => 'Objeto eliminado']);
    }
}
