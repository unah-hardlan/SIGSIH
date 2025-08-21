<?php

namespace App\Http\Controllers;

use App\Models\Ingresos;
use App\Http\Resources\IngresosResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IngresosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ingresos::with(['proyecto', 'categoria']);

        // Filtros opcionales
        if ($request->has('id_proyecto_fk')) {
            $query->where('id_proyecto_fk', $request->id_proyecto_fk);
        }

        if ($request->has('id_categoria_fk')) {
            $query->where('id_categoria_fk', $request->id_categoria_fk);
        }

        if ($request->has('fecha_ingreso')) {
            $query->whereDate('fecha_ingreso', $request->fecha_ingreso);
        }

        if ($request->has('nombre_ingreso')) {
            $query->where('nombre_ingreso', 'like', '%' . $request->nombre_ingreso . '%');
        }

        $ingresos = $query->paginate(15);

        return IngresosResource::collection($ingresos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre_ingreso' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'monto_ingreso' => 'required|numeric|min:0',
            'descripcion_ingreso' => 'nullable|string|max:500',
            'id_proyecto_fk' => 'required|integer|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'required|integer|exists:tbl_categorias,id_categoria_pk'
        ]);

        $ingreso = Ingresos::create($validatedData);
        $ingreso->load(['proyecto', 'categoria']);

        return new IngresosResource($ingreso);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ingreso = Ingresos::with(['proyecto', 'categoria'])->findOrFail($id);
        return new IngresosResource($ingreso);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ingreso = Ingresos::findOrFail($id);

        $validatedData = $request->validate([
            'nombre_ingreso' => 'sometimes|required|string|max:255',
            'fecha_ingreso' => 'sometimes|required|date',
            'monto_ingreso' => 'sometimes|required|numeric|min:0',
            'descripcion_ingreso' => 'nullable|string|max:500',
            'id_proyecto_fk' => 'sometimes|required|integer|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'sometimes|required|integer|exists:tbl_categorias,id_categoria_pk'
        ]);

        $ingreso->update($validatedData);
        $ingreso->load(['proyecto', 'categoria']);

        return new IngresosResource($ingreso);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ingreso = Ingresos::findOrFail($id);
        $ingreso->delete();

        return response()->json([
            'message' => 'Ingreso eliminado correctamente'
        ], Response::HTTP_OK);
    }
}
