<?php

namespace App\Http\Controllers;

use App\Models\EstadoProyecto;
use App\Http\Requests\StoreEstadoProyectoRequest;
use App\Http\Requests\UpdateEstadoProyectoRequest;
use App\Http\Resources\EstadoProyectoResource;
use Illuminate\Http\Request;


class EstadoProyectoController extends Controller
{
    public function index(Request $request)
    {
        $query = EstadoProyecto::query();

        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('nombre', 'like', "%$q%")
                    ->orWhere('descripcion', 'like', "%$q%")
                    ->orWhere('codigo', 'like', "%$q%");
            });
        }

        $sortable = [
            'nombre' => 'nombre',
            'codigo' => 'codigo',
            'orden'  => 'orden',
            'id'     => 'id_estado_proyecto_pk',
        ];

        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc')) === 'desc' ? 'desc' : 'asc';

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('orden', 'asc');
        }
        
        $estadosProyecto = $query->get();

        return EstadoProyectoResource::collection($estadosProyecto);
    }

    public function store(StoreEstadoProyectoRequest $request)
    {
        $estadoProyecto = EstadoProyecto::create($request->validated());
        
        return (new EstadoProyectoResource($estadoProyecto))
                ->response()
                ->setStatusCode(201);
    }

    public function show(EstadoProyecto $estadoProyecto)
    {
        return new EstadoProyectoResource($estadoProyecto);
    }

    public function update(UpdateEstadoProyectoRequest $request, EstadoProyecto $estadoProyecto)
    {
        $estadoProyecto->update($request->validated());
        
        return new EstadoProyectoResource($estadoProyecto);
    }

    public function destroy(EstadoProyecto $estadoProyecto)
    {
        if ($estadoProyecto->proyectos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el estado porque tiene proyectos asociados.'
            ], 422);
        }

        $estadoProyecto->delete();
        
        return response()->json(null, 204);
    }
}