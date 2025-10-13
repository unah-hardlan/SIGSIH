<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\EstadoProyecto;
use App\Models\EstadoTicket;
use App\Models\OrdenServicio;
use App\Models\Proyecto;
use App\Models\Producto;
use App\Models\Ticket;
use App\Models\EmpresaCliente;
use App\Models\NombreEmpresa;
use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Indicadores agregados (KPIs) para las tarjetas del dashboard.
     * Notas/Asunciones:
     * - "Empresas Activas": se cuenta total de EmpresaCliente por simplicidad.
     * - Tickets abiertos/cerrados: se infieren por el nombre del estado (LIKE 'Abierto%' / 'Cerrado%').
     * - Proyectos activos/finalizados: se infieren por nombre del estado (LIKE 'Finaliz%').
     */
    public function indicators(): JsonResponse
    {
        // Usuarios
        $totalUsuarios = \App\Models\Usuario::query()->count();

        // Empresas: preferimos catálogo de nombres si existe; de lo contrario, EmpresaCliente
        $empresasActivas = 0;
        try {
            if (Schema::hasTable('tbl_nombre_empresa')) {
                $empresasActivas = NombreEmpresa::query()->count();
            }
        } catch (\Throwable $e) { $empresasActivas = 0; }
        if ($empresasActivas === 0) {
            try { $empresasActivas = EmpresaCliente::query()->count(); } catch (\Throwable $e) { $empresasActivas = 0; }
        }

        // Órdenes de servicio, Cotizaciones
        $ordenesServicio = class_exists(OrdenServicio::class)
            ? OrdenServicio::query()->count()
            : 0;
        $cotizaciones = class_exists(Cotizacion::class)
            ? Cotizacion::query()->count()
            : 0;

        // Proyectos: activos vs finalizados
        $proyectosActivos = 0;
        $proyectosFinalizados = 0;
        if (class_exists(Proyecto::class)) {
            // 1) Intentar por catálogo de estados (columna real 'nombre')
            $idsFinalizado = collect();
            $idsActivo = collect();
            try {
                if (Schema::hasTable('tbl_estado_proyecto')) {
                    $idsFinalizado = EstadoProyecto::query()
                        ->where(function ($q) {
                            $q->where('nombre', 'like', 'Finaliz%')
                              ->orWhere('nombre', 'like', 'Cerrad%')
                              ->orWhere('nombre', 'like', 'Terminad%');
                        })
                        ->pluck('id_estado_proyecto_pk');

                    $idsActivo = EstadoProyecto::query()
                        ->where(function ($q) {
                            $q->where('nombre', 'like', 'Activo%')
                              ->orWhere('nombre', 'like', 'En%')
                              ->orWhere('nombre', 'like', 'Progres%')
                              ->orWhere('nombre', 'like', 'Proceso%');
                        })
                        ->pluck('id_estado_proyecto_pk');
                }
            } catch (\Throwable $e) { /* fallback por fecha */ }

            if ($idsFinalizado->isNotEmpty() || $idsActivo->isNotEmpty()) {
                if ($idsFinalizado->isNotEmpty()) {
                    $proyectosFinalizados = Proyecto::query()
                        ->whereIn('id_estado_proyecto_fk', $idsFinalizado)
                        ->count();
                }
                if ($idsActivo->isNotEmpty()) {
                    $proyectosActivos = Proyecto::query()
                        ->whereIn('id_estado_proyecto_fk', $idsActivo)
                        ->count();
                }
                // Si alguno de los conjuntos está vacío, completar con 0 explícito
                $proyectosFinalizados = (int) $proyectosFinalizados;
                $proyectosActivos = (int) $proyectosActivos;
            } else {
                // 2) Fallback por fecha, evitando valores inválidos por defecto como '0000-00-00'
                $proyectosFinalizados = Proyecto::query()
                    ->whereNotNull('fecha_finalizacion_proyecto')
                    ->whereNotIn('fecha_finalizacion_proyecto', ['0000-00-00', '0000-00-00 00:00:00'])
                    ->count();

                $proyectosActivos = Proyecto::query()
                    ->where(function ($q) {
                        $q->whereNull('fecha_finalizacion_proyecto')
                          ->orWhereIn('fecha_finalizacion_proyecto', ['0000-00-00', '0000-00-00 00:00:00']);
                    })
                    ->count();
            }
        }

        // Tickets abiertos/cerrados
        $ticketsAbiertos = 0;
        $ticketsCerrados = 0;
        if (class_exists(Ticket::class)) {
            $idsAbierto = collect();
            $idsCerrado = collect();
            try {
                if (Schema::hasTable('tbl_estado_ticket')) {
                    $idsAbierto = EstadoTicket::query()
                        ->where(function($q){
                            $q->where('nombre', 'like', 'Abierto%')
                              ->orWhere('nombre', 'like', 'En%')
                              ->orWhere('nombre', 'like', 'Pendiente%');
                        })
                        ->pluck('id_estado_ticket_pk');
                    $idsCerrado = EstadoTicket::query()
                        ->where(function($q){
                            $q->where('nombre', 'like', 'Cerrado%')
                              ->orWhere('nombre', 'like', 'Resuelto%')
                              ->orWhere('nombre', 'like', 'Finaliz%');
                        })
                        ->pluck('id_estado_ticket_pk');
                }
            } catch (\Throwable $e) { /* fallback total abierto abajo */ }

            if ($idsAbierto->isNotEmpty()) {
                $ticketsAbiertos = Ticket::query()->whereIn('id_estado_ticket_fk', $idsAbierto)->count();
            }
            if ($idsCerrado->isNotEmpty()) {
                $ticketsCerrados = Ticket::query()->whereIn('id_estado_ticket_fk', $idsCerrado)->count();
            }
            if ($idsAbierto->isEmpty() && $idsCerrado->isEmpty()) {
                $ticketsCerrados = 0;
                $ticketsAbiertos = Ticket::query()->count();
            }
        }

        // Inventario: total de productos
        $inventarioProductos = class_exists(Producto::class)
            ? Producto::query()->count()
            : 0;

    // Reportes generados: total de registros en bitácora como proxy
    $reportesGenerados = Bitacora::query()->count();

        return response()->json([
            'totalUsuarios' => $totalUsuarios,
            'empresasActivas' => $empresasActivas,
            'ordenesServicio' => $ordenesServicio,
            'cotizaciones' => $cotizaciones,
            'proyectosActivos' => $proyectosActivos,
            'proyectosFinalizados' => $proyectosFinalizados,
            'ticketsAbiertos' => $ticketsAbiertos,
            'ticketsCerrados' => $ticketsCerrados,
            'inventarioProductos' => $inventarioProductos,
            'reportesGenerados' => $reportesGenerados,
        ]);
    }
    public function ordenesPorEstado(): JsonResponse
    {
        $abiertas = OrdenServicio::whereNull('fecha_inicio')->count();
        $enProceso = OrdenServicio::whereNotNull('fecha_inicio')
            ->whereNull('fecha_finalizacion')
            ->count();
        $cerradas = OrdenServicio::whereNotNull('fecha_finalizacion')->count();

        $labels = ['Abiertas', 'En Proceso', 'Cerradas'];
        $data = [$abiertas, $enProceso, $cerradas];

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function cotizacionesPorMes(Request $request): JsonResponse
    {
        $year = (int) ($request->query('year') ?: Carbon::now()->year);

        $counts = array_fill(1, 12, 0);
        Cotizacion::query()
            ->selectRaw('MONTH(fecha_cotizacion) as mes, COUNT(*) as total')
            ->whereYear('fecha_cotizacion', $year)
            ->groupBy('mes')
            ->get()
            ->each(function ($row) use (&$counts) {
                $counts[(int) $row->mes] = (int) $row->total;
            });

        $labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        // Asegurar 12 valores
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $counts[$i] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'year' => $year,
        ]);
    }

    public function proyectosPorEstado(): JsonResponse
    {
        $rows = Proyecto::query()
            ->selectRaw('id_estado_proyecto_fk, COUNT(*) as total')
            ->groupBy('id_estado_proyecto_fk')
            ->get();

        $estadoIds = $rows->pluck('id_estado_proyecto_fk')->filter();
        $estados = collect();
        try {
            if ($estadoIds->isNotEmpty() && Schema::hasTable('tbl_estado_proyecto')) {
                $estados = EstadoProyecto::query()
                    ->whereIn('id_estado_proyecto_pk', $estadoIds)
                    ->pluck('nombre', 'id_estado_proyecto_pk');
            }
        } catch (\Throwable $e) { $estados = collect(); }

        $labels = [];
        $data = [];
        foreach ($rows as $row) {
            $labels[] = $estados[$row->id_estado_proyecto_fk] ?? 'Sin estado';
            $data[] = (int) $row->total;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}