<?php

namespace App\Http\Controllers;

use App\Models\EstadoCalendario;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEstadoCalendarioRequest;
use App\Http\Requests\UpdateEstadoCalendarioRequest;
use App\Http\Resources\EstadoCalendarioResource;

class EstadoCalendarioController extends Controller
{
    public function index(Request $request)
    {
        $query = EstadoCalendario::query();
        
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre','like',"%$q%")
                    ->orWhere('codigo','like',"%$q%")
                    ->orWhere('descripcion','like',"%$q%");
            });
        }
        
        $sortable = [
            'nombre' => 'nombre',
            'codigo' => 'codigo',
            'orden' => 'orden',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'orden',$direction);

        if($request->boolean('all')){
            return EstadoCalendarioResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return EstadoCalendarioResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreEstadoCalendarioRequest $request)
    {
        $estadoCalendario = EstadoCalendario::create($request->validated());
        return (new EstadoCalendarioResource($estadoCalendario))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $estadoCalendario = EstadoCalendario::find($id);
        if(!$estadoCalendario) return response()->json(['error'=>'Estado de Calendario no encontrado'],404);
        return (new EstadoCalendarioResource($estadoCalendario))->response();
    }

    public function update(UpdateEstadoCalendarioRequest $request, $id)
    {
        $estadoCalendario = EstadoCalendario::find($id);
        if(!$estadoCalendario) return response()->json(['error'=>'Estado de Calendario no encontrado'],404);
        $estadoCalendario->update($request->validated());
        return (new EstadoCalendarioResource($estadoCalendario))->response();
    }

    public function destroy($id)
    {
        $estadoCalendario = EstadoCalendario::find($id);
        if(!$estadoCalendario) return response()->json(['error'=>'Estado de Calendario no encontrado'],404);
        $estadoCalendario->delete();
        return response()->json(['message'=>'Estado de Calendario eliminado']);
    }
}