<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Usuario;
use App\Models\Rol;
use App\Notifications\SystemNotification;
use Illuminate\Validation\Rule;
use App\Http\Resources\SolicitudResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Eager-load cliente.empresa y cliente.personas para garantizar que la API devuelva
        // información tanto de empresa como de persona cuando exista.
        $query = Solicitud::with(['cliente.empresa', 'cliente.personas', 'estadoSolicitud', 'contacto']);

        // Filtros opcionales
        if ($request->filled('id_cliente_fk')) {
            $query->where('id_cliente_fk', $request->id_cliente_fk);
        }

        if ($request->filled('id_estado_solicitud_fk')) {
            $query->where('id_estado_solicitud_fk', $request->id_estado_solicitud_fk);
        }

        if ($request->filled('numero_solicitud_acf')) {
            $query->where('numero_solicitud_acf', (int) $request->numero_solicitud_acf);
        }

        if ($request->filled('numero_solicitud_cliente')) {
            $query->where('numero_solicitud_cliente', (int) $request->numero_solicitud_cliente);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($sub) use ($search) {
                $sub->where('descripcion_problema', 'like', "%{$search}%")
                    ->orWhereHas('cliente.empresa', function ($empresaQuery) use ($search) {
                        $empresaQuery->where('nombre_comercial', 'like', "%{$search}%")
                            ->orWhere('razon_social', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $solicitudes = $query->paginate(max(1, $perPage));

        return SolicitudResource::collection($solicitudes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente_fk' => 'required|integer|exists:tbl_cliente,id_cliente_pk',
            'nombre_solicitud' => 'nullable|string|max:150',
            'descripcion_problema' => 'required|string|max:500',
            'id_estado_solicitud_fk' => 'required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => [
                'required',
                'integer',
                Rule::exists('tbl_contacto', 'id_contacto_pk')->where(function ($q) use ($request) {
                    return $q->where('id_cliente_fk', $request->input('id_cliente_fk'));
                }),
            ],
        ]);

        // Usar transacción para evitar condiciones de carrera al calcular correlativos por cliente
        $solicitud = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            // Calcular correlativo global ACF con mínimo 1000, bloqueando la última fila para reducir condiciones de carrera
            $lastAcfRow = \Illuminate\Support\Facades\DB::table('tbl_solicitud')
                ->select('numero_solicitud_acf')
                ->orderByDesc('numero_solicitud_acf')
                ->lockForUpdate()
                ->first();

            $maxAcf = $lastAcfRow?->numero_solicitud_acf;
            if ($maxAcf === null) {
                $nextAcf = 1000;
            } else {
                $maxAcf = (int) $maxAcf;
                $nextAcf = $maxAcf < 1000 ? 1000 : ($maxAcf + 1);
            }

            // Bloquear el conjunto de filas de este cliente para calcular el correlativo del cliente de forma segura
            $lockRow = \Illuminate\Support\Facades\DB::table('tbl_solicitud')
                ->where('id_cliente_fk', $validated['id_cliente_fk'])
                ->lockForUpdate()
                ->selectRaw('MAX(numero_solicitud_cliente) as max_cli')
                ->first();

            $maxCli = $lockRow?->max_cli;
            if ($maxCli === null) {
                $nextCli = 1000;
            } else {
                $maxCli = (int) $maxCli;
                $nextCli = $maxCli < 1000 ? 1000 : ($maxCli + 1);
            }

            $sol = new \App\Models\Solicitud();
            $sol->fill($validated);
            $sol->numero_solicitud_acf = $nextAcf;
            $sol->numero_solicitud_cliente = $nextCli;
            $sol->save();

            return $sol;
        });

        // Enviar notificación a técnicos (rol que contenga 'tecn' en su nombre) — tanto técnicos con rol principal
        // como técnicos en la tabla pivot (roles secundarios)
        try {
            $solicitud->load(['cliente']);
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
                    $clienteNombre = $solicitud->cliente->nombre ?? ($solicitud->cliente->nombre_comercial ?? 'Cliente');
                    $payload = [
                        'title' => 'Nueva solicitud',
                        'body' => "Nueva solicitud #{$solicitud->numero_solicitud_acf} para {$clienteNombre}",
                        'url' => '/admin/solicitudes',
                        'icon' => 'fa-ticket-alt',
                        'severity' => 'info',
                        'module' => 'solicitudes',
                        'meta' => ['id_solicitud_pk' => $solicitud->getKey()]
                    ];

                    foreach ($users as $u) {
                        try {
                            $u->notify(new SystemNotification($payload));
                        } catch (\Throwable $t) {
                            // No detener la creación por fallos en notificaciones
                            Log::warning('Failed to notify user ' . $u->id_usuario_pk . ' about new solicitud: ' . $t->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Registrar y continuar
            Log::error('Error sending new-solicitud notifications: ' . $e->getMessage());
        }

        return new SolicitudResource($solicitud->load(['cliente.empresa', 'cliente.personas', 'estadoSolicitud', 'contacto']));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $solicitud = Solicitud::with(['cliente.empresa', 'cliente.personas', 'estadoSolicitud', 'contacto'])->findOrFail($id);
        return new SolicitudResource($solicitud);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::with(['cliente.empresa', 'cliente.personas', 'estadoSolicitud', 'contacto'])->findOrFail($id);
        $oldEstadoId = $solicitud->id_estado_solicitud_fk;

        $validatedData = $request->validate([
            'id_cliente_fk' => 'sometimes|required|integer|exists:tbl_cliente,id_cliente_pk',
            'nombre_solicitud' => 'sometimes|nullable|string|max:150',
            'descripcion_problema' => 'sometimes|required|string|max:500',
            'id_estado_solicitud_fk' => 'sometimes|required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('tbl_contacto', 'id_contacto_pk')->where(function ($q) use ($request) {
                    $clienteId = $request->input('id_cliente_fk');
                    if ($clienteId === null) return $q; // if cliente not provided in this update, skip client filter
                    return $q->where('id_cliente_fk', $clienteId);
                }),
            ],
        ]);

        $solicitud->update($validatedData);

        // Si cambió el estado, notificar al/los usuarios cliente(s) asociados
        try {
            if (array_key_exists('id_estado_solicitud_fk', $validatedData)) {
                $newEstadoId = (int) $validatedData['id_estado_solicitud_fk'];
                if ((int) $oldEstadoId !== $newEstadoId) {
                    $oldNombre = optional(\App\Models\EstadoSolicitud::find($oldEstadoId))->nombre ?? 'N/A';
                    $newNombre = optional(\App\Models\EstadoSolicitud::find($newEstadoId))->nombre ?? 'N/A';

                    // Buscar usuarios (clientes) vinculados al cliente de esta solicitud
                    $userIds = \Illuminate\Support\Facades\DB::table('tbl_cliente_persona as cp')
                        ->join('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                        ->join('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'p.id_usuario_fk')
                        ->where('cp.id_cliente_fk', $solicitud->id_cliente_fk)
                        ->pluck('u.id_usuario_pk')
                        ->all();

                    if (!empty($userIds)) {
                        $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();
                        $cliFmt = 'CLI-' . now()->format('Ymd') . '-' . $solicitud->getKey();
                        $payload = [
                            'title' => 'Cambio de estado de solicitud',
                            'body' => "Tu solicitud {$cliFmt} cambió de {$oldNombre} a {$newNombre}",
                            'url' => '/cliente/solicitudes',
                            'icon' => 'fa-ticket-alt',
                            'severity' => 'info',
                            'module' => 'solicitudes',
                            'meta' => [
                                'id_solicitud_pk' => $solicitud->getKey(),
                                'old_estado' => $oldNombre,
                                'new_estado' => $newNombre,
                            ],
                        ];

                        foreach ($users as $u) {
                            try {
                                $u->notify(new SystemNotification($payload));
                            } catch (\Throwable $t) {
                                \Illuminate\Support\Facades\Log::warning('Failed to notify client user ' . $u->id_usuario_pk . ' about solicitud status change: ' . $t->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error notifying client on solicitud status change: ' . $e->getMessage());
        }

        return new SolicitudResource($solicitud->load(['cliente.empresa', 'cliente.personas', 'estadoSolicitud', 'contacto']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->delete();

        return response()->json([
            'message' => 'Solicitud eliminada correctamente'
        ], Response::HTTP_OK);
    }
}
