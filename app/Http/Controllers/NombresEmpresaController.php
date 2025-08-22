<?php

namespace App\Http\Controllers;

use App\Models\NombreEmpresa;
use App\Http\Resources\NombreEmpresaResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NombresEmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = NombreEmpresa::query();

        // Filtro por estado
        if ($request->has('estado_empresa')) {
            $query->where('estado_empresa', $request->estado_empresa);
        }

        // Filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_empresa', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion_empresa', 'LIKE', "%{$search}%");
            });
        }

        $empresas = $query->orderBy('nombre_empresa', 'asc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => NombreEmpresaResource::collection($empresas->items()),
            'pagination' => [
                'current_page' => $empresas->currentPage(),
                'per_page' => $empresas->perPage(),
                'total' => $empresas->total(),
                'last_page' => $empresas->lastPage(),
                'from' => $empresas->firstItem(),
                'to' => $empresas->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_empresa' => 'required|string|max:100|unique:tbl_nombre_empresa,nombre_empresa',
            'descripcion_empresa' => 'nullable|string|max:255',
            'estado_empresa' => 'required|string|max:20|in:activo,inactivo'
        ]);

        $empresa = NombreEmpresa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Empresa creada exitosamente',
            'data' => new NombreEmpresaResource($empresa)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $empresa = NombreEmpresa::find($id);

        if (!$empresa) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new NombreEmpresaResource($empresa)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $empresa = NombreEmpresa::find($id);

        if (!$empresa) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'nombre_empresa' => 'sometimes|required|string|max:100|unique:tbl_nombre_empresa,nombre_empresa,' . $id . ',id_nombre_empresa_pk',
            'descripcion_empresa' => 'nullable|string|max:255',
            'estado_empresa' => 'sometimes|required|string|max:20|in:activo,inactivo'
        ]);

        $empresa->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Empresa actualizada exitosamente',
            'data' => new NombreEmpresaResource($empresa)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $empresa = NombreEmpresa::find($id);

        if (!$empresa) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada'
            ], 404);
        }

        $empresa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empresa eliminada exitosamente'
        ]);
    }
}
