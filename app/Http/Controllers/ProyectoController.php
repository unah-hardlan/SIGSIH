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
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre_proyecto', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripcion_proyecto', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('ordenServicio', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('numero_orden_servicio', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('estadoProyecto', function ($subQuery) use ($searchTerm) {
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


    public function reporteBasico(Request $request)
    {
        $fecha = $request->query('fecha', \App\Helpers\DateHelper::nowFormatted('d/m/Y'));
        $modulo = $request->query('modulo', 'Proyecto BAC');
        return view('admin.reporte-proyecto', compact('fecha', 'modulo'));
    }


    public function formatoReporte()
    {
        return view('admin.formato-reporte');
    }


    public function reportesHeader(Request $request)
    {
        $modulo = $request->query('modulo', '');
        $fecha = $request->query('fecha', \App\Helpers\DateHelper::nowFormatted('d/m/Y'));
        $moduloLower = strtolower($modulo);
        $ordenarPor = $request->query('ordenar_por');
        $search = $request->query('search');
        $estadoEmpresa = $request->query('estado_empresa');
        $fechaGeneracion = $request->query('fecha_generacion');

        $delegaciones = [
            'usuarios' => [\App\Http\Controllers\UsuarioController::class, 'reporte'],
            'parametros' => [\App\Http\Controllers\ParametroController::class, 'reporte'],
            'proyectos' => [self::class, 'reporte'],
            'movimientos-proyecto' => [self::class, 'reporteMovimientos'],
            'proyecto-financiero' => [self::class, 'reporteFinanciero'],
            'tickets' => [\App\Http\Controllers\TicketController::class, 'reporte'],
            'agencias' => [\App\Http\Controllers\AgenciasController::class, 'reporte'],
            'calendario' => [\App\Http\Controllers\CalendarioController::class, 'reporte'],
            'cai' => [\App\Http\Controllers\CaiController::class, 'reporte'],
            'productos' => [\App\Http\Controllers\ProductoController::class, 'reporte'],
            'kardex' => [\App\Http\Controllers\KardexController::class, 'reporte'],
        ];
        if (isset($delegaciones[$moduloLower])) {
            return app($delegaciones[$moduloLower][0])->{$delegaciones[$moduloLower][1]}($request);
        }

        $view = match ($moduloLower) {
            'configuracion de accesos', 'configuracion-acceso' => null,
            'empresas' => 'admin.reporte-empresas',
            'solicitudes' => 'admin.reporte-solicitudes',
            'tickets' => 'admin.reporte-tickets',
            'agencias' => 'admin.reporte-agencias',
            'calendario' => 'admin.reporte-calendario',
            'facturas' => 'admin.reporte-facturas',
            'cai' => 'admin.reporte-cai',
            'bitacora' => 'admin.reporte-bitacora',
            'productos' => 'admin.reporte-productos',
            'kardex' => 'admin.reporte-kardex',
            'proyectos' => 'admin.reporte-proyectos',
            default => 'admin.reporte-generico',
        };
        if (in_array($moduloLower, ['configuracion de accesos', 'configuracion-acceso'])) {
            return app(\App\Http\Controllers\ConfiguracionAccesoReporteController::class)->reporte($request);
        }
        if ($moduloLower === 'gestion de personas') {
            return app(\App\Http\Controllers\PersonaController::class)->reporte($request);
        }

        if ($moduloLower === 'empresas') {
            $query = \App\Models\Cliente::query()
                ->where('tipo_cliente', 'empresa')
                ->join('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'tbl_cliente.id_cliente_pk')
                ->select([
                    'tbl_cliente.id_cliente_pk',
                    'tbl_cliente.fecha_registro',
                    'tbl_cliente.estado_cliente',
                    'ce.nombre_comercial',
                    'ce.razon_social',
                    'ce.rtn',
                    'ce.descripcion_empresa',
                    'ce.horario_atencion',
                ]);
            if (!empty($search)) {
                $s = "%$search%";
                $query->where(function ($q) use ($s) {
                    $q->where('ce.nombre_comercial', 'like', $s)
                        ->orWhere('ce.razon_social', 'like', $s)
                        ->orWhere('ce.rtn', 'like', $s);
                });
            }
            if ($estadoEmpresa && in_array(strtolower($estadoEmpresa), ['activo', 'inactivo'])) {
                $query->where('tbl_cliente.estado_cliente', strtolower($estadoEmpresa));
            }
            $allowedOrden = [
                'nombre_empresa' => 'ce.nombre_comercial',
                'estado_empresa' => 'tbl_cliente.estado_cliente',
                'fecha_registro' => 'tbl_cliente.fecha_registro',
            ];
            if ($ordenarPor && isset($allowedOrden[$ordenarPor])) {
                $query->orderBy($allowedOrden[$ordenarPor], 'asc');
            } else {
                $query->orderBy('tbl_cliente.fecha_registro', 'desc');
            }
            $empresas = $query->get();
            try {
                $nombresEmpresa = \App\Models\NombreEmpresa::select('id_nombre_empresa_pk', 'nombre_empresa', 'descripcion_empresa')->orderBy('nombre_empresa')->get();
            } catch (\Throwable $e) {
                $nombresEmpresa = collect();
            }
            try {
                $oficinasEmpresa = \App\Models\OficinaEmpresa::select('id_oficina_empresa_pk', 'nombre_oficina')->orderBy('nombre_oficina')->get();
            } catch (\Throwable $e) {
                $oficinasEmpresa = collect();
            }
            $ordenLabelMap = [
                'nombre_empresa' => 'nombre (comercial)',
                'estado_empresa' => 'estado',
                'fecha_registro' => 'fecha_registro (desc por defecto)',
            ];
            $ordenKey = ($ordenarPor && is_string($ordenarPor) && $ordenarPor !== '') ? $ordenarPor : null;
            $ordenLabel = $ordenKey ? ($ordenLabelMap[$ordenKey] ?? str_replace('_', ' ', $ordenKey)) : 'fecha_registro (desc)';
            return view($view, compact('fecha', 'modulo', 'empresas', 'ordenarPor', 'ordenLabel', 'search', 'estadoEmpresa', 'fechaGeneracion', 'nombresEmpresa', 'oficinasEmpresa'));
        }

        if (in_array($moduloLower, ['proyecto', 'proyecto-bac', 'reporte-proyecto'])) {
            $total_ingresos = $request->query('total_ingresos') ?? 'L. 4,787.00';
            $total_gastos = $request->query('total_gastos') ?? 'L. 0.00';
            $balance = $request->query('balance') ?? 'L. 4,787.00';
            $cards = [
                ['title' => 'Ingresos Totales', 'value' => $total_ingresos, 'icon' => 'fa-arrow-up', 'sub' => 'Total recibido en el período', 'borderColor' => 'border-emerald-500', 'textColor' => 'text-emerald-600', 'bgColor' => 'bg-emerald-50'],
                ['title' => 'Gastos Totales', 'value' => $total_gastos, 'icon' => 'fa-arrow-down', 'sub' => 'Total gastado en el período', 'borderColor' => 'border-rose-500', 'textColor' => 'text-rose-600', 'bgColor' => 'bg-rose-50'],
                ['title' => 'Balance Neto', 'value' => $balance, 'icon' => 'fa-scale-balanced', 'sub' => 'Diferencia entre ingresos y gastos', 'borderColor' => 'border-blue-500', 'textColor' => 'text-blue-600', 'bgColor' => 'bg-blue-50'],
            ];
            return view('admin.reporte-proyecto', compact('fecha', 'modulo', 'total_ingresos', 'total_gastos', 'balance', 'cards'));
        }

        if ($moduloLower === 'solicitudes') {
            $ordenarPor = $request->query('ordenar_por');
            $search = $request->query('search');
            $estadoSolicitud = $request->query('estado_solicitud');
            $clienteNombreExpr = "COALESCE(ce.nombre_comercial, ce.razon_social, CONCAT_WS(' ', p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido))";
            $query = \Illuminate\Support\Facades\DB::table('tbl_solicitud as s')
                ->join('tbl_cliente as c', 'c.id_cliente_pk', '=', 's.id_cliente_fk')
                ->leftJoin('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'c.id_cliente_pk')
                ->leftJoin('tbl_cliente_persona as cp', 'cp.id_cliente_fk', '=', 'c.id_cliente_pk')
                ->leftJoin('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                ->leftJoin('tbl_estado_solicitud as es', 'es.id_estado_solicitud_pk', '=', 's.id_estado_solicitud_fk')
                ->leftJoin('tbl_contacto as co', 'co.id_contacto_pk', '=', 's.id_contacto_fk')
                ->select([
                    's.id_solicitud_pk as id',
                    's.numero_solicitud_acf',
                    's.numero_solicitud_cliente',
                    's.descripcion_problema',
                    's.id_estado_solicitud_fk',
                    's.id_cliente_fk',
                    'es.nombre as estado_nombre',
                    'co.valor_contacto',
                    \Illuminate\Support\Facades\DB::raw($clienteNombreExpr . ' as cliente_nombre'),
                ]);
            if (!empty($estadoSolicitud)) {
                if (is_numeric($estadoSolicitud)) {
                    $query->where('s.id_estado_solicitud_fk', (int)$estadoSolicitud);
                } else {
                    $query->where(function ($q) use ($estadoSolicitud) {
                        $q->where('es.nombre', 'like', "%$estadoSolicitud%")
                            ->orWhere('es.codigo', 'like', "%$estadoSolicitud%");
                    });
                }
            }
            if (!empty($search)) {
                $s = "%$search%";
                $query->where(function ($q) use ($s, $clienteNombreExpr) {
                    $q->where('s.numero_solicitud_acf', 'like', $s)
                        ->orWhere('s.numero_solicitud_cliente', 'like', $s)
                        ->orWhere('s.descripcion_problema', 'like', $s)
                        ->orWhere('es.nombre', 'like', $s)
                        ->orWhere('es.codigo', 'like', $s)
                        ->orWhere(\Illuminate\Support\Facades\DB::raw($clienteNombreExpr), 'like', $s);
                });
            }
            $allowedOrden = [
                'estado_solicitud' => 'es.nombre',
                'cliente' => \Illuminate\Support\Facades\DB::raw($clienteNombreExpr),
                'solicitud_acf' => 's.numero_solicitud_acf',
                'solicitud_cliente' => 's.numero_solicitud_cliente',
            ];
            if ($ordenarPor && isset($allowedOrden[$ordenarPor])) {
                $query->orderBy($allowedOrden[$ordenarPor], 'asc');
            } else {
                $query->orderBy('es.nombre', 'asc')->orderBy('s.id_solicitud_pk', 'asc');
            }
            $solicitudes = $query->distinct()->get();
            return view($view, compact('fecha', 'modulo', 'solicitudes', 'ordenarPor', 'search', 'estadoSolicitud', 'fechaGeneracion'));
        }

        if ($moduloLower === 'bitacora') {
            $search = $request->query('search');
            $accion = $request->query('accion');
            $usuario = $request->query('usuario');
            $objeto = $request->query('objeto');
            $desde = $request->query('desde');
            $hasta = $request->query('hasta');
            $sort = $request->query('sort', 'fecha_evento');
            $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
            $q = \Illuminate\Support\Facades\DB::table('tbl_ms_bitacora as b')
                ->leftJoin('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'b.id_usuario_fk')
                ->leftJoin('tbl_objetos as o', 'o.id_objetos_pk', '=', 'b.id_objetos_fk')
                ->select(['b.id_bitacora_pk as id', 'b.fecha_evento', 'b.accion', 'b.descripcion', 'b.creado_por', 'b.fecha_creacion', 'u.usuario as usuario_nombre', 'o.nombre_objeto as objeto_nombre']);
            if (!empty($accion)) {
                $q->where('b.accion', $accion);
            }
            if (!empty($usuario)) {
                $q->where('u.usuario', 'like', "%$usuario%");
            }
            if (!empty($objeto)) {
                $q->where('o.nombre_objeto', 'like', "%$objeto%");
            }
            if (!empty($search)) {
                $s = "%$search%";
                $q->where(function ($w) use ($s) {
                    $w->where('b.accion', 'like', $s)->orWhere('b.descripcion', 'like', $s);
                });
            }
            if (!empty($desde)) {
                $q->whereDate('b.fecha_evento', '>=', $desde);
            }
            if (!empty($hasta)) {
                $q->whereDate('b.fecha_evento', '<=', $hasta);
            }
            $allowedSort = ['fecha_evento' => 'b.fecha_evento', 'usuario' => 'u.usuario', 'objeto' => 'o.nombre_objeto', 'accion' => 'b.accion', 'fecha_creacion' => 'b.fecha_creacion'];
            $q->orderBy($allowedSort[$sort] ?? 'b.fecha_evento', $direction);
            $rows = $q->get();
            $items = $rows->map(function ($r) {
                return [
                    'id' => $r->id,
                    'fecha_evento' => $r->fecha_evento,
                    'accion' => $r->accion,
                    'descripcion' => $r->descripcion,
                    'creado_por' => $r->creado_por,
                    'fecha_creacion' => $r->fecha_creacion,
                    'usuario' => ['usuario' => $r->usuario_nombre],
                    'objeto' => ['nombre_objeto' => $r->objeto_nombre],
                ];
            });
            return view($view, compact('fecha', 'modulo', 'items', 'search', 'accion', 'usuario', 'objeto', 'desde', 'hasta', 'sort', 'direction'));
        }

        if ($moduloLower === 'facturas') {
            $search = $request->query('search');
            $estadoFactura = $request->query('estado_factura');
            $query = \App\Models\Factura::with(['cliente.empresa', 'cliente.personas', 'estadoFactura', 'cai', 'detalles.servicio']);
            if (!empty($estadoFactura)) {
                if (is_numeric($estadoFactura)) {
                    $query->where('id_estado_factura_fk', (int)$estadoFactura);
                } else {
                    $query->whereHas('estadoFactura', function ($q) use ($estadoFactura) {
                        $q->where('nombre_estado', 'like', "%$estadoFactura%");
                    });
                }
            }
            if (!empty($search)) {
                $s = "%$search%";
                $query->where(function ($q) use ($s) {
                    $q->where('numero', 'like', $s)->orWhereHas('cliente', function ($c) use ($s) {
                        $c->where('nombre', 'like', $s);
                    });
                });
            }
            $facturas = $query->orderBy('fecha', 'desc')->get();
            $totalFacturas = $facturas->count();
            $pagadas = $facturas->where('estadoFactura.nombre', 'Pagada')->sum('total');
            $pendientes = $facturas->where('estadoFactura.nombre', 'Pendiente')->sum('total');
            $anuladas = $facturas->where('estadoFactura.nombre', 'Anulada')->sum('total');
            return view($view, compact('fecha', 'modulo', 'facturas', 'totalFacturas', 'pagadas', 'pendientes', 'anuladas', 'search', 'estadoFactura'));
        }

        return view($view, compact('fecha', 'modulo'));
    }


    public function reporte(Request $request)
    {
        $query = Proyecto::with(['ordenServicio', 'estadoProyecto']);


        if ($estado = $request->input('estado')) {
            $query->whereHas('estadoProyecto', function ($q) use ($estado) {
                $q->where('codigo', $estado);
            });
        }
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre_proyecto', 'like', "%$q%")
                    ->orWhere('descripcion_proyecto', 'like', "%$q%")
                    ->orWhereHas('ordenServicio', function ($subQuery) use ($q) {
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

        $normalizeEstado = function ($p): string {
            $estado = $p->estadoProyecto;
            $raw = $estado->codigo ?? $estado->nombre ?? $estado->nombre_estado ?? '';
            return strtoupper(trim((string) $raw));
        };

        $activos = $proyectos->filter(function ($p) use ($normalizeEstado) {
            return in_array($normalizeEstado($p), ['AC', 'ACTIVO'], true);
        })->count();
        $finalizados = $proyectos->filter(function ($p) use ($normalizeEstado) {
            return in_array($normalizeEstado($p), ['FIN', 'FINALIZADO'], true);
        })->count();
        $inactivos = $total - $activos - $finalizados;

        $fecha = \App\Helpers\DateHelper::nowFormatted('d/m/Y');
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
                    ->orWhereHas('proyecto', function ($subQuery) use ($q) {
                        $subQuery->where('nombre_proyecto', 'like', "%$q%");
                    })
                    ->orWhereHas('categoria', function ($subQuery) use ($q) {
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
                    ->orWhereHas('proyecto', function ($subQuery) use ($q) {
                        $subQuery->where('nombre_proyecto', 'like', "%$q%");
                    })
                    ->orWhereHas('categoria', function ($subQuery) use ($q) {
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

        $fecha = \App\Helpers\DateHelper::nowFormatted('d/m/Y');
        $modulo = 'movimientos-proyecto';

        return view('admin.reporte-movimientos-proyecto', compact(
            'ingresos',
            'gastos',
            'totalIngresos',
            'totalGastos',
            'sumaIngresos',
            'sumaGastos',
            'balance',
            'fecha',
            'modulo'
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

        $fecha = \App\Helpers\DateHelper::nowFormatted('d/m/Y');
        $modulo = 'proyecto-financiero';

        return view('admin.reporte-proyecto-financiero', compact(
            'proyecto',
            'ingresos',
            'gastos',
            'totalIngresos',
            'totalGastos',
            'balance',
            'movimientos',
            'fecha',
            'modulo'
        ));
    }
}
