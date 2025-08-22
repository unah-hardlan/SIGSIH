<?php

namespace App\Http\Controllers;

use App\Models\OficinaEmpresa;
use App\Http\Resources\OficinaEmpresaResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OficinasEmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = OficinaEmpresa::query();

        // Filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre_oficina', 'LIKE', "%{$search}%");
        }

        $oficinas = $query->orderBy('nombre_oficina', 'asc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => OficinaEmpresaResource::collection($oficinas->items()),
            'pagination' => [
                'current_page' => $oficinas->currentPage(),
                'per_page' => $oficinas->perPage(),
                'total' => $oficinas->total(),
                'last_page' => $oficinas->lastPage(),
                'from' => $oficinas->firstItem(),
                'to' => $oficinas->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_oficina' => 'required|string|max:100|unique:tbl_oficina_empresa,nombre_oficina'
        ]);

        $oficina = OficinaEmpresa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Oficina creada exitosamente',
            'data' => new OficinaEmpresaResource($oficina)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $oficina = OficinaEmpresa::find($id);

        if (!$oficina) {
            return response()->json([
                'success' => false,
                'message' => 'Oficina no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new OficinaEmpresaResource($oficina)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $oficina = OficinaEmpresa::find($id);

        if (!$oficina) {
            return response()->json([
                'success' => false,
                'message' => 'Oficina no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'nombre_oficina' => 'sometimes|required|string|max:100|unique:tbl_oficina_empresa,nombre_oficina,' . $id . ',id_oficina_empresa_pk'
        ]);

        $oficina->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Oficina actualizada exitosamente',
            'data' => new OficinaEmpresaResource($oficina)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $oficina = OficinaEmpresa::find($id);

        if (!$oficina) {
            return response()->json([
                'success' => false,
                'message' => 'Oficina no encontrada'
            ], 404);
        }

        $oficina->delete();

        return response()->json([
            'success' => true,
            'message' => 'Oficina eliminada exitosamente'
        ]);
    }
}
