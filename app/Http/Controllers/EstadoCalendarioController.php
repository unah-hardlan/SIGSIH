<?php

namespace App\Http\Controllers;

use App\Models\EstadoCalendario;
use App\Http\Resources\EstadoCalendarioResource;
use App\Http\Requests\StoreEstadoCalendarioRequest;
use App\Http\Requests\UpdateEstadoCalendarioRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstadoCalendarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EstadoCalendario::query();

        // Filtro por nombre del estado
        if ($request->has('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        // Filtro por descripción
        if ($request->has('descripcion')) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }

        $estadosCalendario = $query->orderBy('orden')
                                  ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => EstadoCalendarioResource::collection($estadosCalendario->items()),
            'pagination' => [
                'current_page' => $estadosCalendario->currentPage(),
                'total' => $estadosCalendario->total(),
                'per_page' => $estadosCalendario->perPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEstadoCalendarioRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $estadoCalendario = EstadoCalendario::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Estado de calendario creado exitosamente',
            'data' => new EstadoCalendarioResource($estadoCalendario)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $estadoCalendario = EstadoCalendario::find($id);

        if (!$estadoCalendario) {
            return response()->json([
                'success' => false,
                'message' => 'Estado de calendario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EstadoCalendarioResource($estadoCalendario)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEstadoCalendarioRequest $request, string $id): JsonResponse
    {
        $estadoCalendario = EstadoCalendario::find($id);

        if (!$estadoCalendario) {
            return response()->json([
                'success' => false,
                'message' => 'Estado de calendario no encontrado'
            ], 404);
        }

        $validated = $request->validated();
        $estadoCalendario->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Estado de calendario actualizado exitosamente',
            'data' => new EstadoCalendarioResource($estadoCalendario)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $estadoCalendario = EstadoCalendario::find($id);

        if (!$estadoCalendario) {
            return response()->json([
                'success' => false,
                'message' => 'Estado de calendario no encontrado'
            ], 404);
        }

        $estadoCalendario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estado de calendario eliminado exitosamente'
        ]);
    }
}
