<?php

namespace App\Http\Controllers;

use App\Models\TipoObjeto;
use Illuminate\Http\Request;
use App\Http\Resources\TipoObjetoResource;
use Illuminate\Validation\Rule;

class TipoObjetoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoObjeto::query();
        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q){
                $sub->where('nombre_tipo_objeto','like',"%$q%")
                    ->orWhere('descripcion_tipo_objeto','like',"%$q%");
            });
        }
        $query->orderBy('nombre_tipo_objeto','asc');

        if ($request->boolean('all')) {
            return TipoObjetoResource::collection($query->get());
        }

        $perPage = (int) $request->input('per_page', 20);
        $items = $query->paginate($perPage);
        return TipoObjetoResource::collection($items)->additional([
            'meta' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_tipo_objeto' => 'required|string|max:50|unique:tbl_tipo_objetos,nombre_tipo_objeto',
            'descripcion_tipo_objeto' => 'nullable|string|max:255',
        ], [
            'nombre_tipo_objeto.required' => 'El nombre del tipo de objeto es obligatorio.',
            'nombre_tipo_objeto.unique' => 'Ya existe un tipo de objeto con ese nombre.',
        ]);

        $tipo = TipoObjeto::create($request->only(['nombre_tipo_objeto', 'descripcion_tipo_objeto']));

        return (new TipoObjetoResource($tipo))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $tipo = TipoObjeto::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de objeto no encontrado'], 404);
        }

        return new TipoObjetoResource($tipo);
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoObjeto::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de objeto no encontrado'], 404);
        }

        $request->validate([
            'nombre_tipo_objeto' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_tipo_objetos', 'nombre_tipo_objeto')->ignore($id, 'id_tipo_objeto_pk')
            ],
            'descripcion_tipo_objeto' => 'sometimes|nullable|string|max:255',
        ], [
            'nombre_tipo_objeto.unique' => 'Ya existe un tipo de objeto con ese nombre.',
        ]);

        $tipo->update($request->only(['nombre_tipo_objeto', 'descripcion_tipo_objeto']));

        return new TipoObjetoResource($tipo);
    }

    public function destroy($id)
    {
        $tipo = TipoObjeto::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de objeto no encontrado'], 404);
        }

        $tipo->delete();

        return response()->json(['message' => 'Tipo de objeto eliminado exitosamente']);
    }
}
