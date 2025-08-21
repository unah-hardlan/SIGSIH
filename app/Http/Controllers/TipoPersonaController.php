<?php

namespace App\Http\Controllers;

use App\Models\TipoPersona;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTipoPersonaRequest;
use App\Http\Requests\UpdateTipoPersonaRequest;
use App\Http\Resources\TipoPersonaResource;

class TipoPersonaController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoPersona::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_tipo_persona','like',"%$q%")
                    ->orWhere('descripcion','like',"%$q%");
            });
        }
        $sortable = [
            'nombre' => 'nombre_tipo_persona',
            'descripcion' => 'descripcion',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_tipo_persona_pk', $direction);
        if($request->boolean('all')){
            return TipoPersonaResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',10);
        $items = $query->paginate($perPage);
        return TipoPersonaResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreTipoPersonaRequest $request)
    {
        $data = $request->validated();
        $tipo = TipoPersona::create($data);
        return (new TipoPersonaResource($tipo))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $tipo = TipoPersona::find($id);
        if(!$tipo) return response()->json(['error'=>'Tipo de persona no encontrado'],404);
        return (new TipoPersonaResource($tipo))->response();
    }

    public function update(UpdateTipoPersonaRequest $request, $id)
    {
        $tipo = TipoPersona::find($id);
        if(!$tipo) return response()->json(['error'=>'Tipo de persona no encontrado'],404);
        $tipo->update($request->validated());
        return (new TipoPersonaResource($tipo))->response();
    }

    public function destroy($id)
    {
        $tipo = TipoPersona::find($id);
        if(!$tipo) return response()->json(['error'=>'Tipo de persona no encontrado'],404);
        $tipo->delete();
        return response()->json(['message'=>'Tipo de persona eliminado']);
    }
}
