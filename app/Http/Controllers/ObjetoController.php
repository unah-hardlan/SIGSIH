<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use Illuminate\Http\Request;

class ObjetoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Objeto::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_objeto' => 'required|string|max:100',
            'descripcion_objeto' => 'nullable|string|max:255',
            'id_tipo_objetos_fk' => 'required|integer',
        ]);

        $objeto = Objeto::create($validated);

        return response()->json($objeto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) {
            return response()->json(['error' => 'Objeto no encontrado'], 404);
        }
        return response()->json($objeto, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) {
            return response()->json(['error' => 'Objeto no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre_objeto' => 'string|max:100',
            'descripcion_objeto' => 'nullable|string|max:255',
        ]);

        $objeto->update($validated);

        return response()->json($objeto, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $objeto = Objeto::find($id);
        if (!$objeto) {
            return response()->json(['error' => 'Objeto no encontrado'], 404);
        }

        $objeto->delete();

        return response()->json(['message' => 'Objeto eliminado'], 200);
    }
}
