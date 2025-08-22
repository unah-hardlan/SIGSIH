<?php

namespace App\Http\Controllers;

use App\Models\Agencia;
use App\Http\Resources\AgenciaResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Agencia::with(['direccion.ciudad.departamento.pais']);

        // Filtro por dirección
        if ($request->has('id_direccion_fk')) {
            $query->where('id_direccion_fk', $request->id_direccion_fk);
        }

        // Filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_agencia', 'LIKE', "%{$search}%")
                  ->orWhere('horario_agencia', 'LIKE', "%{$search}%");
            });
        }

        $agencias = $query->orderBy('nombre_agencia', 'asc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => AgenciaResource::collection($agencias->items()),
            'pagination' => [
                'current_page' => $agencias->currentPage(),
                'per_page' => $agencias->perPage(),
                'total' => $agencias->total(),
                'last_page' => $agencias->lastPage(),
                'from' => $agencias->firstItem(),
                'to' => $agencias->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_agencia' => 'required|string|max:100|unique:tbl_agencias,nombre_agencia',
            'horario_agencia' => 'required|string|max:50',
            'id_direccion_fk' => 'required|exists:tbl_direccion,id_direccion_pk'
        ]);

        $agencia = Agencia::create($validated);
        $agencia->load(['direccion.ciudad.departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Agencia creada exitosamente',
            'data' => new AgenciaResource($agencia)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $agencia = Agencia::with(['direccion.ciudad.departamento.pais'])->find($id);

        if (!$agencia) {
            return response()->json([
                'success' => false,
                'message' => 'Agencia no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AgenciaResource($agencia)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $agencia = Agencia::find($id);

        if (!$agencia) {
            return response()->json([
                'success' => false,
                'message' => 'Agencia no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'nombre_agencia' => 'sometimes|required|string|max:100|unique:tbl_agencias,nombre_agencia,' . $id . ',id_agencias_pk',
            'horario_agencia' => 'sometimes|required|string|max:50',
            'id_direccion_fk' => 'sometimes|required|exists:tbl_direccion,id_direccion_pk'
        ]);

        $agencia->update($validated);
        $agencia->load(['direccion.ciudad.departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Agencia actualizada exitosamente',
            'data' => new AgenciaResource($agencia)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $agencia = Agencia::find($id);

        if (!$agencia) {
            return response()->json([
                'success' => false,
                'message' => 'Agencia no encontrada'
            ], 404);
        }

        $agencia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agencia eliminada exitosamente'
        ]);
    }
}
