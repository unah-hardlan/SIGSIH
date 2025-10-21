<?php

namespace App\Http\Controllers;

use App\Models\Agencia;
use App\Http\Resources\AgenciaResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Agencia::with(['direccion.ciudad.departamento.pais']);

        // Filtro por dirección exacta
        if ($request->filled('id_direccion_fk')) {
            $query->where('id_direccion_fk', $request->integer('id_direccion_fk'));
        }

        // Filtro por ciudad/departamento/pais a través de relaciones
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
                // En la tabla departamento la FK hacia país es id_pais_pk
                $q->where('id_pais_pk', $request->integer('id_pais_pk'));
            });
        }

        // Filtro de búsqueda por nombre/horario/dirección
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

        // Ordenamiento: nombre (default), ciudad, departamento, pais
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
                // Nota: la FK en departamento hacia país es id_pais_pk
                ->join('tbl_pais as p', 'p.id_pais_pk', '=', 'dep.id_pais_pk')
                ->orderBy('p.nombre_pais', $direction)
                ->select('tbl_agencias.*');
            break;
            case 'nombre':
            default:
                $query->orderBy('nombre_agencia', $direction);
                break;
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min($perPage, 100));
        $agencias = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AgenciaResource::collection($agencias->items()),
            'pagination' => [
                'current_page' => $agencias->currentPage(),
                'per_page' => $agencias->perPage(),
                'total' => $agencias->total(),
                'last_page' => $agencias->lastPage(),
                'from' => $agencias->firstItem(),
                'to' => $agencias->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_agencia' => 'required|string|max:100|unique:tbl_agencias,nombre_agencia',
            'horario_agencia' => 'required|string|max:50',
            'id_direccion_fk' => 'required|exists:tbl_direccion,id_direccion_pk'
        ]);

        $agencia = Agencia::create($validated);
        $agencia->load(['direccion.ciudad.departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Agencia creada exitosamente',
            'data' => new AgenciaResource($agencia)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $agencia = Agencia::with(['direccion.ciudad.departamento.pais'])->find($id);

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

    /**
     * Update the specified resource in storage.
     */
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
            'id_direccion_fk' => 'sometimes|required|exists:tbl_direccion,id_direccion_pk'
        ]);

        $agencia->update($validated);
        $agencia->load(['direccion.ciudad.departamento.pais']);

        return response()->json([
            'success' => true,
            'message' => 'Agencia actualizada exitosamente',
            'data' => new AgenciaResource($agencia)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
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
            // MySQL error code for foreign key constraint failure is 1451
            if ((int)($e->errorInfo[1] ?? 0) === 1451) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la agencia porque está en uso por otros registros.'
                ], 409);
            }
            throw $e;
        }
    }
}
