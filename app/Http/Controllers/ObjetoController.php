<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreObjetoRequest;
use App\Http\Requests\UpdateObjetoRequest;
use App\Http\Resources\ObjetoResource;
use App\Services\BitacoraService;

class ObjetoController extends Controller
{
    public function __construct(private BitacoraService $bitacora) {}
    
    public function index(Request $request)
    {
        $query = Objeto::query()->with('tipoObjeto');

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

    
    public function store(StoreObjetoRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_creacion'] = now();
        $objeto = Objeto::create($data);
        try {
            $this->bitacora->log('Insertar', 'Creación de objeto ' . $objeto->nombre_objeto, $objeto->id_objetos_pk, null, [
                'tabla' => 'tbl_objetos',
                'id_registro' => $objeto->id_objetos_pk,
                'despues' => $objeto->getAttributes(),
            ]);
        } catch (\Throwable $e) {
        }
        return (new ObjetoResource($objeto))->response()->setStatusCode(201);
    }

    
    public function show($id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) return response()->json(['error' => 'Objeto no encontrado'], 404);
        return (new ObjetoResource($objeto))->response();
    }

    
    public function update(UpdateObjetoRequest $request, $id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) return response()->json(['error' => 'Objeto no encontrado'], 404);
        $data = $request->validated();
        $data['modificado_por'] = auth()->user()->usuario ?? 'system';
        $data['fecha_modificacion'] = now();
        $antes = $objeto->getOriginal();
        $objeto->update($data);
        $objeto->refresh();
        try {
            $this->bitacora->log('Actualizar', 'Actualización de objeto ' . $objeto->nombre_objeto, $objeto->id_objetos_pk, null, [
                'tabla' => 'tbl_objetos',
                'id_registro' => $objeto->id_objetos_pk,
                'antes' => $antes,
                'despues' => $objeto->getAttributes(),
            ]);
        } catch (\Throwable $e) {
        }
        return (new ObjetoResource($objeto))->response();
    }

    
    public function destroy($id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) return response()->json(['error' => 'Objeto no encontrado'], 404);
        try {
            $objeto->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'error' => 'conflict',
                    'message' => 'No se puede eliminar el objeto porque tiene registros asociados (Bitácora o Permisos).'
                ], 409);
            }
            throw $e;
        }
        try {
            $this->bitacora->log('Eliminar', 'Eliminación de objeto ' . $objeto->nombre_objeto, $objeto->id_objetos_pk, null, [
                'tabla' => 'tbl_objetos',
                'id_registro' => $objeto->id_objetos_pk,
            ]);
        } catch (\Throwable $e) {
        }
        return response()->json(['message' => 'Objeto eliminado']);
    }
}
