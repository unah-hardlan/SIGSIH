<?php

namespace App\Http\Controllers;

use App\Models\EstadoSolicitud;
// ¡Importamos los nuevos FormRequests!
use App\Http\Requests\StoreEstadoSolicitudRequest;
use App\Http\Requests\UpdateEstadoSolicitudRequest;
use App\Http\Resources\EstadoSolicitudResource;
use Illuminate\Http\Request; // Necesario para el método index

class EstadoSolicitudController extends Controller
{
    /**
     * Muestra una lista de los recursos.
     */
    public function index(Request $request)
    {
        $query = EstadoSolicitud::query();

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
            'id'     => 'id_estado_solicitud_pk',
        ];

        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc')) === 'desc' ? 'desc' : 'asc';

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('orden', 'asc');
        }
        
        $estadosSolicitud = $query->get();

        return EstadoSolicitudResource::collection($estadosSolicitud);
    }

    /**
     * Almacena un nuevo recurso.
     */
    public function store(StoreEstadoSolicitudRequest $request)
    {
        // La validación ya ocurrió automáticamente.
        // Usamos $request->validated() para obtener solo los datos validados.
        $estadoSolicitud = EstadoSolicitud::create($request->validated());
        
        return (new EstadoSolicitudResource($estadoSolicitud))
                ->response()
                ->setStatusCode(201); // 201 Created
    }

    /**
     * Muestra un recurso específico.
     */
    public function show(EstadoSolicitud $estadoSolicitud)
    {
        return new EstadoSolicitudResource($estadoSolicitud);
    }

    /**
     * Actualiza un recurso específico.
     */
    public function update(UpdateEstadoSolicitudRequest $request, EstadoSolicitud $estadoSolicitud)
    {
        // La validación también ocurrió automáticamente.
        $estadoSolicitud->update($request->validated());
        
        return new EstadoSolicitudResource($estadoSolicitud);
    }

    /**
     * Elimina un recurso específico.
     */
    public function destroy(EstadoSolicitud $estadoSolicitud)
    {
        // Esta lógica es excelente. Asegúrate de que el modelo EstadoSolicitud
        // tenga definida la relación `solicitudes()`.
        if ($estadoSolicitud->solicitudes()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el estado porque tiene solicitudes asociadas.'
            ], 422); // 422 Unprocessable Entity es perfecto para esto.
        }

        $estadoSolicitud->delete();
        
        return response()->json(null, 204); // 204 No Content
    }
}