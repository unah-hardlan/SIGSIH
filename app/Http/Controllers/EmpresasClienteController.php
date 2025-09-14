<?php

namespace App\Http\Controllers;

use App\Models\EmpresaCliente;
use App\Http\Resources\EmpresaClienteResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmpresasClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = EmpresaCliente::with(['nombreEmpresa', 'direccion.ciudad.departamento.pais', 'oficina']);

        // Filtro por nombre empresa
        if ($request->has('id_nombre_empresa_fk')) {
            $query->where('id_nombre_empresa_fk', $request->id_nombre_empresa_fk);
        }

        // Filtro por oficina
        if ($request->has('id_oficina_fk')) {
            $query->where('id_oficina_fk', $request->id_oficina_fk);
        }

        // Filtro por rango de fechas
        if ($request->has('fecha_desde')) {
            $query->where('fecha_registro', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_registro', '<=', $request->fecha_hasta);
        }

        // Filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('nombreEmpresa', function ($q) use ($search) {
                $q->where('nombre_empresa', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por estado_empresa propio
        if ($request->has('estado_empresa')) {
            $estado = strtolower($request->estado_empresa);
            if (in_array($estado, ['activo', 'inactivo'])) {
                $query->where('estado_empresa', $estado);
            }
        }

        $empresasCliente = $query->orderBy('fecha_registro', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => EmpresaClienteResource::collection($empresasCliente->items()),
            'pagination' => [
                'current_page' => $empresasCliente->currentPage(),
                'per_page' => $empresasCliente->perPage(),
                'total' => $empresasCliente->total(),
                'last_page' => $empresasCliente->lastPage(),
                'from' => $empresasCliente->firstItem(),
                'to' => $empresasCliente->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha_registro' => 'required|date',
            'id_nombre_empresa_fk' => 'required|exists:tbl_nombre_empresa,id_nombre_empresa_pk',
            'id_direccion_fk' => 'required|exists:tbl_direccion,id_direccion_pk',
            'id_oficina_fk' => 'required|exists:tbl_oficina_empresa,id_oficina_empresa_pk',
            'estado_empresa' => 'sometimes|in:activo,inactivo'
        ]);

        if (!isset($validated['estado_empresa'])) {
            $validated['estado_empresa'] = 'activo';
        }

        $empresaCliente = EmpresaCliente::create($validated);
        $empresaCliente->load(['nombreEmpresa', 'direccion.ciudad.departamento.pais', 'oficina']);

        return response()->json([
            'success' => true,
            'message' => 'Empresa cliente creada exitosamente',
            'data' => new EmpresaClienteResource($empresaCliente)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $empresaCliente = EmpresaCliente::with(['nombreEmpresa', 'direccion.ciudad.departamento.pais', 'oficina'])->find($id);

        if (!$empresaCliente) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa cliente no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EmpresaClienteResource($empresaCliente)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $empresaCliente = EmpresaCliente::find($id);

        if (!$empresaCliente) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa cliente no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'fecha_registro' => 'sometimes|required|date',
            'id_nombre_empresa_fk' => 'sometimes|required|exists:tbl_nombre_empresa,id_nombre_empresa_pk',
            'id_direccion_fk' => 'sometimes|required|exists:tbl_direccion,id_direccion_pk',
            'id_oficina_fk' => 'sometimes|required|exists:tbl_oficina_empresa,id_oficina_empresa_pk',
            'estado_empresa' => 'sometimes|in:activo,inactivo'
        ]);

        $empresaCliente->update($validated);
        $empresaCliente->load(['nombreEmpresa', 'direccion.ciudad.departamento.pais', 'oficina']);

        return response()->json([
            'success' => true,
            'message' => 'Empresa cliente actualizada exitosamente',
            'data' => new EmpresaClienteResource($empresaCliente)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $empresaCliente = EmpresaCliente::find($id);

        if (!$empresaCliente) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa cliente no encontrada'
            ], 404);
        }

        $empresaCliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empresa cliente eliminada exitosamente'
        ]);
    }
}
