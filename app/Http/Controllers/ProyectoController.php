<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Http\Resources\ProyectoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProyectoController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Proyecto::with(['ordenServicio.solicitudServicio', 'estadoProyecto']);

        
        if ($request->has('id_orden_servicio_fk')) {
            $query->where('id_orden_servicio_fk', $request->id_orden_servicio_fk);
        }

        if ($request->has('id_estado_proyecto_fk')) {
            $query->where('id_estado_proyecto_fk', $request->id_estado_proyecto_fk);
        }

        if ($request->has('nombre_proyecto')) {
            $query->where('nombre_proyecto', 'like', '%' . $request->nombre_proyecto . '%');
        }

        
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

    
    public function show($id)
    {
        $proyecto = Proyecto::with(['ordenServicio.solicitudServicio', 'estadoProyecto'])->findOrFail($id);
        return new ProyectoResource($proyecto);
    }

    
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

    
    public function destroy($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente'
        ], Response::HTTP_OK);
    }

    
    public function reporte(Request $request)
    {
        $query = Proyecto::with(['ordenServicio', 'estadoProyecto']);

        
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

    
    public function reporteMovimientos(Request $request)
    {
        
        $queryIngresos = \App\Models\Ingresos::with(['proyecto', 'categoria']);
        if ($q = $request->input('q_ingreso')) {
            $queryIngresos->where(function ($sub) use ($q) {
                $sub->where('nombre_ingreso', 'like', "%$q%")
                    ->orWhere('descripcion_ingreso', 'like', "%$q%")
                    ->orWhereHas('proyecto', function($subQuery) use ($q) {
                        $subQuery->where('nombre_proyecto', 'like', "%$q%");
                    })
                    ->orWhereHas('categoria', function($subQuery) use ($q) {
                        $subQuery->where('nombre_categoria', 'like', "%$q%");
                    });
            });
        }
        $sortableIngresos = [
            'proyecto' => 'proyecto.nombre_proyecto',
            'fecha' => 'fecha_ingreso',
            'monto' => 'monto_ingreso',
        ];
        $sortIngreso = $request->input('sort_ingreso');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if ($sortIngreso && isset($sortableIngresos[$sortIngreso])) {
            $queryIngresos->orderBy($sortableIngresos[$sortIngreso], $direction);
        } else {
            $queryIngresos->orderBy('id_ingresos_pk', 'desc');
        }
        $ingresos = $queryIngresos->get();

        
        $queryGastos = \App\Models\Gastos::with(['proyecto', 'categoria']);
        if ($q = $request->input('q_gasto')) {
            $queryGastos->where(function ($sub) use ($q) {
                $sub->where('nombre_gasto', 'like', "%$q%")
                    ->orWhere('descripcion_gasto', 'like', "%$q%")
                    ->orWhereHas('proyecto', function($subQuery) use ($q) {
                        $subQuery->where('nombre_proyecto', 'like', "%$q%");
                    })
                    ->orWhereHas('categoria', function($subQuery) use ($q) {
                        $subQuery->where('nombre_categoria', 'like', "%$q%");
                    });
            });
        }
        $sortableGastos = [
            'proyecto' => 'proyecto.nombre_proyecto',
            'fecha' => 'fecha_gasto',
            'monto' => 'monto_gasto',
        ];
        $sortGasto = $request->input('sort_gasto');
        if ($sortGasto && isset($sortableGastos[$sortGasto])) {
            $queryGastos->orderBy($sortableGastos[$sortGasto], $direction);
        } else {
            $queryGastos->orderBy('id_gasto_pk', 'desc');
        }
        $gastos = $queryGastos->get();

        
        $totalIngresos = $ingresos->count();
        $totalGastos = $gastos->count();
        $sumaIngresos = $ingresos->sum('monto_ingreso');
        $sumaGastos = $gastos->sum('monto_gasto');
        $balance = $sumaIngresos - $sumaGastos;

        $fecha = now()->format('d/m/Y');
        $modulo = 'movimientos-proyecto';

        return view('admin.reporte-movimientos-proyecto', compact(
            'ingresos', 'gastos', 'totalIngresos', 'totalGastos', 
            'sumaIngresos', 'sumaGastos', 'balance', 'fecha', 'modulo'
        ));
    }

    
    public function reporteFinanciero(Request $request, $idProyecto = null)
    {
        
        $proyectoId = $idProyecto ?? $request->input('id_proyecto');

        if (!$proyectoId) {
            return redirect()->back()->with('error', 'Debe seleccionar un proyecto para generar el reporte.');
        }

        
        $proyecto = Proyecto::with(['ordenServicio', 'estadoProyecto'])->findOrFail($proyectoId);

        
        $ingresos = $proyecto->ingresos()->with('categoria')->get();

        
        $gastos = $proyecto->gastos()->with('categoria')->get();

        
        $totalIngresos = $ingresos->sum('monto_ingreso');
        $totalGastos = $gastos->sum('monto_gasto');
        $balance = $totalIngresos - $totalGastos;

        
        $movimientos = collect();

        
        foreach ($ingresos as $ingreso) {
            $movimientos->push([
                'tipo' => 'ingreso',
                'id' => $ingreso->id_ingresos_pk,
                'nombre' => $ingreso->nombre_ingreso,
                'categoria' => $ingreso->categoria ? $ingreso->categoria->nombre_categoria : 'Sin categoría',
                'monto' => $ingreso->monto_ingreso,
                'fecha' => $ingreso->fecha_ingreso,
                'descripcion' => $ingreso->descripcion_ingreso,
            ]);
        }

        
        foreach ($gastos as $gasto) {
            $movimientos->push([
                'tipo' => 'gasto',
                'id' => $gasto->id_gasto_pk,
                'nombre' => $gasto->nombre_gasto,
                'categoria' => $gasto->categoria ? $gasto->categoria->nombre_categoria : 'Sin categoría',
                'monto' => $gasto->monto_gasto,
                'fecha' => $gasto->fecha_gasto,
                'descripcion' => $gasto->descripcion_gasto,
            ]);
        }

        
        $movimientos = $movimientos->sortByDesc('fecha');

        $fecha = now()->format('d/m/Y');
        $modulo = 'proyecto-financiero';

        return view('admin.reporte-proyecto-financiero', compact(
            'proyecto', 'ingresos', 'gastos', 'totalIngresos', 'totalGastos', 'balance', 'movimientos', 'fecha', 'modulo'
        ));
    }
}
