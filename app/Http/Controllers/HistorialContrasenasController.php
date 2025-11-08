<?php

namespace App\Http\Controllers;

use App\Models\HistorialContrasena;
use App\Http\Resources\HistorialContrasenaResource;
use App\Http\Requests\StoreHistorialContrasenaRequest;
use App\Http\Requests\UpdateHistorialContrasenaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class HistorialContrasenasController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $query = HistorialContrasena::with(['usuario']);

        
        if ($request->has('id_usuario_fk')) {
            $query->where('id_usuario_fk', $request->id_usuario_fk);
        }

        
        if ($request->has('creado_por')) {
            $query->where('creado_por', $request->creado_por);
        }

        
        if ($request->has('fecha_creacion_desde')) {
            $query->where('fecha_creacion', '>=', $request->fecha_creacion_desde);
        }

        if ($request->has('fecha_creacion_hasta')) {
            $query->where('fecha_creacion', '<=', $request->fecha_creacion_hasta);
        }

        
        if ($request->has('fecha_modificacion_desde')) {
            $query->where('fecha_modificacion', '>=', $request->fecha_modificacion_desde);
        }

        if ($request->has('fecha_modificacion_hasta')) {
            $query->where('fecha_modificacion', '<=', $request->fecha_modificacion_hasta);
        }

        $historial = $query->orderBy('fecha_creacion', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => HistorialContrasenaResource::collection($historial->items()),
            'pagination' => [
                'current_page' => $historial->currentPage(),
                'per_page' => $historial->perPage(),
                'total' => $historial->total(),
                'last_page' => $historial->lastPage(),
                'from' => $historial->firstItem(),
                'to' => $historial->lastItem()
            ]
        ]);
    }

    
    public function store(StoreHistorialContrasenaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        
        $validated['contrasena'] = Hash::make($validated['contrasena']);

        $historial = HistorialContrasena::create($validated);
        $historial->load(['usuario']);

        return response()->json([
            'success' => true,
            'message' => 'Historial de contraseña creado exitosamente',
            'data' => new HistorialContrasenaResource($historial)
        ], 201);
    }

    
    public function show(string $id): JsonResponse
    {
        $historial = HistorialContrasena::with(['usuario'])->find($id);

        if (!$historial) {
            return response()->json([
                'success' => false,
                'message' => 'Historial de contraseña no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new HistorialContrasenaResource($historial)
        ]);
    }

    
    public function update(UpdateHistorialContrasenaRequest $request, string $id): JsonResponse
    {
        $historial = HistorialContrasena::find($id);

        if (!$historial) {
            return response()->json([
                'success' => false,
                'message' => 'Historial de contraseña no encontrado'
            ], 404);
        }

        $validated = $request->validated();

        
        if (isset($validated['contrasena'])) {
            $validated['contrasena'] = Hash::make($validated['contrasena']);
        }

        $historial->update($validated);
        $historial->load(['usuario']);

        return response()->json([
            'success' => true,
            'message' => 'Historial de contraseña actualizado exitosamente',
            'data' => new HistorialContrasenaResource($historial)
        ]);
    }

    
    public function destroy(string $id): JsonResponse
    {
        $historial = HistorialContrasena::find($id);

        if (!$historial) {
            return response()->json([
                'success' => false,
                'message' => 'Historial de contraseña no encontrado'
            ], 404);
        }

        $historial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Historial de contraseña eliminado exitosamente'
        ]);
    }
}
