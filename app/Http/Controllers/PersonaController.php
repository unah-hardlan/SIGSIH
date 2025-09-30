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
        $query = Persona::query()->with(['genero']);
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
    if($genero = $request->input('id_genero_fk')) $query->where('id_genero_fk',$genero);

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
    $persona->load(['genero']);
        return (new PersonaResource($persona))->response()->setStatusCode(201);
    }

    public function show($id)
    {
    $persona = Persona::with(['genero'])->find($id);
        if(!$persona) return response()->json(['error'=>'Persona no encontrada'],404);
        return (new PersonaResource($persona))->response();
    }

    public function update(UpdatePersonaRequest $request, $id)
    {
        $persona = Persona::find($id);
        if(!$persona) return response()->json(['error'=>'Persona no encontrada'],404);
    $persona->update($request->validated());
    $persona->load(['genero']);
        return (new PersonaResource($persona))->response();
    }

    public function destroy($id)
    {
        $persona = Persona::find($id);
        if(!$persona) return response()->json(['error'=>'Persona no encontrada'],404);
        $persona->delete();
        return response()->json(['message'=>'Persona eliminada']);
    }

    // Reporte de Gestión de Personas (vista)
    public function reporte(Request $request)
    {
        $q = $request->input('q');
        $sort = $request->input('sort','nombre');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
    $tipo = $request->input('tipo'); // deprecado
        $genero = $request->input('genero'); // puede ser nombre

    $query = Persona::query()->with(['genero']);
        if($q){
            $query->where(function($sub) use ($q){
                $sub->where('primer_nombre','like',"%$q%")
                    ->orWhere('segundo_nombre','like',"%$q%")
                    ->orWhere('primer_apellido','like',"%$q%")
                    ->orWhere('segundo_apellido','like',"%$q%")
                    ->orWhere('dni','like',"%$q%")
                    ->orWhere('cargo','like',"%$q%");
            });
        }
        // filtro tipo persona eliminado
        if($genero){
            if(is_numeric($genero)){
                $query->where('id_genero_fk',(int)$genero);
            } else {
                $query->whereHas('genero', function($q2) use($genero){
                    $q2->whereRaw('LOWER(genero) = ?', [strtolower($genero)]);
                });
            }
        }

        $sortable = [
            'nombre' => 'primer_nombre',
            'apellido' => 'primer_apellido',
            'dni' => 'dni',
            'cargo' => 'cargo',
        ];
        $query->orderBy($sortable[$sort] ?? 'id_persona_pk', $direction);

    $rows = $query->get();
        $fecha = $request->query('fecha', now()->format('d-M-Y'));
        $modulo = $request->query('modulo', 'Gestion de Personas');

        return view('admin.reporte-gestion-personas', compact('fecha','modulo','rows'));
    }
}
