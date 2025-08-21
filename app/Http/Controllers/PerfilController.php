<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;
use App\Http\Requests\StorePerfilRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Http\Resources\PerfilResource;

class PerfilController extends Controller
{
    public function index(Request $request)
    {
        $query = Perfil::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_perfil','like',"%$q%")
                    ->orWhere('descripcion_perfil','like',"%$q%");
            });
        }
        $sortable = [
            'nombre' => 'nombre_perfil',
            'descripcion' => 'descripcion_perfil',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_perfil_pk', $direction);
        if($request->boolean('all')){
            return PerfilResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',10);
        $items = $query->paginate($perPage);
        return PerfilResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StorePerfilRequest $request)
    {
        $perfil = Perfil::create($request->validated());
        return (new PerfilResource($perfil))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $perfil = Perfil::find($id);
        if(!$perfil) return response()->json(['error'=>'Perfil no encontrado'],404);
        return (new PerfilResource($perfil))->response();
    }

    public function update(UpdatePerfilRequest $request, $id)
    {
        $perfil = Perfil::find($id);
        if(!$perfil) return response()->json(['error'=>'Perfil no encontrado'],404);
        $perfil->update($request->validated());
        return (new PerfilResource($perfil))->response();
    }

    public function destroy($id)
    {
        $perfil = Perfil::find($id);
        if(!$perfil) return response()->json(['error'=>'Perfil no encontrado'],404);
        $perfil->delete();
        return response()->json(['message'=>'Perfil eliminado']);
    }
}
