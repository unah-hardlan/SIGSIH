<?php

namespace App\Http\Controllers;

use App\Models\ServicioRealizado;
use Illuminate\Http\Request;
use App\Http\Requests\StoreServicioRealizadoRequest;
use App\Http\Requests\UpdateServicioRealizadoRequest;
use App\Http\Resources\ServicioRealizadoResource;

class ServicioRealizadoController extends Controller
{
    public function index(Request $request)
    {
        $query = ServicioRealizado::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_servicio','like',"%$q%")
                    ->orWhere('descripcion_servicio','like',"%$q%");
            });
        }
        $sortable = [ 'nombre' => 'nombre_servicio' ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_servicio_realizado_pk',$direction);

        if($request->boolean('all')){
            return ServicioRealizadoResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return ServicioRealizadoResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreServicioRealizadoRequest $request)
    {
        $servicio = ServicioRealizado::create($request->validated());
        return (new ServicioRealizadoResource($servicio))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $servicio = ServicioRealizado::find($id);
        if(!$servicio) return response()->json(['error'=>'Servicio no encontrado'],404);
        return (new ServicioRealizadoResource($servicio))->response();
    }

    public function update(UpdateServicioRealizadoRequest $request, $id)
    {
        $servicio = ServicioRealizado::find($id);
        if(!$servicio) return response()->json(['error'=>'Servicio no encontrado'],404);
        $servicio->update($request->validated());
        return (new ServicioRealizadoResource($servicio))->response();
    }

    public function destroy($id)
    {
        $servicio = ServicioRealizado::find($id);
        if(!$servicio) return response()->json(['error'=>'Servicio no encontrado'],404);
        $servicio->delete();
        return response()->json(['message'=>'Servicio eliminado']);
    }
}
