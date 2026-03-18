<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EmpresaCliente;
use App\Http\Resources\EmpresaClienteResource;
use App\Http\Requests\StoreEmpresaClienteRequest;
use App\Http\Requests\UpdateEmpresaClienteRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmpresasClienteController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $query = EmpresaCliente::with(['cliente', 'contactos']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_comercial', 'like', "%{$search}%")
                    ->orWhere('razon_social', 'like', "%{$search}%")
                    ->orWhere('rtn', 'like', "%{$search}%");
            });
        }

        if ($estado = $request->input('estado_cliente', $request->input('estado_empresa'))) {
            $estado = strtolower($estado);
            if (in_array($estado, ['activo', 'inactivo'])) {
                $query->whereHas('cliente', function ($clienteQuery) use ($estado) {
                    $clienteQuery->where('estado_cliente', $estado);
                });
            }
        }

        if ($desde = $request->input('fecha_desde')) {
            $query->whereHas('cliente', function ($clienteQuery) use ($desde) {
                $clienteQuery->whereDate('fecha_registro', '>=', $desde);
            });
        }

        if ($hasta = $request->input('fecha_hasta')) {
            $query->whereHas('cliente', function ($clienteQuery) use ($hasta) {
                $clienteQuery->whereDate('fecha_registro', '<=', $hasta);
            });
        }

        $query->orderBy('nombre_comercial');

        $perPage = (int) $request->input('per_page', 15);
        if ($perPage === -1) {
            $empresas = $query->get();
            return response()->json([
                'success' => true,
                'data' => EmpresaClienteResource::collection($empresas),
                'pagination' => null,
            ]);
        }

        $empresas = $query->paginate(max(1, $perPage));

            return response()->json([
                'success' => true,
                'data' => EmpresaClienteResource::collection($empresas),
            'pagination' => [
                'current_page' => $empresas->currentPage(),
                'per_page' => $empresas->perPage(),
                'total' => $empresas->total(),
                'last_page' => $empresas->lastPage(),
                'from' => $empresas->firstItem(),
                'to' => $empresas->lastItem(),
            ],
        ]);
    }

    
    public function store(StoreEmpresaClienteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $fechaRegistro = $validated['fecha_registro'] ?? Carbon::now()->toDateString();
        $estado = $validated['estado_cliente'] ?? 'activo';

        $cliente = Cliente::create([
            'tipo_cliente' => 'empresa',
            'fecha_registro' => $fechaRegistro,
            'estado_cliente' => $estado,
        ]);

        $empresaCliente = EmpresaCliente::create([
            'id_cliente_fk' => $cliente->id_cliente_pk,
            'nombre_comercial' => $validated['nombre_comercial'],
            'razon_social' => $validated['razon_social'] ?? null,
            'rtn' => $validated['rtn'] ?? null,
            'descripcion_empresa' => $validated['descripcion_empresa'] ?? null,
            'horario_atencion' => $validated['horario_atencion'] ?? null,
        ]);

        $empresaCliente->load(['cliente', 'contactos']);

        return response()->json([
            'success' => true,
            'message' => 'Empresa cliente creada exitosamente',
            'data' => new EmpresaClienteResource($empresaCliente),
        ], 201);
    }

    
    public function show(string $id): JsonResponse
    {
        $empresaCliente = EmpresaCliente::with(['cliente', 'contactos'])->find($id);

        if (!$empresaCliente) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa cliente no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EmpresaClienteResource($empresaCliente),
        ]);
    }

    
    public function update(UpdateEmpresaClienteRequest $request, string $id): JsonResponse
    {
        $empresaCliente = EmpresaCliente::with('cliente')->find($id);

        if (!$empresaCliente) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa cliente no encontrada',
            ], 404);
        }

        $validated = $request->validated();

        $empresaCliente->fill(array_intersect_key($validated, array_flip([
            'nombre_comercial',
            'razon_social',
            'rtn',
            'descripcion_empresa',
            'horario_atencion',
        ])));
        $empresaCliente->save();

        if ($empresaCliente->cliente) {
            if (array_key_exists('fecha_registro', $validated)) {
                $empresaCliente->cliente->fecha_registro = Carbon::parse($validated['fecha_registro']);
            }
            if (array_key_exists('estado_cliente', $validated)) {
                $empresaCliente->cliente->estado_cliente = strtolower($validated['estado_cliente']);
            }
            $empresaCliente->cliente->save();
        }

        $empresaCliente->load(['cliente', 'contactos']);

        return response()->json([
            'success' => true,
            'message' => 'Empresa cliente actualizada exitosamente',
            'data' => new EmpresaClienteResource($empresaCliente),
        ]);
    }

    
    public function destroy(string $id): JsonResponse
    {
        $empresaCliente = EmpresaCliente::with('cliente')->find($id);

        if (!$empresaCliente) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa cliente no encontrada',
            ], 404);
        }

        
        if ($empresaCliente->cliente) {
            $empresaCliente->cliente->delete();
        } else {
            $empresaCliente->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Empresa cliente eliminada exitosamente',
        ]);
    }
}
