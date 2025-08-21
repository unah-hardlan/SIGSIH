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
                $sub->where('nombre_estado', 'like', "%$q%")
                    ->orWhere('descripcion_estado', 'like', "%$q%");
            });
        }

        if ($nombre = request('nombre')) {
            $query->where('nombre_estado', 'like', "%$nombre%");
        }

        // Ordenamiento dinámico
        $sortable = [
            'nombre' => 'nombre_estado',
            'descripcion' => 'descripcion_estado',
            'id' => 'id_estado_solicitud_pk',
        ];
        $sort = request('sort');
        $direction = strtolower(request('direction','asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            // orden por defecto
            $query->orderBy('nombre_estado', 'asc');
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

    public function store(Request $request)
    {
        $request->validate([
            'nombre_estado' => 'required|string|max:50|unique:tbl_estado_solicitud,nombre_estado',
            'descripcion_estado' => 'nullable|string|max:255',
        ]);

        $estadoSolicitud = EstadoSolicitud::create($request->all());
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

    public function update(Request $request, $id)
    {
        $estadoSolicitud = EstadoSolicitud::find($id);
        if (!$estadoSolicitud) {
            return response()->json(['error' => 'Estado de solicitud no encontrado'], 404);
        }

        $request->validate([
            'nombre_estado' => 'sometimes|required|string|max:50|unique:tbl_estado_solicitud,nombre_estado,' . $id . ',id_estado_solicitud_pk',
            'descripcion_estado' => 'nullable|string|max:255',
        ]);

        $estadoSolicitud->update($request->all());
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
