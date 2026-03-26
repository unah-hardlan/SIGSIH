<?php

namespace App\Http\Controllers;

use App\Models\CalificacionServicio;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCalificacionServicioRequest;
use App\Http\Requests\UpdateCalificacionServicioRequest;
use App\Http\Resources\CalificacionServicioResource;

class CalificacionServicioController extends Controller
{
    public function index()
    {
        $query = CalificacionServicio::query();

        
        if ($q = request('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('nombre_calificacion', 'like', "%$q%")
                    ->orWhere('descripcion_calificacion', 'like', "%$q%");
            });
        }

        
        $sortable = [
            'nombre_calificacion' => 'nombre_calificacion',
            'descripcion_calificacion' => 'descripcion_calificacion',
        ];
        
        $sort = request('sort');
        $direction = strtolower(request('direction','asc')) === 'desc' ? 'desc' : 'asc';
        
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            
            $query->orderBy('id_calificacion_servicio_pk', 'desc');
        }

        $perPage = (int) request('per_page', 15);
        $calificaciones = $query->paginate($perPage);

        return CalificacionServicioResource::collection($calificaciones)->additional([
            'meta' => [
                'page' => $calificaciones->currentPage(),
                'per_page' => $calificaciones->perPage(),
                'total' => $calificaciones->total(),
                'last_page' => $calificaciones->lastPage(),
            ]
        ]);
    }

    public function create() {}

    public function store(StoreCalificacionServicioRequest $request)
    {
        $data = $request->validated();
        $calificacion = CalificacionServicio::create($data);
        return (new CalificacionServicioResource($calificacion))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $calificacion = CalificacionServicio::find($id);
        if (!$calificacion) {
            return response()->json(['error' => 'Calificación de servicio no encontrada'], 404);
        }
        return (new CalificacionServicioResource($calificacion))->response();
    }

    public function edit(string $id) {}

    public function update(UpdateCalificacionServicioRequest $request, $id)
    {
        $calificacion = CalificacionServicio::find($id);
        if (!$calificacion) {
            return response()->json(['error' => 'Calificación de servicio no encontrada'], 404);
        }
        
        $data = $request->validated();
        $calificacion->update($data);
        
        return (new CalificacionServicioResource($calificacion))->response();
    }

    public function destroy($id)
    {
        $calificacion = CalificacionServicio::find($id);
        if (!$calificacion) {
            return response()->json(['error' => 'Calificación de servicio no encontrada'], 404);
        }
        
        $calificacion->delete();
        return response()->json(['message' => 'Calificación de servicio eliminada correctamente'], 200);
    }

    
    public function reporte(Request $request)
    {
        $query = CalificacionServicio::query();

        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('nombre_calificacion', 'like', "%$q%")
                    ->orWhere('descripcion_calificacion', 'like', "%$q%");
            });
        }
        
        
        $sortable = [
            'nombre_calificacion' => 'nombre_calificacion',
            'descripcion_calificacion' => 'descripcion_calificacion',
        ];
        
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc')) === 'desc' ? 'desc' : 'asc';
        
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('id_calificacion_servicio_pk', 'desc');
        }

        $calificaciones = $query->get();
        $total = $calificaciones->count();

        $fecha = \App\Helpers\DateHelper::nowFormatted('d/m/Y');
        $modulo = 'calificaciones-servicio';

        return view('admin.reporte-calificaciones-servicio', compact('calificaciones','total','fecha','modulo','sort','direction'));
    }
}
