<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Http\Resources\DireccionResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DireccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Direccion::with(['ciudad.departamento.pais']);

        // Filtro por ciudad
        if ($request->has('id_ciudad_fk')) {
            $query->where('id_ciudad_fk', $request->id_ciudad_fk);
        }

        $direcciones = $query->orderBy('id_direccion_pk', 'asc')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => DireccionResource::collection($direcciones->items()),
            'pagination' => [
                'current_page' => $direcciones->currentPage(),
                'per_page' => $direcciones->perPage(),
                'total' => $direcciones->total(),
                'last_page' => $direcciones->lastPage(),
                'from' => $direcciones->firstItem(),
                'to' => $direcciones->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_ciudad_fk' => 'required|exists:tbl_ciudad,id_ciudad_pk',
            'calle' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'colonia' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'referencia' => 'nullable|string'
        ]);

        $direccion = Direccion::create($validated);
        $direccion->load(['ciudad.departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Dirección creada exitosamente',
            'data' => new DireccionResource($direccion)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $direccion = Direccion::with(['ciudad.departamento.pais'])->find($id);

        if (!$direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new DireccionResource($direccion)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $direccion = Direccion::find($id);

        if (!$direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'id_ciudad_fk' => 'sometimes|required|exists:tbl_ciudad,id_ciudad_pk',
            'calle' => 'sometimes|required|string|max:100',
            'numero' => 'sometimes|required|string|max:20',
            'colonia' => 'sometimes|required|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'referencia' => 'nullable|string'
        ]);

        $direccion->update($validated);
        $direccion->load(['ciudad.departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada exitosamente',
            'data' => new DireccionResource($direccion)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $direccion = Direccion::find($id);

        if (!$direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Dirección no encontrada'
            ], 404);
        }

        $direccion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada exitosamente'
        ]);
    }
}
