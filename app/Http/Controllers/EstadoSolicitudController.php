<?php

namespace App\Http\Controllers;

use App\Models\EstadoSolicitud;
use App\Http\Requests\StoreEstadoSolicitudRequest;
use App\Http\Requests\UpdateEstadoSolicitudRequest;
use App\Http\Resources\EstadoSolicitudResource;
use Illuminate\Http\Request; 

class EstadoSolicitudController extends Controller
{
   
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

    
    public function store(StoreEstadoSolicitudRequest $request)
    {
    
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

  
    public function update(UpdateEstadoSolicitudRequest $request, EstadoSolicitud $estadoSolicitud)
    {
        $estadoSolicitud->update($request->validated());
        
        return new EstadoSolicitudResource($estadoSolicitud);
    }

  
    public function destroy(EstadoSolicitud $estadoSolicitud)
    {
        if ($estadoSolicitud->solicitudes()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el estado porque tiene solicitudes asociadas.'
            ], 422); 
        }

        $estadoSolicitud->delete();
        
        return response()->json(null, 204);
    }
}