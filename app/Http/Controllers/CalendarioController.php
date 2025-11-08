<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Usuario;
use App\Models\Rol;
use App\Notifications\SystemNotification;
use App\Http\Resources\CalendarioResource;
use App\Http\Requests\StoreCalendarioRequest;
use App\Http\Requests\UpdateCalendarioRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CalendarioController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $query = Calendario::with([
            'estado',
            'agencia.direccion.ciudad.departamento.pais',
            'ordenServicio',
            'tipoMantenimiento',
            'cliente'
        ]);

        
        if ($request->has('id_estado_calendario_fk')) {
            $query->where('id_estado_calendario_fk', $request->id_estado_calendario_fk);
        }

        
        if ($request->has('id_agencias_fk')) {
            $query->where('id_agencias_fk', $request->id_agencias_fk);
        }

        
        if ($request->has('id_orden_servicio_fk')) {
            $query->where('id_orden_servicio_fk', $request->id_orden_servicio_fk);
        }

        
        if ($request->has('id_tipo_mantenimiento_fk')) {
            $query->where('id_tipo_mantenimiento_fk', $request->id_tipo_mantenimiento_fk);
        }

        
        if ($request->has('id_cliente_fk')) {
            $query->where('id_cliente_fk', $request->id_cliente_fk);
        }

        
        if ($request->has('descripcion_calendario')) {
            $query->where('descripcion_calendario', 'like', '%' . $request->descripcion_calendario . '%');
        }

        
        if ($request->has('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $calendarios = $query->orderBy('fecha', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => CalendarioResource::collection($calendarios->items()),
            'pagination' => [
                'current_page' => $calendarios->currentPage(),
                'per_page' => $calendarios->perPage(),
                'total' => $calendarios->total(),
                'last_page' => $calendarios->lastPage(),
                'from' => $calendarios->firstItem(),
                'to' => $calendarios->lastItem()
            ]
        ]);
    }

    
    public function store(StoreCalendarioRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $calendario = Calendario::create($validated);
        $calendario->load(['estado', 'agencia', 'ordenServicio', 'tipoMantenimiento', 'cliente']);

        return response()->json([
            'success' => true,
            'message' => 'Evento de calendario creado exitosamente',
            'data' => new CalendarioResource($calendario)
        ], 201);
    }

    
    public function show(string $id): JsonResponse
    {
        $calendario = Calendario::with(['estado', 'agencia', 'ordenServicio', 'tipoMantenimiento', 'cliente'])->find($id);

        if (!$calendario) {
            return response()->json([
                'success' => false,
                'message' => 'Evento de calendario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CalendarioResource($calendario)
        ]);
    }

    
    public function update(UpdateCalendarioRequest $request, string $id): JsonResponse
    {
        $calendario = Calendario::find($id);

        if (!$calendario) {
            return response()->json([
                'success' => false,
                'message' => 'Evento de calendario no encontrado'
            ], 404);
        }

        $validated = $request->validated();

        
        $oldEstadoId = $calendario->id_estado_calendario_fk;
        $calendario->update($validated);
        $calendario->load(['estado', 'agencia', 'ordenServicio', 'tipoMantenimiento', 'cliente']);

        
        try {
            $newEstadoId = $calendario->id_estado_calendario_fk;
            if (isset($validated['id_estado_calendario_fk']) && $newEstadoId != $oldEstadoId) {
                
                $oldName = null;
                try {
                    $oldName = \App\Models\EstadoCalendario::find($oldEstadoId)?->nombre;
                } catch (\Throwable $_) {
                    $oldName = null;
                }
                $newName = $calendario->estado?->nombre ?? null;

                
                $rols = Rol::where('rol', 'like', '%tecn%')->get();
                $roleIds = $rols->pluck('id_rol_pk')->all();
                $userIdsPrimary = Usuario::whereIn('id_rol_fk', $roleIds)->pluck('id_usuario_pk')->all();
                $userIdsPivot = \Illuminate\Support\Facades\DB::table('tbl_usuario_rol')
                    ->whereIn('id_rol_fk', $roleIds)
                    ->pluck('id_usuario_fk')
                    ->all();

                $userIds = collect($userIdsPrimary)->merge($userIdsPivot)->unique()->values()->all();
                if (!empty($userIds)) {
                    $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();
                    $clienteNombre = $calendario->cliente->nombre ?? ($calendario->cliente->nombre_comercial ?? 'Cliente');
                    $payload = [
                        'title' => 'Cambio de estado de evento',
                        'body' => "Evento #{$calendario->id_calendario_pk} — {$calendario->descripcion_calendario} cambió de " . ($oldName ?? 'N/A') . " a " . ($newName ?? 'N/A') . " — Cliente: {$clienteNombre}",
                        'url' => '/admin/calendario',
                        'icon' => 'fa-calendar-check',
                        'severity' => 'info',
                        'module' => 'calendario',
                        'meta' => ['id_calendario_pk' => $calendario->getKey(), 'old_estado' => $oldName, 'new_estado' => $newName]
                    ];

                    foreach ($users as $u) {
                        try {
                            $u->notify(new SystemNotification($payload));
                        } catch (\Throwable $e) {
                            Log::warning('Failed to notify user ' . $u->id_usuario_pk . ' about calendario state change: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error sending calendario state-change notifications: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Evento de calendario actualizado exitosamente',
            'data' => new CalendarioResource($calendario)
        ]);
    }

    
    public function destroy(string $id): JsonResponse
    {
        $calendario = Calendario::find($id);

        if (!$calendario) {
            return response()->json([
                'success' => false,
                'message' => 'Evento de calendario no encontrado'
            ], 404);
        }

        $calendario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento de calendario eliminado exitosamente'
        ]);
    }

    
    public function reporte(Request $request)
    {
        $query = Calendario::with([
            'estado',
            'agencia.direccion.ciudad.departamento.pais',
            'ordenServicio',
            'tipoMantenimiento',
            'cliente.empresa',
            'cliente.personas'
        ]);

        
        if ($estado = $request->input('estado')) {
            $query->whereHas('estado', function ($q) use ($estado) {
                $q->where('codigo', $estado);
            });
        }

        
        if ($agencia = $request->input('agencia')) {
            $query->where('id_agencias_fk', $agencia);
        }

        
        if ($cliente = $request->input('cliente')) {
            $query->where('id_cliente_fk', $cliente);
        }

        
        if ($tipo = $request->input('tipo_mantenimiento')) {
            $query->where('id_tipo_mantenimiento_fk', $tipo);
        }

        
        if ($q = $request->input('q')) {
            $query->where('descripcion_calendario', 'like', "%$q%");
        }

        
        if ($desde = $request->input('desde')) {
            $query->where('fecha', '>=', $desde);
        }

        if ($hasta = $request->input('hasta')) {
            $query->where('fecha', '<=', $hasta);
        }

        
        $sortable = [
            'fecha' => 'fecha',
            'estado' => 'id_estado_calendario_fk',
            'cliente' => 'id_cliente_fk',
            'agencia' => 'id_agencias_fk',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'desc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('fecha', 'desc');
        }

        $calendarios = $query->get();
        $total = $calendarios->count();
        $pendientes = $calendarios->filter(function ($c) {
            return $c->estado && $c->estado->codigo === 'PEND';
        })->count();
        $enEjecucion = $calendarios->filter(function ($c) {
            return $c->estado && $c->estado->codigo === 'CAL-EJE';
        })->count();
        $completados = $calendarios->filter(function ($c) {
            return $c->estado && $c->estado->codigo === 'COMP';
        })->count();

        $fecha = now()->format('d/m/Y');
        $modulo = 'calendario';

        return view('admin.reporte-calendario', compact('calendarios', 'total', 'pendientes', 'enEjecucion', 'completados', 'fecha', 'modulo', 'sort', 'direction'));
    }
}
