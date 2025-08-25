<?php

namespace App\Http\Controllers;

use App\Models\AccionRealizada;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAccionRealizadaRequest;
use App\Http\Requests\UpdateAccionRealizadaRequest;
use App\Http\Resources\AccionRealizadaResource;

class AccionRealizadaController extends Controller
{
    public function index(Request $request)
    {
        $query = AccionRealizada::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_accion','like',"%$q%")
                    ->orWhere('descripcion_accion','like',"%$q%");
            });
        }
        $sortable = [ 'nombre' => 'nombre_accion' ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_accion_realizada_pk',$direction);

        if($request->boolean('all')){
            return AccionRealizadaResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return AccionRealizadaResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreAccionRealizadaRequest $request)
    {
        $accion = AccionRealizada::create($request->validated());
        return (new AccionRealizadaResource($accion))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $accion = AccionRealizada::find($id);
        if(!$accion) return response()->json(['error'=>'Accion no encontrada'],404);
        return (new AccionRealizadaResource($accion))->response();
    }

    public function update(UpdateAccionRealizadaRequest $request, $id)
    {
        $accion = AccionRealizada::find($id);
        if(!$accion) return response()->json(['error'=>'Accion no encontrada'],404);
        $accion->update($request->validated());
        return (new AccionRealizadaResource($accion))->response();
    }

    public function destroy($id)
    {
        $accion = AccionRealizada::find($id);
        if(!$accion) return response()->json(['error'=>'Accion no encontrada'],404);
        $accion->delete();
        return response()->json(['message'=>'Accion eliminada']);
    }
}
