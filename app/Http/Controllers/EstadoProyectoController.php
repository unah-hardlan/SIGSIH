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

        // Búsqueda general (q)
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($sub) use ($searchTerm) {
                $sub->where('nombre', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripcion', 'like', '%' . $searchTerm . '%')
                    ->orWhere('codigo', 'like', '%' . $searchTerm . '%');
            });
        }

        // Ordenamiento
        if ($request->has('sort') && !empty($request->sort)) {
            $sortField = $request->sort;
            switch ($sortField) {
                case 'nombre':
                    $query->orderBy('nombre');
                    break;
                case 'id':
                    $query->orderBy('id_estado_proyecto_pk');
                    break;
                default:
                    $query->orderBy('orden', 'asc');
            }
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