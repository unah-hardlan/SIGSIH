<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGeneroRequest;
use App\Http\Requests\UpdateGeneroRequest;
use App\Http\Resources\GeneroResource;

class GeneroController extends Controller
{
    public function index(Request $request)
    {
        $query = Genero::query();
        if($q = $request->input('q')){
            $query->where('genero','like',"%$q%");
        }
        $sortable = [
            'nombre' => 'genero',
            'genero' => 'genero'
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_genero_pk', $direction);
        
        if($request->boolean('all')){
            return GeneroResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return GeneroResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreGeneroRequest $request)
    {
        $genero = Genero::create($request->validated());
        return (new GeneroResource($genero))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $genero = Genero::find($id);
        if(!$genero) return response()->json(['error'=>'Género no encontrado'],404);
        return (new GeneroResource($genero))->response();
    }

    public function update(UpdateGeneroRequest $request, $id)
    {
        $genero = Genero::find($id);
        if(!$genero) return response()->json(['error'=>'Género no encontrado'],404);
        $genero->update($request->validated());
        return (new GeneroResource($genero))->response();
    }

    public function destroy($id)
    {
        $genero = Genero::find($id);
        if(!$genero) return response()->json(['error'=>'Género no encontrado'],404);
        $genero->delete();
        return response()->json(['message'=>'Género eliminado']);
    }
}