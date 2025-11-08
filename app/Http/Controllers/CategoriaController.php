<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query();

        
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_categoria', 'like', '%' . $searchTerm . '%')
                  ->orWhere('descripcion_categoria', 'like', '%' . $searchTerm . '%')
                  ->orWhere('tipo_categoria', 'like', '%' . $searchTerm . '%');
            });
        }

        
        if ($request->has('sort') && !empty($request->sort)) {
            $sortField = $request->sort;
            switch ($sortField) {
                case 'nombre_categoria':
                    $query->orderBy('nombre_categoria');
                    break;
                case 'id_categoria_pk':
                    $query->orderBy('id_categoria_pk');
                    break;
                default:
                    $query->orderBy('nombre_categoria', 'asc');
            }
        } else {
            $query->orderBy('nombre_categoria', 'asc');
        }

        $categorias = $query->get();

        return response()->json([
            'success' => true,
            'data' => CategoriaResource::collection($categorias)
        ]);
    }

    public function store(StoreCategoriaRequest $request)
    {
        $categoria = Categoria::create($request->validated());
        
        return (new CategoriaResource($categoria))
                ->response()
                ->setStatusCode(201);
    }

    public function show(Categoria $categoria)
    {
        return new CategoriaResource($categoria);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());
        
        return new CategoriaResource($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        

        $categoria->delete();
        
        return response()->json(null, 204);
    }
}