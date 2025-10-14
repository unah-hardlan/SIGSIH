<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Http\Resources\OrdenServicioResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrdenServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OrdenServicio::with([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ]);

        // Filtros opcionales
        if ($request->has('id_solicitud_servicio_fk')) {
            $query->where('id_solicitud_servicio_fk', $request->id_solicitud_servicio_fk);
        }

        if ($request->has('id_tecnico_fk')) {
            $query->where('id_tecnico_fk', $request->id_tecnico_fk);
        }

        if ($request->has('fecha_recepcion')) {
            $query->whereDate('fecha_recepcion', $request->fecha_recepcion);
        }

        $ordenesServicio = $query->paginate(15);

        return OrdenServicioResource::collection($ordenesServicio);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_solicitud_servicio_fk' => 'required|integer|exists:tbl_solicitud,id_solicitud_pk',
            'id_tecnico_fk' => 'required|integer|exists:tbl_persona,id_persona_pk',
            'numero_orden_servicio' => 'nullable|string|max:50',
            'fecha_creada' => 'nullable|date',
            'fecha_asignada' => 'nullable|date',
            'fecha_recepcion' => 'required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'observaciones' => 'nullable|string|max:500',
            'diagnostico_tecnico' => 'nullable|string|max:500',
            'diagnostico_cliente' => 'nullable|string|max:500',
            'id_estado_orden_servicio_fk' => 'nullable|integer|exists:tbl_estado_orden_servicio,id_estado_orden_servicio_pk',
            'id_cotizacion_fk' => 'nullable|integer|exists:tbl_cotizacion,id_cotizacion_pk'
        ]);

        $ordenServicio = OrdenServicio::create($validatedData);
        $ordenServicio->load([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ]);

        return new OrdenServicioResource($ordenServicio);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ordenServicio = OrdenServicio::with([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ])->findOrFail($id);
        return new OrdenServicioResource($ordenServicio);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ordenServicio = OrdenServicio::findOrFail($id);

        $validatedData = $request->validate([
            'id_solicitud_servicio_fk' => 'sometimes|required|integer|exists:tbl_solicitud,id_solicitud_pk',
            'id_tecnico_fk' => 'sometimes|required|integer|exists:tbl_persona,id_persona_pk',
            'numero_orden_servicio' => 'nullable|string|max:50',
            'fecha_creada' => 'nullable|date',
            'fecha_asignada' => 'nullable|date',
            'fecha_recepcion' => 'sometimes|required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'observaciones' => 'nullable|string|max:500',
            'diagnostico_tecnico' => 'nullable|string|max:500',
            'diagnostico_cliente' => 'nullable|string|max:500',
            'id_estado_orden_servicio_fk' => 'nullable|integer|exists:tbl_estado_orden_servicio,id_estado_orden_servicio_pk',
            'id_cotizacion_fk' => 'nullable|integer|exists:tbl_cotizacion,id_cotizacion_pk'
        ]);

        $ordenServicio->update($validatedData);
        $ordenServicio->load([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ]);

        return new OrdenServicioResource($ordenServicio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ordenServicio = OrdenServicio::findOrFail($id);
        $ordenServicio->delete();

        return response()->json([
            'message' => 'Orden de servicio eliminada correctamente'
        ], Response::HTTP_OK);
    }
}