<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Permiso::all(), 200);
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
            'id_rol_fk' => 'required|integer|exists:tbl_ms_rol,id_rol_pk',
            'id_objeto_fk' => 'required|integer|exists:tbl_objetos,id_objetos_pk',
            'permiso_insercion' => 'required|boolean',
            'permiso_consultar' => 'required|boolean',
            'permiso_actualiza' => 'required|boolean',
            'permiso_eliminado' => 'required|boolean',
        ]);

        $permiso = Permiso::create($validated);

        return response()->json($permiso, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['error' => 'Permiso no encontrado'], 404);
        }
        return response()->json($permiso, 200);
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
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['error' => 'Permiso no encontrado'], 404);
        }

        $validated = $request->validate([
            'permiso_insercion' => 'boolean',
            'permiso_consultar' => 'boolean',
            'permiso_actualiza' => 'boolean',
            'permiso_eliminado' => 'boolean',
        ]);

        $permiso->update($validated);

        return response()->json($permiso, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['error' => 'Permiso no encontrado'], 404);
        }

        $permiso->delete();

        return response()->json(['message' => 'Permiso eliminado'], 200);
    }
}
