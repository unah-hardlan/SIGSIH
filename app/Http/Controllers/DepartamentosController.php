<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Http\Resources\DepartamentoResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartamentosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Departamento::with(['pais']);

        // Filtro por país
        if ($request->has('id_pais_fk')) {
            $query->where('id_pais_fk', $request->id_pais_fk);
        }

        // Filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre_departamento', 'LIKE', "%{$search}%");
        }

        $departamentos = $query->orderBy('nombre_departamento', 'asc')
                             ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => DepartamentoResource::collection($departamentos->items()),
            'pagination' => [
                'current_page' => $departamentos->currentPage(),
                'per_page' => $departamentos->perPage(),
                'total' => $departamentos->total(),
                'last_page' => $departamentos->lastPage(),
                'from' => $departamentos->firstItem(),
                'to' => $departamentos->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_departamento' => 'required|string|max:100',
            'id_pais_fk' => 'required|exists:tbl_pais,id_pais_pk'
        ]);

        $departamento = Departamento::create($validated);
        $departamento->load(['pais']);

        return response()->json([
            'success' => true,
            'message' => 'Departamento creado exitosamente',
            'data' => new DepartamentoResource($departamento)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $departamento = Departamento::with(['pais'])->find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new DepartamentoResource($departamento)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'nombre_departamento' => 'sometimes|required|string|max:100',
            'id_pais_fk' => 'sometimes|required|exists:tbl_pais,id_pais_pk'
        ]);

        $departamento->update($validated);
        $departamento->load(['pais']);

        return response()->json([
            'success' => true,
            'message' => 'Departamento actualizado exitosamente',
            'data' => new DepartamentoResource($departamento)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ], 404);
        }

        $departamento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departamento eliminado exitosamente'
        ]);
    }
}
