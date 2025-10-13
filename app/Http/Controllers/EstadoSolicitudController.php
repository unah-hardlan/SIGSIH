<?php

namespace App\Http\Controllers;

use App\Models\EstadoSolicitud;
use Illuminate\Http\Request;
use App\Http\Resources\EstadoSolicitudResource;

class EstadoSolicitudController extends Controller
{
    public function index()
    {
        $query = EstadoSolicitud::query();

        // Filtros
        if ($q = request('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('nombre', 'like', "%$q%")
                    ->orWhere('descripcion', 'like', "%$q%");
            });
        }

        if ($nombre = request('nombre')) {
            $query->where('nombre', 'like', "%$nombre%");
        }

        // Ordenamiento dinámico
        $sortable = [
            'nombre' => 'nombre',
            'descripcion' => 'descripcion',
            'id' => 'id_estado_solicitud_pk',
        ];
        $sort = request('sort');
        $direction = strtolower(request('direction','asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            // orden por defecto
            $query->orderBy('nombre', 'asc');
        }

        $perPage = (int) request('per_page', 15);
        $estadosSolicitud = $query->paginate($perPage);

        return EstadoSolicitudResource::collection($estadosSolicitud)->additional([
            'meta' => [
                'page' => $estadosSolicitud->currentPage(),
                'per_page' => $estadosSolicitud->perPage(),
                'total' => $estadosSolicitud->total(),
                'last_page' => $estadosSolicitud->lastPage(),
            ]
        ]);
    }

    public function create() {}

    public function store(\App\Http\Requests\StoreEstadoSolicitudRequest $request)
    {
        $data = $request->validated();
        $estadoSolicitud = EstadoSolicitud::create($data);
        return (new EstadoSolicitudResource($estadoSolicitud))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $estadoSolicitud = EstadoSolicitud::find($id);
        if (!$estadoSolicitud) {
            return response()->json(['error' => 'Estado de solicitud no encontrado'], 404);
        }
        return (new EstadoSolicitudResource($estadoSolicitud))->response();
    }

    public function edit(string $id) {}

    public function update(\App\Http\Requests\UpdateEstadoSolicitudRequest $request, $id)
    {
        $estadoSolicitud = EstadoSolicitud::find($id);
        if (!$estadoSolicitud) {
            return response()->json(['error' => 'Estado de solicitud no encontrado'], 404);
        }

        $estadoSolicitud->update($request->validated());
        return (new EstadoSolicitudResource($estadoSolicitud))->response();
    }

    public function destroy($id)
    {
        $estadoSolicitud = EstadoSolicitud::find($id);
        if (!$estadoSolicitud) {
            return response()->json(['error' => 'Estado de solicitud no encontrado'], 404);
        }

        // Verificar si tiene solicitudes asociadas antes de eliminar
        if ($estadoSolicitud->solicitudes()->exists()) {
            return response()->json([
                'error' => 'No se puede eliminar el estado porque tiene solicitudes asociadas'
            ], 422);
        }

        $estadoSolicitud->delete();
        return response()->json(['message' => 'Estado de solicitud eliminado correctamente'], 200);
    }
}
