<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Http\Resources\GastoResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GastosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Gasto::with(['proyecto', 'categoria']);

        // Filtros opcionales
        if ($request->has('id_proyecto_fk')) {
            $query->where('id_proyecto_fk', $request->id_proyecto_fk);
        }

        if ($request->has('id_categoria_fk')) {
            $query->where('id_categoria_fk', $request->id_categoria_fk);
        }

        if ($request->has('fecha_desde')) {
            $query->where('fecha_gasto', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_gasto', '<=', $request->fecha_hasta);
        }

        if ($request->has('monto_min')) {
            $query->where('monto_gasto', '>=', $request->monto_min);
        }

        if ($request->has('monto_max')) {
            $query->where('monto_gasto', '<=', $request->monto_max);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_gasto', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion_gasto', 'LIKE', "%{$search}%");
            });
        }

        $gastos = $query->orderBy('fecha_gasto', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => GastoResource::collection($gastos->items()),
            'pagination' => [
                'current_page' => $gastos->currentPage(),
                'per_page' => $gastos->perPage(),
                'total' => $gastos->total(),
                'last_page' => $gastos->lastPage(),
                'from' => $gastos->firstItem(),
                'to' => $gastos->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_gasto' => 'required|string|max:255',
            'fecha_gasto' => 'required|date',
            'monto_gasto' => 'required|numeric|min:0',
            'descripcion_gasto' => 'nullable|string',
            'id_proyecto_fk' => 'required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'required|exists:tbl_categorias,id_categoria_pk'
        ]);

        $gasto = Gasto::create($validated);
        $gasto->load(['proyecto', 'categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Gasto creado exitosamente',
            'data' => new GastoResource($gasto)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $gasto = Gasto::with(['proyecto', 'categoria'])->find($id);

        if (!$gasto) {
            return response()->json([
                'success' => false,
                'message' => 'Gasto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new GastoResource($gasto)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json([
                'success' => false,
                'message' => 'Gasto no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'nombre_gasto' => 'sometimes|required|string|max:255',
            'fecha_gasto' => 'sometimes|required|date',
            'monto_gasto' => 'sometimes|required|numeric|min:0',
            'descripcion_gasto' => 'nullable|string',
            'id_proyecto_fk' => 'sometimes|required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'sometimes|required|exists:tbl_categorias,id_categoria_pk'
        ]);

        $gasto->update($validated);
        $gasto->load(['proyecto', 'categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Gasto actualizado exitosamente',
            'data' => new GastoResource($gasto)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json([
                'success' => false,
                'message' => 'Gasto no encontrado'
            ], 404);
        }

        $gasto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gasto eliminado exitosamente'
        ]);
    }
}
