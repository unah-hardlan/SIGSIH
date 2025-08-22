<?php

namespace App\Http\Controllers;

use App\Models\EstadoProyecto;
use App\Http\Resources\EstadoProyectoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EstadoProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EstadoProyecto::query();

        // Filtro opcional por nombre
        if ($request->has('nombre_estado')) {
            $query->where('nombre_estado', 'like', '%' . $request->nombre_estado . '%');
        }

        $estadosProyecto = $query->paginate(15);

        return EstadoProyectoResource::collection($estadosProyecto);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre_estado' => 'required|string|max:50|unique:tbl_estado_proyecto,nombre_estado',
            'descripcion_estado_proyecto' => 'nullable|string|max:250'
        ]);

        $estadoProyecto = EstadoProyecto::create($validatedData);

        return new EstadoProyectoResource($estadoProyecto);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $estadoProyecto = EstadoProyecto::findOrFail($id);
        return new EstadoProyectoResource($estadoProyecto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $estadoProyecto = EstadoProyecto::findOrFail($id);

        $validatedData = $request->validate([
            'nombre_estado' => 'sometimes|required|string|max:50|unique:tbl_estado_proyecto,nombre_estado,' . $id . ',id_estado_proyecto_pk',
            'descripcion_estado_proyecto' => 'nullable|string|max:250'
        ]);

        $estadoProyecto->update($validatedData);

        return new EstadoProyectoResource($estadoProyecto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $estadoProyecto = EstadoProyecto::findOrFail($id);
        $estadoProyecto->delete();

        return response()->json([
            'message' => 'Estado de proyecto eliminado correctamente'
        ], Response::HTTP_OK);
    }
}
