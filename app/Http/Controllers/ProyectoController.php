<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Http\Resources\ProyectoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proyecto::with(['ordenServicio.solicitudServicio', 'estadoProyecto']);

        // Filtros opcionales
        if ($request->has('id_orden_servicio_fk')) {
            $query->where('id_orden_servicio_fk', $request->id_orden_servicio_fk);
        }

        if ($request->has('id_estado_proyecto_fk')) {
            $query->where('id_estado_proyecto_fk', $request->id_estado_proyecto_fk);
        }

        if ($request->has('nombre_proyecto')) {
            $query->where('nombre_proyecto', 'like', '%' . $request->nombre_proyecto . '%');
        }

        // Búsqueda general (q)
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_proyecto', 'like', '%' . $searchTerm . '%')
                  ->orWhere('descripcion_proyecto', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('ordenServicio', function($subQuery) use ($searchTerm) {
                      $subQuery->where('numero_orden_servicio', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('estadoProyecto', function($subQuery) use ($searchTerm) {
                      $subQuery->where('nombre', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Ordenamiento
        if ($request->has('sort') && !empty($request->sort)) {
            $sortField = $request->sort;
            switch ($sortField) {
                case 'nombre':
                    $query->orderBy('nombre_proyecto');
                    break;
                case 'fecha_inicio':
                    $query->orderBy('fecha_inicio_proyecto');
                    break;
                case 'fecha_estimada':
                    $query->orderBy('fecha_estimada_fin_proyecto');
                    break;
                case 'fecha_fin':
                    $query->orderBy('fecha_finalizacion_proyecto');
                    break;
                default:
                    $query->orderBy('id_proyecto_pk', 'desc');
            }
        } else {
            $query->orderBy('id_proyecto_pk', 'desc');
        }

        $proyectos = $query->paginate(15);

        return ProyectoResource::collection($proyectos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre_proyecto' => 'required|string|max:100',
            'fecha_inicio_proyecto' => 'required|date',
            'fecha_estimada_fin_proyecto' => 'nullable|date',
            'fecha_finalizacion_proyecto' => 'nullable|date',
            'descripcion_proyecto' => 'nullable|string|max:500',
            'id_orden_servicio_fk' => 'required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_estado_proyecto_fk' => 'required|integer|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ]);

        $proyecto = Proyecto::create($validatedData);
        $proyecto->load(['ordenServicio.solicitudServicio', 'estadoProyecto']);

        return new ProyectoResource($proyecto);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $proyecto = Proyecto::with(['ordenServicio.solicitudServicio', 'estadoProyecto'])->findOrFail($id);
        return new ProyectoResource($proyecto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);

        $validatedData = $request->validate([
            'nombre_proyecto' => 'sometimes|required|string|max:100',
            'fecha_inicio_proyecto' => 'sometimes|required|date',
            'fecha_estimada_fin_proyecto' => 'nullable|date',
            'fecha_finalizacion_proyecto' => 'nullable|date',
            'descripcion_proyecto' => 'nullable|string|max:500',
            'id_orden_servicio_fk' => 'sometimes|required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_estado_proyecto_fk' => 'sometimes|required|integer|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ]);

        $proyecto->update($validatedData);
        $proyecto->load(['ordenServicio.solicitudServicio', 'estadoProyecto']);

        return new ProyectoResource($proyecto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente'
        ], Response::HTTP_OK);
    }

    // Reporte web (HTML) dinámico
    public function reporte(Request $request)
    {
        $query = Proyecto::with(['ordenServicio', 'estadoProyecto']);

        // Por defecto mostrar todos los proyectos para el reporte
        if ($estado = $request->input('estado')) {
            $query->whereHas('estadoProyecto', function($q) use ($estado) {
                $q->where('codigo', $estado);
            });
        }
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre_proyecto', 'like', "%$q%")
                    ->orWhere('descripcion_proyecto', 'like', "%$q%")
                    ->orWhereHas('ordenServicio', function($subQuery) use ($q) {
                        $subQuery->where('numero_orden_servicio', 'like', "%$q%");
                    });
            });
        }
        // Orden
        $sortable = [
            'nombre' => 'nombre_proyecto',
            'fecha_inicio' => 'fecha_inicio_proyecto',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('id_proyecto_pk', 'desc');
        }

        $proyectos = $query->get();
        $total = $proyectos->count();
        $activos = $proyectos->filter(function($p) { return $p->estadoProyecto && $p->estadoProyecto->codigo === 'ACTIVO'; })->count();
        $finalizados = $proyectos->filter(function($p) { return $p->estadoProyecto && $p->estadoProyecto->codigo === 'FINALIZADO'; })->count();
        $inactivos = $proyectos->filter(function($p) { return $p->estadoProyecto && $p->estadoProyecto->codigo === 'INACTIVO'; })->count();

        $fecha = now()->format('d/m/Y');
        $modulo = 'proyectos';

        return view('admin.reporte-proyectos', compact('proyectos', 'total', 'activos', 'finalizados', 'inactivos', 'fecha', 'modulo', 'sort', 'direction'));
    }
}
