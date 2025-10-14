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

        if ($q = $request->input('q')) {
            $query->where('nombre_categoria', 'like', "%$q%")
                  ->orWhere('descripcion_categoria', 'like', "%$q%");
        }

        $sortable = [
            'nombre_categoria' => 'nombre_categoria',
            'id_categoria_pk'     => 'id_categoria_pk',
        ];

        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
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
        // $request->validated() ya contiene 'nombre_categoria' y 'descripcion_categoria'
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
        // Asegúrate que los modelos Ingreso y Gasto y sus relaciones están definidos
        // if ($categoria->ingresos()->exists() || $categoria->gastos()->exists()) {
        //     return response()->json([
        //         'message' => 'No se puede eliminar la categoría porque tiene movimientos asociados.'
        //     ], 422);
        // }

        $categoria->delete();
        
        return response()->json(null, 204);
    }
}