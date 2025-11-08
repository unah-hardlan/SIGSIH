<?php

namespace App\Http\Controllers;

use App\Models\Agencia;
use App\Http\Resources\AgenciaResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgenciasController extends Controller
{
  
    public function index(Request $request): JsonResponse
    {
    $query = Agencia::with(['direccion.ciudad.departamento.pais', 'clientes.empresa', 'clientes.personas']);

        
        if ($request->filled('id_direccion_fk')) {
            $query->where('id_direccion_fk', $request->integer('id_direccion_fk'));
        }

        
        if ($request->filled('id_ciudad_fk')) {
            $query->whereHas('direccion', function ($q) use ($request) {
                $q->where('id_ciudad_fk', $request->integer('id_ciudad_fk'));
            });
        }
        if ($request->filled('ciudad_nombre')) {
            $name = $request->input('ciudad_nombre');
            $query->whereHas('direccion.ciudad', function ($q) use ($name) {
                $q->where('nombre_ciudad', 'LIKE', "%{$name}%");
            });
        }
        if ($request->filled('id_departamento_fk')) {
            $query->whereHas('direccion.ciudad', function ($q) use ($request) {
                $q->where('id_departamento_fk', $request->integer('id_departamento_fk'));
            });
        }
        if ($request->filled('id_pais_pk')) {
            $query->whereHas('direccion.ciudad.departamento', function ($q) use ($request) {
                $q->where('id_pais_pk', $request->integer('id_pais_pk'));
            });
        }

        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre_agencia', 'LIKE', "%{$search}%")
                    ->orWhere('horario_agencia', 'LIKE', "%{$search}%")
                    ->orWhereHas('direccion', function ($qq) use ($search) {
                        $qq->where('calle', 'LIKE', "%{$search}%")
                           ->orWhere('colonia', 'LIKE', "%{$search}%");
                    });
            });
        }

        
        $ordenarPor = $request->input('ordenarPor', 'nombre');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        switch ($ordenarPor) {
            case 'ciudad':
                $query->join('tbl_direccion as d', 'd.id_direccion_pk', '=', 'tbl_agencias.id_direccion_fk')
                      ->join('tbl_ciudad as c', 'c.id_ciudad_pk', '=', 'd.id_ciudad_fk')
                      ->orderBy('c.nombre_ciudad', $direction)
                      ->select('tbl_agencias.*');
                break;
            case 'departamento':
                $query->join('tbl_direccion as d', 'd.id_direccion_pk', '=', 'tbl_agencias.id_direccion_fk')
                      ->join('tbl_ciudad as c', 'c.id_ciudad_pk', '=', 'd.id_ciudad_fk')
                      ->join('tbl_departamento as dep', 'dep.id_departamento_pk', '=', 'c.id_departamento_fk')
                      ->orderBy('dep.nombre_departamento', $direction)
                      ->select('tbl_agencias.*');
                break;
            case 'pais':
                $query->join('tbl_direccion as d', 'd.id_direccion_pk', '=', 'tbl_agencias.id_direccion_fk')
                    ->join('tbl_ciudad as c', 'c.id_ciudad_pk', '=', 'd.id_ciudad_fk')
                    ->join('tbl_departamento as dep', 'dep.id_departamento_pk', '=', 'c.id_departamento_fk')
                    ->join('tbl_pais as p', 'p.id_pais_pk', '=', 'dep.id_pais_pk')
                    ->orderBy('p.nombre_pais', $direction)
                    ->select('tbl_agencias.*');
                break;
            case 'nombre':
            default:
                $query->orderBy('nombre_agencia', $direction);
                break;
        }

        
        $agencias = $query->get();

        
        return response()->json([
            'success' => true,
            'data' => AgenciaResource::collection($agencias),
        ]);
    }

    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_agencia' => 'required|string|max:100|unique:tbl_agencias,nombre_agencia',
            'horario_agencia' => 'required|string|max:50',
            'id_direccion_fk' => 'required|exists:tbl_direccion,id_direccion_pk',
            'clientes' => 'sometimes|array',
            'clientes.*' => 'integer|exists:tbl_cliente,id_cliente_pk'
        ]);

        $agencia = Agencia::create($request->only(['nombre_agencia','horario_agencia','id_direccion_fk']));

        if (!empty($validated['clientes'])) {
            $agencia->clientes()->sync($validated['clientes']);
        }

        $agencia->load(['direccion.ciudad.departamento.pais', 'clientes.empresa', 'clientes.personas']);

        return response()->json([
            'success' => true,
            'message' => 'Agencia creada exitosamente',
            'data' => new AgenciaResource($agencia)
        ], 201);
    }

    
    public function show(string $id): JsonResponse
    {
        $agencia = Agencia::with(['direccion.ciudad.departamento.pais', 'clientes.empresa', 'clientes.personas'])->find($id);

        if (!$agencia) {
            return response()->json([
                'success' => false,
                'message' => 'Agencia no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AgenciaResource($agencia)
        ]);
    }

    
    public function update(Request $request, string $id): JsonResponse
    {
        $agencia = Agencia::find($id);

        if (!$agencia) {
            return response()->json([
                'success' => false,
                'message' => 'Agencia no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'nombre_agencia' => 'sometimes|required|string|max:100|unique:tbl_agencias,nombre_agencia,' . $id . ',id_agencias_pk',
            'horario_agencia' => 'sometimes|required|string|max:50',
            'id_direccion_fk' => 'sometimes|required|exists:tbl_direccion,id_direccion_pk',
            'clientes' => 'sometimes|array',
            'clientes.*' => 'integer|exists:tbl_cliente,id_cliente_pk'
        ]);

        $agencia->update($request->only(['nombre_agencia','horario_agencia','id_direccion_fk']));

        if (array_key_exists('clientes', $validated)) {
            $agencia->clientes()->sync($validated['clientes'] ?? []);
        }

        $agencia->load(['direccion.ciudad.departamento.pais', 'clientes.empresa', 'clientes.personas']);

        return response()->json([
            'success' => true,
            'message' => 'Agencia actualizada exitosamente',
            'data' => new AgenciaResource($agencia)
        ]);
    }

    
    public function destroy(string $id): JsonResponse
    {
        $agencia = Agencia::find($id);

        if (!$agencia) {
            return response()->json([
                'success' => false,
                'message' => 'Agencia no encontrada'
            ], 404);
        }

        try {
            $agencia->delete();

            return response()->json([
                'success' => true,
                'message' => 'Agencia eliminada exitosamente'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ((int)($e->errorInfo[1] ?? 0) === 1451) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la agencia porque está en uso por otros registros.'
                ], 409);
            }
            throw $e;
        }
    }

    
    public function reporte(Request $request)
    {
        $query = Agencia::with(['direccion.ciudad.departamento.pais', 'clientes.empresa', 'clientes.personas']);

        
        if ($ciudad = $request->input('ciudad')) {
            $query->whereHas('direccion.ciudad', function($q) use ($ciudad) {
                $q->where('nombre_ciudad', 'like', "%$ciudad%");
            });
        }

        
        if ($departamento = $request->input('departamento')) {
            $query->whereHas('direccion.ciudad.departamento', function($q) use ($departamento) {
                $q->where('nombre_departamento', 'like', "%$departamento%");
            });
        }

        
        if ($pais = $request->input('pais')) {
            $query->whereHas('direccion.ciudad.departamento.pais', function($q) use ($pais) {
                $q->where('nombre_pais', 'like', "%$pais%");
            });
        }

        
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre_agencia', 'like', "%$q%")
                    ->orWhere('horario_agencia', 'like', "%$q%")
                    ->orWhereHas('direccion', function($qq) use ($q) {
                        $qq->where('calle', 'like', "%$q%")
                           ->orWhere('colonia', 'like', "%$q%");
                    });
            });
        }

        
        $sortable = [
            'nombre' => 'nombre_agencia',
            'ciudad' => 'direccion.ciudad.nombre_ciudad',
            'departamento' => 'direccion.ciudad.departamento.nombre_departamento',
            'pais' => 'direccion.ciudad.departamento.pais.nombre_pais',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('nombre_agencia', 'asc');
        }

        $agencias = $query->get();
        $total = $agencias->count();

        $fecha = now()->format('d/m/Y');
        $modulo = 'agencias';

        return view('admin.reporte-agencias', compact('agencias', 'total', 'fecha', 'modulo', 'sort', 'direction'));
    }
}