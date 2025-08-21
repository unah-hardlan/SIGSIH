<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Http\Resources\PersonaResource;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $query = Persona::query()->with(['tipoPersona','genero','perfil']);
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('primer_nombre','like',"%$q%")
                    ->orWhere('segundo_nombre','like',"%$q%")
                    ->orWhere('primer_apellido','like',"%$q%")
                    ->orWhere('segundo_apellido','like',"%$q%")
                    ->orWhere('dni','like',"%$q%")
                    ->orWhere('cargo','like',"%$q%");
            });
        }
        if($tipo = $request->input('id_tipo_persona_fk')) $query->where('id_tipo_persona_fk',$tipo);
        if($genero = $request->input('id_genero_fk')) $query->where('id_genero_fk',$genero);
        if($perfil = $request->input('id_perfil_fk')) $query->where('id_perfil_fk',$perfil);

        $sortable = [
            'nombre' => 'primer_nombre',
            'apellido' => 'primer_apellido',
            'dni' => 'dni',
            'cargo' => 'cargo',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_persona_pk', $direction);

        if($request->boolean('all')){
            return PersonaResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',10);
        $items = $query->paginate($perPage);
        return PersonaResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StorePersonaRequest $request)
    {
        $persona = Persona::create($request->validated());
        $persona->load(['tipoPersona','genero','perfil']);
        return (new PersonaResource($persona))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $persona = Persona::with(['tipoPersona','genero','perfil'])->find($id);
        if(!$persona) return response()->json(['error'=>'Persona no encontrada'],404);
        return (new PersonaResource($persona))->response();
    }

    public function update(UpdatePersonaRequest $request, $id)
    {
        $persona = Persona::find($id);
        if(!$persona) return response()->json(['error'=>'Persona no encontrada'],404);
        $persona->update($request->validated());
        $persona->load(['tipoPersona','genero','perfil']);
        return (new PersonaResource($persona))->response();
    }

    public function destroy($id)
    {
        $persona = Persona::find($id);
        if(!$persona) return response()->json(['error'=>'Persona no encontrada'],404);
        $persona->delete();
        return response()->json(['message'=>'Persona eliminada']);
    }
}
