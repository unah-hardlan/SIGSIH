<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Http\Resources\PaisResource;
use App\Http\Requests\StorePaisRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaisesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pais::query();

        // Filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre_pais', 'LIKE', "%{$search}%");
        }

        $paises = $query->orderBy('nombre_pais', 'asc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => PaisResource::collection($paises->items()),
            'pagination' => [
                'current_page' => $paises->currentPage(),
                'per_page' => $paises->perPage(),
                'total' => $paises->total(),
                'last_page' => $paises->lastPage(),
                'from' => $paises->firstItem(),
                'to' => $paises->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaisRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pais = Pais::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'País creado exitosamente',
            'data' => new PaisResource($pais)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $pais = Pais::find($id);

        if (!$pais) {
            return response()->json([
                'success' => false,
                'message' => 'País no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PaisResource($pais)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $pais = Pais::find($id);

        if (!$pais) {
            return response()->json([
                'success' => false,
                'message' => 'País no encontrado'
            ], 404);
        }

        // Verificar si el país tiene departamentos asociados
        if ($pais->departamentos()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el país porque tiene departamentos asociados'
            ], 400);
        }

        $pais->delete();

        return response()->json([
            'success' => true,
            'message' => 'País eliminado exitosamente'
        ]);
    }
}
