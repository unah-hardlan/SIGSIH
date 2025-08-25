<?php

namespace App\Http\Controllers;

use App\Models\TipoVisita;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTipoVisitaRequest;
use App\Http\Requests\UpdateTipoVisitaRequest;
use App\Http\Resources\TipoVisitaResource;

class TipoVisitaController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoVisita::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_tipo_visita','like',"%$q%")
                    ->orWhere('descripcion_tipo_visita','like',"%$q%");
            });
        }
        $sortable = [ 'nombre' => 'nombre_tipo_visita' ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_tipo_visita_pk',$direction);

        if($request->boolean('all')){
            return TipoVisitaResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return TipoVisitaResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreTipoVisitaRequest $request)
    {
        $tipo = TipoVisita::create($request->validated());
        return (new TipoVisitaResource($tipo))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $tipo = TipoVisita::find($id);
        if(!$tipo) return response()->json(['error'=>'TipoVisita no encontrado'],404);
        return (new TipoVisitaResource($tipo))->response();
    }

    public function update(UpdateTipoVisitaRequest $request, $id)
    {
        $tipo = TipoVisita::find($id);
        if(!$tipo) return response()->json(['error'=>'TipoVisita no encontrado'],404);
        $tipo->update($request->validated());
        return (new TipoVisitaResource($tipo))->response();
    }

    public function destroy($id)
    {
        $tipo = TipoVisita::find($id);
        if(!$tipo) return response()->json(['error'=>'TipoVisita no encontrado'],404);
        $tipo->delete();
        return response()->json(['message'=>'TipoVisita eliminado']);
    }
}
