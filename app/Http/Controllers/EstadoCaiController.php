<?php

namespace App\Http\Controllers;

use App\Models\EstadoCai;
use App\Http\Resources\EstadoCaiResource;
use App\Http\Requests\StoreEstadoCaiRequest;
use App\Http\Requests\UpdateEstadoCaiRequest;
use Illuminate\Http\Request;

class EstadoCaiController extends Controller
{
    public function index()
    {
        try {
            $estados = EstadoCai::all(); // Usar all() en lugar de paginate() para simplificar
            return response()->json([
                'success' => true,
                'data' => EstadoCaiResource::collection($estados)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los estados CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreEstadoCaiRequest $request)
    {
        try {
            $validated = $request->validated();
            $estado = EstadoCai::create([
                'codigo' => $validated['codigo_estado_cai'] ?? null,
                'nombre' => $validated['nombre_estado_cai'],
                'descripcion' => $validated['descripcion_estado_cai'] ?? null,
                'es_final' => $validated['es_final'] ?? false,
                'orden' => $validated['orden'] ?? 0,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Estado CAI creado exitosamente',
                'data' => new EstadoCaiResource($estado)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el estado CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(EstadoCai $estadoCai)
    {
        return new EstadoCaiResource($estadoCai);
    }

    public function update(UpdateEstadoCaiRequest $request, $id)
    {
        try {
            $estadoCai = EstadoCai::findOrFail($id);
            $validated = $request->validated();
            $updateData = [];
            
            if (isset($validated['codigo_estado_cai'])) {
                $updateData['codigo'] = $validated['codigo_estado_cai'];
            }
            if (isset($validated['nombre_estado_cai'])) {
                $updateData['nombre'] = $validated['nombre_estado_cai'];
            }
            if (isset($validated['descripcion_estado_cai'])) {
                $updateData['descripcion'] = $validated['descripcion_estado_cai'];
            }
            if (isset($validated['es_final'])) {
                $updateData['es_final'] = $validated['es_final'];
            }
            if (isset($validated['orden'])) {
                $updateData['orden'] = $validated['orden'];
            }
            
            $estadoCai->update($updateData);
            $estadoCai->refresh(); // Recargar el modelo desde la BD
            
            return response()->json([
                'success' => true,
                'message' => 'Estado CAI actualizado exitosamente',
                'data' => new EstadoCaiResource($estadoCai)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $estadoCai = EstadoCai::findOrFail($id);
            $deleted = $estadoCai->delete();
            
            if ($deleted) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Estado CAI eliminado exitosamente'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo eliminar el estado CAI'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el estado CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
