<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Http\Resources\ProyectoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proyecto::with(['ordenServicio', 'estadoProyecto']);

        // Filtros opcionales
        if ($request->has('id_orden_servicio_fk')) {
            $query->where('id_orden_servicio_fk', $request->id_orden_servicio_fk);
        }

        if ($request->has('id_estado_proyecto_fk')) {
            $query->where('id_estado_proyecto_fk', $request->id_estado_proyecto_fk);
        }

        if ($request->has('nombre_proyecto')) {
            $query->where('nombre_proyecto', 'like', '%' . $request->nombre_proyecto . '%');
        }

        $proyectos = $query->paginate(15);

        return ProyectoResource::collection($proyectos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre_proyecto' => 'required|string|max:100',
            'fecha_inicio_proyecto' => 'required|date',
            'fecha_estimada_fin_proyecto' => 'nullable|date',
            'fecha_finalizacion_proyecto' => 'nullable|date',
            'descripcion_proyecto' => 'nullable|string|max:500',
            'actividades_proyecto' => 'nullable|string|max:500',
            'id_orden_servicio_fk' => 'required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_estado_proyecto_fk' => 'required|integer|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ]);

        $proyecto = Proyecto::create($validatedData);
        $proyecto->load(['ordenServicio', 'estadoProyecto']);

        return new ProyectoResource($proyecto);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $proyecto = Proyecto::with(['ordenServicio', 'estadoProyecto'])->findOrFail($id);
        return new ProyectoResource($proyecto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);

        $validatedData = $request->validate([
            'nombre_proyecto' => 'sometimes|required|string|max:100',
            'fecha_inicio_proyecto' => 'sometimes|required|date',
            'fecha_estimada_fin_proyecto' => 'nullable|date',
            'fecha_finalizacion_proyecto' => 'nullable|date',
            'descripcion_proyecto' => 'nullable|string|max:500',
            'actividades_proyecto' => 'nullable|string|max:500',
            'id_orden_servicio_fk' => 'sometimes|required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_estado_proyecto_fk' => 'sometimes|required|integer|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ]);

        $proyecto->update($validatedData);
        $proyecto->load(['ordenServicio', 'estadoProyecto']);

        return new ProyectoResource($proyecto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente'
        ], Response::HTTP_OK);
    }
}
