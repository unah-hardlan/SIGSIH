<?php

namespace App\Http\Controllers;

use App\Models\Gastos;
use App\Http\Resources\GastosResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GastosController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $query = Gastos::with(['proyecto', 'categoria']);

        
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

        
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_gasto', 'like', '%' . $searchTerm . '%')
                  ->orWhere('descripcion_gasto', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('proyecto', function($subQuery) use ($searchTerm) {
                      $subQuery->where('nombre_proyecto', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('categoria', function($subQuery) use ($searchTerm) {
                      $subQuery->where('nombre_categoria', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        
        if ($request->has('sort') && !empty($request->sort)) {
            $sortField = $request->sort;
            switch ($sortField) {
                case 'nombre':
                    $query->orderBy('nombre_gasto');
                    break;
                case 'fecha':
                    $query->orderBy('fecha_gasto');
                    break;
                case 'monto':
                    $query->orderBy('monto_gasto');
                    break;
                default:
                    $query->orderBy('id_gasto_pk', 'desc');
            }
        } else {
            $query->orderBy('fecha_gasto', 'desc');
        }

        $gastos = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => GastosResource::collection($gastos->items()),
            'pagination' => [
                'current_page' => $gastos->currentPage(),
                'last_page' => $gastos->lastPage(),
                'per_page' => $gastos->perPage(),
                'total' => $gastos->total(),
            ]
        ], 200);
    }

    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'id_proyecto_fk' => 'required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'required|exists:tbl_categorias,id_categoria_pk'
        ]);

        
        $data = [
            'nombre_gasto' => $validated['nombre'],
            'fecha_gasto' => $validated['fecha'],
            'monto_gasto' => $validated['monto'],
            'descripcion_gasto' => $validated['descripcion'] ?? null,
            'id_proyecto_fk' => $validated['id_proyecto_fk'],
            'id_categoria_fk' => $validated['id_categoria_fk']
        ];

        $gasto = Gastos::create($data);
        $gasto->load(['proyecto', 'categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Gasto creado exitosamente',
            'data' => new GastosResource($gasto)
        ], 201);
    }

    
    public function show($id): JsonResponse
    {
        $gasto = Gastos::with(['proyecto', 'categoria'])->find($id);

        if (!$gasto) {
            return response()->json([
                'success' => false,
                'message' => 'Gasto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Gasto encontrado',
            'data' => new GastosResource($gasto)
        ], 200);
    }

    
    public function update(Request $request, $id): JsonResponse
    {
        $gasto = Gastos::find($id);

        if (!$gasto) {
            return response()->json([
                'success' => false,
                'message' => 'Gasto no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'fecha' => 'sometimes|required|date',
            'monto' => 'sometimes|required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'id_proyecto_fk' => 'sometimes|required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'sometimes|required|exists:tbl_categorias,id_categoria_pk'
        ]);

        
        $data = [];
        if (isset($validated['nombre'])) $data['nombre_gasto'] = $validated['nombre'];
        if (isset($validated['fecha'])) $data['fecha_gasto'] = $validated['fecha'];
        if (isset($validated['monto'])) $data['monto_gasto'] = $validated['monto'];
        if (array_key_exists('descripcion', $validated)) $data['descripcion_gasto'] = $validated['descripcion'];
        if (isset($validated['id_proyecto_fk'])) $data['id_proyecto_fk'] = $validated['id_proyecto_fk'];
        if (isset($validated['id_categoria_fk'])) $data['id_categoria_fk'] = $validated['id_categoria_fk'];

        $gasto->update($data);
        $gasto->load(['proyecto', 'categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Gasto actualizado exitosamente',
            'data' => new GastosResource($gasto)
        ]);
    }

    
    public function destroy($id): JsonResponse
    {
        $gasto = Gastos::find($id);

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
        ], 200);
    }
}