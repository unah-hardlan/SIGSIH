<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Rol;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Contacto;
use App\Models\EstadoSolicitud;
use App\Models\EstadoTicket;
use App\Models\Persona;
use App\Models\Solicitud;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudClienteController extends Controller
{
    /**
     * Listado simple de solicitudes del cliente autenticado (para UI del portal).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $clienteId = $this->clienteIdFromUser();
            if (!$clienteId) {
                return response()->json(['data' => []]);
            }

            // Resolver por persona -> cliente(s) para evitar desalineaciones de IDs
            $user = auth()->user();
            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona) {
                return response()->json(['data' => []]);
            }

            $rows = DB::table('tbl_solicitud as s')
                ->join('tbl_cliente_persona as cp', 'cp.id_cliente_fk', '=', 's.id_cliente_fk')
                ->leftJoin('tbl_estado_solicitud as es', 'es.id_estado_solicitud_pk', '=', 's.id_estado_solicitud_fk')
                ->leftJoin('tbl_contacto as co', 'co.id_contacto_pk', '=', 's.id_contacto_fk')
                ->where('cp.id_persona_fk', $persona->id_persona_pk)
                ->orderByDesc('s.id_solicitud_pk')
                ->get([
                    's.id_solicitud_pk as id',
                    's.nombre_solicitud',
                    's.numero_solicitud_acf',
                    's.numero_solicitud_cliente',
                    's.descripcion_problema',
                    DB::raw('COALESCE(es.nombre, "") as estado'),
                    DB::raw('COALESCE(co.valor_contacto, "") as contacto'),
                ]);

            // Formateo para UI (prefijos y año)
            $yearDate = now()->format('Ymd');
            $pad = fn($n) => str_pad((string) max(0, (int) $n), 3, '0', STR_PAD_LEFT);
            $items = $rows->map(function ($r) use ($yearDate, $pad) {
                $fmtAcf = 'ACF-' . $pad($r->numero_solicitud_acf);
                // Formato solicitado: CLI-AÑOFECHA-ID (ej. CLI-20251103-168)
                $fmtCli = 'CLI-' . $yearDate . '-' . (int) ($r->id ?? 0);
                return [
                    'id' => (int) ($r->id ?? 0),
                    'nombre_solicitud' => (string) ($r->nombre_solicitud ?? ''),
                    'numero_solicitud_acf' => (int) ($r->numero_solicitud_acf ?? 0),
                    'numero_solicitud_cliente' => (int) ($r->numero_solicitud_cliente ?? 0),
                    'numero_solicitud_acf_fmt' => $fmtAcf,
                    'numero_solicitud_cliente_fmt' => $fmtCli,
                    'descripcion_problema' => (string) ($r->descripcion_problema ?? ''),
                    'estado' => (string) ($r->estado ?? ''),
                    'contacto' => (string) ($r->contacto ?? ''),
                ];
            });

            return response()->json(['data' => $items]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['data' => []]);
        }
    }

    /**
     * Crear una solicitud de soporte del cliente y un ticket asociado.
     * Reglas solicitadas:
     * - Ticket: estado Pendiente; técnico asignado aleatoriamente; descripción igual a la solicitud
     * - Solicitud: estado "En Espera"; números ACF y Cliente autogenerados; contacto correo ingresado
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre_solicitud' => 'required|string|max:150',
            'descripcion_problema' => 'required|string|max:1000',
            'correo_contacto' => 'required|email|max:255',
        ]);

        $clienteId = $this->clienteIdFromUser();
        if (!$clienteId) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
        }

        try {
            DB::beginTransaction();

            // Contacto (email)
            $contacto = Contacto::updateOrCreate(
                [
                    'id_cliente_fk' => $clienteId,
                    'tipo_contacto' => 'email',
                ],
                [
                    'valor_contacto' => $request->string('correo_contacto'),
                ]
            );

            // Estados
            $estadoSolicitudId = EstadoSolicitud::query()
                ->whereRaw('LOWER(nombre) = ?', ['en espera'])
                ->value('id_estado_solicitud_pk');
            if (!$estadoSolicitudId) {
                $estadoSolicitudId = EstadoSolicitud::query()->orderBy('orden')->value('id_estado_solicitud_pk');
            }

            $estadoTicketId = EstadoTicket::query()
                ->whereRaw('LOWER(nombre) LIKE ?', ['pendiente%'])
                ->value('id_estado_ticket_pk');
            if (!$estadoTicketId) {
                $estadoTicketId = EstadoTicket::query()->orderBy('orden')->value('id_estado_ticket_pk');
            }

            // Números autogenerados (mismo criterio que Admin):
            //  - ACF: correlativo global mínimo 1000
            //  - Cliente: correlativo por cliente mínimo 1000
            // Usamos bloqueo para evitar condiciones de carrera

            // ACF global
            $lastAcfRow = DB::table('tbl_solicitud')
                ->select('numero_solicitud_acf')
                ->orderByDesc('numero_solicitud_acf')
                ->lockForUpdate()
                ->first();
            $maxAcf = $lastAcfRow?->numero_solicitud_acf;
            if ($maxAcf === null) {
                $numeroAcf = 1000;
            } else {
                $maxAcf = (int) $maxAcf;
                $numeroAcf = $maxAcf < 1000 ? 1000 : ($maxAcf + 1);
            }

            // Correlativo por cliente
            $lockCli = DB::table('tbl_solicitud')
                ->where('id_cliente_fk', $clienteId)
                ->lockForUpdate()
                ->selectRaw('MAX(numero_solicitud_cliente) as max_cli')
                ->first();
            $maxCli = $lockCli?->max_cli;
            if ($maxCli === null) {
                $numeroCliente = 1000;
            } else {
                $maxCli = (int) $maxCli;
                $numeroCliente = $maxCli < 1000 ? 1000 : ($maxCli + 1);
            }

            // Crear solicitud
            $solicitud = Solicitud::create([
                'id_cliente_fk' => $clienteId,
                'nombre_solicitud' => $request->string('nombre_solicitud'),
                'numero_solicitud_acf' => $numeroAcf,
                'numero_solicitud_cliente' => $numeroCliente,
                'descripcion_problema' => $request->string('descripcion_problema'),
                'id_estado_solicitud_fk' => $estadoSolicitudId,
                'id_contacto_fk' => $contacto?->id_contacto_pk,
            ]);

            // Elegir técnico aleatorio (por rol que contenga TECN)
            $tecnico = Persona::query()
                ->whereHas('usuario.rol', function ($q) {
                    $q->whereRaw('UPPER(rol) like ?', ['%TECN%']);
                })
                ->inRandomOrder()
                ->first();

            if (!$tecnico) {
                // Fallback: alguna persona disponible
                $tecnico = Persona::query()->inRandomOrder()->first();
            }

            if (!$tecnico) {
                throw new \RuntimeException('No hay técnicos disponibles para asignar');
            }

            // Crear ticket vinculado al cliente (no hay FK directa a solicitud en el esquema actual)
            $ticket = Ticket::create([
                'fecha_creacion' => now(),
                'descripcion_ticket' => (string) $request->string('descripcion_problema'),
                'id_estado_ticket_fk' => $estadoTicketId,
                'id_tecnico_fk' => $tecnico->id_persona_pk,
                'id_cliente_fk' => $clienteId,
            ]);

            DB::commit();

            // Notificar a técnicos sobre NUEVA SOLICITUD y NUEVO TICKET
            try {
                // Cargar nombre de cliente (empresa o persona)
                $clienteNombre = DB::table('tbl_cliente as c')
                    ->leftJoin('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'c.id_cliente_pk')
                    ->leftJoin('tbl_cliente_persona as cp', 'cp.id_cliente_fk', '=', 'c.id_cliente_pk')
                    ->leftJoin('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                    ->where('c.id_cliente_pk', $clienteId)
                    ->selectRaw("COALESCE(ce.nombre_comercial, ce.razon_social, CONCAT_WS(' ', p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido)) as nombre")
                    ->value('nombre');
                if (!$clienteNombre) {
                    $clienteNombre = 'Cliente';
                }

                $rols = Rol::where('rol', 'like', '%tecn%')->get();
                if ($rols->isNotEmpty()) {
                    $roleIds = $rols->pluck('id_rol_pk')->all();

                    $userIdsPrimary = Usuario::whereIn('id_rol_fk', $roleIds)->pluck('id_usuario_pk')->all();
                    $userIdsPivot = DB::table('tbl_usuario_rol')
                        ->whereIn('id_rol_fk', $roleIds)
                        ->pluck('id_usuario_fk')
                        ->all();

                    $userIds = collect($userIdsPrimary)->merge($userIdsPivot)->unique()->values()->all();
                    if (!empty($userIds)) {
                        $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();

                        $pad = fn($n) => str_pad((string) max(0, (int) $n), 3, '0', STR_PAD_LEFT);
                        $acfFmt = 'ACF-' . $pad($solicitud->numero_solicitud_acf);
                        $cliFmt = 'CLI-' . now()->format('Ymd') . '-' . (int) $solicitud->id_solicitud_pk;

                        $payloadSolicitud = [
                            'title' => 'Nueva solicitud creada por cliente',
                            'body' => "Nueva solicitud {$acfFmt} ({$cliFmt}) para {$clienteNombre}",
                            'url' => '/admin/solicitudes',
                            'icon' => 'fa-ticket-alt',
                            'severity' => 'info',
                            'module' => 'solicitudes',
                            'meta' => ['id_solicitud_pk' => $solicitud->getKey()],
                        ];
                        $payloadTicket = [
                            'title' => 'Nuevo ticket creado automáticamente',
                            'body' => "Ticket #" . $ticket->getKey() . " generado para {$clienteNombre}",
                            'url' => '/admin/tickets',
                            'icon' => 'fa-headset',
                            'severity' => 'info',
                            'module' => 'tickets',
                            'meta' => ['id_ticket_pk' => $ticket->getKey(), 'id_solicitud_pk' => $solicitud->getKey()],
                        ];

                        foreach ($users as $u) {
                            try {
                                $u->notify(new SystemNotification($payloadSolicitud));
                                $u->notify(new SystemNotification($payloadTicket));
                            } catch (\Throwable $t) {
                                Log::warning('Failed to notify technician user ' . $u->id_usuario_pk . ' about new solicitud/ticket: ' . $t->getMessage());
                            }
                        }
                    }
                }

                // Notificar también al/los usuarios del cliente que su ticket fue creado (enlace del portal cliente)
                try {
                    $clientUserIds = DB::table('tbl_cliente_persona as cp')
                        ->join('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                        ->join('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'p.id_usuario_fk')
                        ->where('cp.id_cliente_fk', $clienteId)
                        ->pluck('u.id_usuario_pk')
                        ->all();
                    if (!empty($clientUserIds)) {
                        $clientUsers = Usuario::whereIn('id_usuario_pk', $clientUserIds)->get();
                        $tckFmt = 'TCK-' . now()->format('Ymd') . '-' . $ticket->getKey();
                        $cliFmt = 'CLI-' . now()->format('Ymd') . '-' . (int) $solicitud->id_solicitud_pk;
                        $payloadClient = [
                            'title' => 'Hemos abierto un ticket',
                            'body' => "Se generó el ticket {$tckFmt} para tu solicitud {$cliFmt}.",
                            'url' => '/cliente/solicitudes',
                            'icon' => 'fa-headset',
                            'severity' => 'info',
                            'module' => 'tickets',
                            'meta' => ['id_ticket_pk' => $ticket->getKey(), 'id_solicitud_pk' => $solicitud->getKey()],
                        ];
                        foreach ($clientUsers as $cu) {
                            try {
                                $cu->notify(new SystemNotification($payloadClient));
                            } catch (\Throwable $t) {
                                Log::warning('Failed to notify client user ' . $cu->id_usuario_pk . ' about new ticket: ' . $t->getMessage());
                            }
                        }
                    }
                } catch (\Throwable $eInner) {
                    Log::warning('Client ticket creation notification failed: ' . $eInner->getMessage());
                }
            } catch (\Throwable $e) {
                Log::error('Error sending notifications for new client solicitud/ticket: ' . $e->getMessage());
            }

            $pad = fn($n) => str_pad((string) max(0, (int) $n), 3, '0', STR_PAD_LEFT);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $solicitud->id_solicitud_pk,
                    'nombre_solicitud' => $solicitud->nombre_solicitud,
                    'numero_solicitud_acf' => $solicitud->numero_solicitud_acf,
                    'numero_solicitud_cliente' => $solicitud->numero_solicitud_cliente,
                    'numero_solicitud_acf_fmt' => 'ACF-' . $pad($solicitud->numero_solicitud_acf),
                    // Formato solicitado: CLI-AÑOFECHA-ID (ej. CLI-20251103-168)
                    'numero_solicitud_cliente_fmt' => 'CLI-' . now()->format('Ymd') . '-' . (int) $solicitud->id_solicitud_pk,
                    'descripcion_problema' => $solicitud->descripcion_problema,
                    'estado' => 'En Espera',
                    'contacto' => $contacto?->valor_contacto,
                ],
                'message' => 'Solicitud y ticket creados correctamente',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear la solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function clienteIdFromUser(): ?int
    {
        $user = auth()->user();
        if (!$user) return null;
        $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        if (!$persona) return null;
        $rel = DB::table('tbl_cliente_persona')->where('id_persona_fk', $persona->id_persona_pk)->first();
        return $rel->id_cliente_fk ?? null;
    }
}
