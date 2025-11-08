<?php

namespace App\Http\Controllers;

use App\Models\TipoMantenimiento;
use App\Http\Resources\TipoMantenimientoResource;
use App\Http\Requests\StoreTipoMantenimientoRequest;
use App\Http\Requests\UpdateTipoMantenimientoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TipoMantenimientoController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $query = TipoMantenimiento::query();

        
        if ($request->has('tipo_mantenimiento')) {
            $query->where('tipo_mantenimiento', 'like', '%' . $request->tipo_mantenimiento . '%');
        }

        
        if ($request->has('descripcion_mantenimiento')) {
            $query->where('descripcion_mantenimiento', 'like', '%' . $request->descripcion_mantenimiento . '%');
        }

        $tiposMantenimiento = $query->orderBy('tipo_mantenimiento')
                                   ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => TipoMantenimientoResource::collection($tiposMantenimiento->items()),
            'pagination' => [
                'current_page' => $tiposMantenimiento->currentPage(),
                'per_page' => $tiposMantenimiento->perPage(),
                'total' => $tiposMantenimiento->total(),
                'last_page' => $tiposMantenimiento->lastPage(),
                'from' => $tiposMantenimiento->firstItem(),
                'to' => $tiposMantenimiento->lastItem()
            ]
        ]);
    }

    
    public function store(StoreTipoMantenimientoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $tipoMantenimiento = TipoMantenimiento::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de mantenimiento creado exitosamente',
            'data' => new TipoMantenimientoResource($tipoMantenimiento)
        ], 201);
    }

    
    public function show(string $id): JsonResponse
    {
        $tipoMantenimiento = TipoMantenimiento::find($id);

        if (!$tipoMantenimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de mantenimiento no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TipoMantenimientoResource($tipoMantenimiento)
        ]);
    }

    
    public function update(UpdateTipoMantenimientoRequest $request, string $id): JsonResponse
    {
        $tipoMantenimiento = TipoMantenimiento::find($id);

        if (!$tipoMantenimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de mantenimiento no encontrado'
            ], 404);
        }

        $validated = $request->validated();
        $tipoMantenimiento->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de mantenimiento actualizado exitosamente',
            'data' => new TipoMantenimientoResource($tipoMantenimiento)
        ]);
    }

    
    public function destroy(string $id): JsonResponse
    {
        $tipoMantenimiento = TipoMantenimiento::find($id);

        if (!$tipoMantenimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de mantenimiento no encontrado'
            ], 404);
        }

        $tipoMantenimiento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de mantenimiento eliminado exitosamente'
        ]);
    }
}
