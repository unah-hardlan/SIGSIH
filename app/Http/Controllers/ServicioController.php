<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Http\Resources\ServicioResource;
use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicio::query();
        
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_servicio','like',"%$q%");
            });
        }
        
        $sortable = [
            'nombre' => 'nombre_servicio',
            'tarifa' => 'tarifa',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_servicio_pk',$direction);

        if($request->boolean('all')){
            return ServicioResource::collection($query->get());
        }
        
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return ServicioResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreServicioRequest $request)
    {
        $servicio = Servicio::create($request->validated());
        return (new ServicioResource($servicio))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $servicio = Servicio::find($id);
        if(!$servicio) return response()->json(['error'=>'Servicio no encontrado'],404);
        return (new ServicioResource($servicio))->response();
    }

    public function update(UpdateServicioRequest $request, $id)
    {
        $servicio = Servicio::find($id);
        if(!$servicio) return response()->json(['error'=>'Servicio no encontrado'],404);
        $servicio->update($request->validated());
        return (new ServicioResource($servicio))->response();
    }

    public function destroy($id)
    {
        $servicio = Servicio::find($id);
        if(!$servicio) return response()->json(['error'=>'Servicio no encontrado'],404);
        $servicio->delete();
        return response()->json(['message'=>'Servicio eliminado']);
    }
}