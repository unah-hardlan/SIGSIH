<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Resources\CategoriaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Categoria::query();

        // Filtros opcionales
        if ($request->has('tipo_categoria')) {
            $query->where('tipo_categoria', 'like', '%' . $request->tipo_categoria . '%');
        }

        if ($request->has('nombre_categoria')) {
            $query->where('nombre_categoria', 'like', '%' . $request->nombre_categoria . '%');
        }

        $categorias = $query->paginate(15);

        return CategoriaResource::collection($categorias);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipo_categoria' => 'required|string|max:255',
            'nombre_categoria' => 'required|string|max:255|unique:tbl_categorias,nombre_categoria'
        ]);

        $categoria = Categoria::create($validatedData);

        return new CategoriaResource($categoria);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $categoria = Categoria::findOrFail($id);
        return new CategoriaResource($categoria);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $validatedData = $request->validate([
            'tipo_categoria' => 'sometimes|required|string|max:255',
            'nombre_categoria' => 'sometimes|required|string|max:255|unique:tbl_categorias,nombre_categoria,' . $id . ',id_categoria_pk'
        ]);

        $categoria->update($validatedData);

        return new CategoriaResource($categoria);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente'
        ], Response::HTTP_OK);
    }
}
