<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Http\Resources\CiudadResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CiudadesController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $query = Ciudad::with(['departamento.pais']);

        
        if ($request->has('id_departamento_fk')) {
            $query->where('id_departamento_fk', $request->id_departamento_fk);
        }

        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre_ciudad', 'LIKE', "%{$search}%");
        }

        $ciudades = $query->orderBy('nombre_ciudad', 'asc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => CiudadResource::collection($ciudades->items()),
            'pagination' => [
                'current_page' => $ciudades->currentPage(),
                'per_page' => $ciudades->perPage(),
                'total' => $ciudades->total(),
                'last_page' => $ciudades->lastPage(),
                'from' => $ciudades->firstItem(),
                'to' => $ciudades->lastItem()
            ]
        ]);
    }

    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_ciudad' => 'required|string|max:100',
            'id_departamento_fk' => 'required|exists:tbl_departamento,id_departamento_pk'
        ]);

        $ciudad = Ciudad::create($validated);
        $ciudad->load(['departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Ciudad creada exitosamente',
            'data' => new CiudadResource($ciudad)
        ], 201);
    }

    
    public function show(string $id): JsonResponse
    {
        $ciudad = Ciudad::with(['departamento.pais'])->find($id);

        if (!$ciudad) {
            return response()->json([
                'success' => false,
                'message' => 'Ciudad no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CiudadResource($ciudad)
        ]);
    }

    
    public function update(Request $request, string $id): JsonResponse
    {
        $ciudad = Ciudad::find($id);

        if (!$ciudad) {
            return response()->json([
                'success' => false,
                'message' => 'Ciudad no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'nombre_ciudad' => 'sometimes|required|string|max:100',
            'id_departamento_fk' => 'sometimes|required|exists:tbl_departamento,id_departamento_pk'
        ]);

        $ciudad->update($validated);
        $ciudad->load(['departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Ciudad actualizada exitosamente',
            'data' => new CiudadResource($ciudad)
        ]);
    }

    
    public function destroy(string $id): JsonResponse
    {
        $ciudad = Ciudad::find($id);

        if (!$ciudad) {
            return response()->json([
                'success' => false,
                'message' => 'Ciudad no encontrada'
            ], 404);
        }

        
        if ($ciudad->direcciones()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la ciudad porque tiene direcciones asociadas'
            ], 400);
        }

        $ciudad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ciudad eliminada exitosamente'
        ]);
    }
}
