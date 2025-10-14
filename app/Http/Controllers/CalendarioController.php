<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Http\Resources\CalendarioResource;
use App\Http\Requests\StoreCalendarioRequest;
use App\Http\Requests\UpdateCalendarioRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CalendarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Calendario::with([
            'estado',
            'agencia.direccion.ciudad.departamento.pais',
            'ordenServicio',
            'tipoMantenimiento',
            'cliente'
        ]);

        // Filtro por estado del calendario
        if ($request->has('id_estado_calendario_fk')) {
            $query->where('id_estado_calendario_fk', $request->id_estado_calendario_fk);
        }

        // Filtro por agencia
        if ($request->has('id_agencias_fk')) {
            $query->where('id_agencias_fk', $request->id_agencias_fk);
        }

        // Filtro por orden de servicio
        if ($request->has('id_orden_servicio_fk')) {
            $query->where('id_orden_servicio_fk', $request->id_orden_servicio_fk);
        }

        // Filtro por tipo de mantenimiento
        if ($request->has('id_tipo_mantenimiento_fk')) {
            $query->where('id_tipo_mantenimiento_fk', $request->id_tipo_mantenimiento_fk);
        }

        // Filtro por cliente
        if ($request->has('id_cliente_fk')) {
            $query->where('id_cliente_fk', $request->id_cliente_fk);
        }

        // Filtro por descripción
        if ($request->has('descripcion_calendario')) {
            $query->where('descripcion_calendario', 'like', '%' . $request->descripcion_calendario . '%');
        }

        // Filtro por rango de fechas
        if ($request->has('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

    $calendarios = $query->orderBy('fecha', 'desc')
                            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => CalendarioResource::collection($calendarios->items()),
            'pagination' => [
                'current_page' => $calendarios->currentPage(),
                'per_page' => $calendarios->perPage(),
                'total' => $calendarios->total(),
                'last_page' => $calendarios->lastPage(),
                'from' => $calendarios->firstItem(),
                'to' => $calendarios->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarioRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $calendario = Calendario::create($validated);
        $calendario->load(['estado', 'agencia', 'ordenServicio', 'tipoMantenimiento', 'cliente']);

        return response()->json([
            'success' => true,
            'message' => 'Evento de calendario creado exitosamente',
            'data' => new CalendarioResource($calendario)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $calendario = Calendario::with(['estado', 'agencia', 'ordenServicio', 'tipoMantenimiento', 'cliente'])->find($id);

        if (!$calendario) {
            return response()->json([
                'success' => false,
                'message' => 'Evento de calendario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CalendarioResource($calendario)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarioRequest $request, string $id): JsonResponse
    {
        $calendario = Calendario::find($id);

        if (!$calendario) {
            return response()->json([
                'success' => false,
                'message' => 'Evento de calendario no encontrado'
            ], 404);
        }

        $validated = $request->validated();
        $calendario->update($validated);
        $calendario->load(['estado', 'agencia', 'ordenServicio', 'tipoMantenimiento', 'cliente']);

        return response()->json([
            'success' => true,
            'message' => 'Evento de calendario actualizado exitosamente',
            'data' => new CalendarioResource($calendario)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $calendario = Calendario::find($id);

        if (!$calendario) {
            return response()->json([
                'success' => false,
                'message' => 'Evento de calendario no encontrado'
            ], 404);
        }

        $calendario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento de calendario eliminado exitosamente'
        ]);
    }
}