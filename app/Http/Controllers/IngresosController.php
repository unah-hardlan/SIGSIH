<?php

namespace App\Http\Controllers;

use App\Models\Ingresos;
use App\Http\Resources\IngresosResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IngresosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ingresos::with(['proyecto', 'categoria']);

        // Filtros opcionales
        if ($request->has('id_proyecto_fk')) {
            $query->where('id_proyecto_fk', $request->id_proyecto_fk);
        }

        if ($request->has('id_categoria_fk')) {
            $query->where('id_categoria_fk', $request->id_categoria_fk);
        }

        if ($request->has('fecha_desde')) {
            $query->where('fecha_ingreso', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_ingreso', '<=', $request->fecha_hasta);
        }

        if ($request->has('monto_min')) {
            $query->where('monto_ingreso', '>=', $request->monto_min);
        }

        if ($request->has('monto_max')) {
            $query->where('monto_ingreso', '<=', $request->monto_max);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_ingreso', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion_ingreso', 'LIKE', "%{$search}%");
            });
        }

        $ingresos = $query->orderBy('fecha_ingreso', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => IngresosResource::collection($ingresos->items()),
            'pagination' => [
                'current_page' => $ingresos->currentPage(),
                'per_page' => $ingresos->perPage(),
                'total' => $ingresos->total(),
                'last_page' => $ingresos->lastPage(),
                'from' => $ingresos->firstItem(),
                'to' => $ingresos->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_ingreso' => 'required|string|max:100',
            'fecha_ingreso' => 'required|date',
            'monto_ingreso' => 'required|numeric|min:0',
            'descripcion_ingreso' => 'nullable|string|max:500',
            'id_proyecto_fk' => 'required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'required|exists:tbl_categorias,id_categoria_pk'
        ]);

        $ingreso = Ingresos::create($validated);
        $ingreso->load(['proyecto', 'categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Ingreso creado exitosamente',
            'data' => new IngresosResource($ingreso)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ingreso = Ingresos::with(['proyecto', 'categoria'])->find($id);

        if (!$ingreso) {
            return response()->json([
                'success' => false,
                'message' => 'Ingreso no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ingreso encontrado',
            'data' => new IngresosResource($ingreso)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ingreso = Ingresos::findOrFail($id);

        $validated = $request->validate([
            'nombre_ingreso' => 'sometimes|required|string|max:100',
            'fecha_ingreso' => 'sometimes|required|date',
            'monto_ingreso' => 'sometimes|required|numeric|min:0',
            'descripcion_ingreso' => 'nullable|string|max:500',
            'id_proyecto_fk' => 'sometimes|required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'sometimes|required|exists:tbl_categorias,id_categoria_pk'
        ]);

        $ingreso->update($validated);
        $ingreso->load(['proyecto', 'categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Ingreso actualizado exitosamente',
            'data' => new IngresosResource($ingreso)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ingreso = Ingresos::find($id);

        if (!$ingreso) {
            return response()->json([
                'success' => false,
                'message' => 'Ingreso no encontrado'
            ], 404);
        }

        $ingreso->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ingreso eliminado exitosamente'
        ], 200);
    }
}
