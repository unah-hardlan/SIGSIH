<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Usuario;
use App\Models\Rol;
use App\Notifications\SystemNotification;
use App\Http\Resources\TicketResource;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['estado', 'tecnico', 'cliente']);

        // Filtro por estado del ticket
        if ($request->has('id_estado_ticket_fk')) {
            $query->where('id_estado_ticket_fk', $request->id_estado_ticket_fk);
        }

        // Filtro por técnico
        if ($request->has('id_tecnico_fk')) {
            $query->where('id_tecnico_fk', $request->id_tecnico_fk);
        }

        // Filtro por cliente
        if ($request->has('id_cliente_fk')) {
            $query->where('id_cliente_fk', $request->id_cliente_fk);
        }

        // Filtro por descripción
        if ($request->has('descripcion_ticket')) {
            $query->where('descripcion_ticket', 'like', '%' . $request->descripcion_ticket . '%');
        }

        // Filtro por rango de fechas
        if ($request->has('fecha_desde')) {
            $query->where('fecha_creacion', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_creacion', '<=', $request->fecha_hasta);
        }

        $tickets = $query->orderBy('fecha_creacion', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => TicketResource::collection($tickets->items()),
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
                'last_page' => $tickets->lastPage(),
                'from' => $tickets->firstItem(),
                'to' => $tickets->lastItem()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $ticket = Ticket::create($validated);
        $ticket->load(['estado', 'tecnico', 'cliente']);

        // Enviar notificación a técnicos (rol que contenga 'tecn' en su nombre)
        try {
            $rols = Rol::where('rol', 'like', '%tecn%')->get();
            if ($rols->isNotEmpty()) {
                $roleIds = $rols->pluck('id_rol_pk')->all();
                $userIdsPrimary = Usuario::whereIn('id_rol_fk', $roleIds)->pluck('id_usuario_pk')->all();
                $userIdsPivot = \Illuminate\Support\Facades\DB::table('tbl_usuario_rol')
                    ->whereIn('id_rol_fk', $roleIds)
                    ->pluck('id_usuario_fk')
                    ->all();

                $userIds = collect($userIdsPrimary)->merge($userIdsPivot)->unique()->values()->all();
                if (!empty($userIds)) {
                    $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();
                    $clienteNombre = $ticket->cliente->nombre ?? ($ticket->cliente->nombre_comercial ?? 'Cliente');
                    $payload = [
                        'title' => 'Nuevo ticket',
                        'body' => "Nuevo ticket #{$ticket->id_ticket_pk} para {$clienteNombre}",
                        'url' => '/admin/tickets',
                        'icon' => 'fa-ticket',
                        'severity' => 'info',
                        'module' => 'tickets',
                        'meta' => ['id_ticket_pk' => $ticket->getKey()]
                    ];

                    foreach ($users as $u) {
                        try {
                            $u->notify(new SystemNotification($payload));
                        } catch (\Throwable $t) {
                            Log::warning('Failed to notify user ' . $u->id_usuario_pk . ' about new ticket: ' . $t->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error sending new-ticket notifications: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket creado exitosamente',
            'data' => new TicketResource($ticket)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $ticket = Ticket::with(['estado', 'tecnico', 'cliente'])->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TicketResource($ticket)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, string $id): JsonResponse
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        $validated = $request->validated();
        $ticket->update($validated);
        $ticket->load(['estado', 'tecnico', 'cliente']);

        return response()->json([
            'success' => true,
            'message' => 'Ticket actualizado exitosamente',
            'data' => new TicketResource($ticket)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket no encontrado'
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket eliminado exitosamente'
        ]);
    }

    /**
     * Generate tickets report
     */
    public function reporte(Request $request)
    {
        $query = Ticket::with([
            'estado',
            'tecnico',
            'cliente' => function ($q) {
                $q->with(['empresa', 'personas']);
            }
        ]);

        // Filtro por estado del ticket
        if ($estado = $request->input('estado')) {
            $query->whereHas('estado', function ($q) use ($estado) {
                $q->where('codigo', $estado);
            });
        }

        // Filtro por técnico
        if ($tecnico = $request->input('tecnico')) {
            $query->where('id_tecnico_fk', $tecnico);
        }

        // Filtro por cliente
        if ($cliente = $request->input('cliente')) {
            $query->where('id_cliente_fk', $cliente);
        }

        // Filtro por descripción
        if ($q = $request->input('q')) {
            $query->where('descripcion_ticket', 'like', "%$q%");
        }

        // Filtro por rango de fechas
        if ($desde = $request->input('desde')) {
            $query->where('fecha_creacion', '>=', $desde);
        }

        if ($hasta = $request->input('hasta')) {
            $query->where('fecha_creacion', '<=', $hasta);
        }

        // Orden
        $sortable = [
            'id' => 'id_ticket_pk',
            'cliente' => 'id_cliente_fk',
            'fecha' => 'fecha_creacion',
            'estado' => 'id_estado_ticket_fk',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'desc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('fecha_creacion', 'desc');
        }

        $tickets = $query->get();
        $total = $tickets->count();
        $pendientes = $tickets->filter(function ($t) {
            return $t->estado && strtolower($t->estado->nombre) === 'pendiente';
        })->count();
        $enProceso = $tickets->filter(function ($t) {
            return $t->estado && strtolower($t->estado->nombre) === 'en proceso';
        })->count();
        $finalizados = $tickets->filter(function ($t) {
            return $t->estado && strtolower($t->estado->nombre) === 'finalizado';
        })->count();

        $fecha = now()->format('d/m/Y');
        $modulo = 'tickets';

        return view('admin.reporte-tickets', compact('tickets', 'total', 'pendientes', 'enProceso', 'finalizados', 'fecha', 'modulo', 'sort', 'direction'));
    }
}
