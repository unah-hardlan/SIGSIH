<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrdenServicio;

class OrdenServicioClienteController extends Controller
{
    /**
     * Listado simple de órdenes de servicio del cliente autenticado (para UI del portal).
     * Devuelve una lista plana con los campos necesarios para la tabla.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['data' => []], 401);
            }

            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona) {
                return response()->json(['data' => []]);
            }

            // Clientes asociados a esta persona
            $clienteIds = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->pluck('id_cliente_fk')
                ->all();
            if (empty($clienteIds)) {
                return response()->json(['data' => []]);
            }

            // Ordenes cuya solicitud pertenece a alguno de los clientes del usuario
            $rows = DB::table('tbl_orden_servicio as os')
                ->join('tbl_solicitud as s', 's.id_solicitud_pk', '=', 'os.id_solicitud_servicio_fk')
                ->leftJoin('tbl_estado_orden_servicio as eos', 'eos.id_estado_orden_servicio_pk', '=', 'os.id_estado_orden_servicio_fk')
                ->leftJoin('tbl_persona as t', 't.id_persona_pk', '=', 'os.id_tecnico_fk')
                ->whereIn('s.id_cliente_fk', $clienteIds)
                ->orderByDesc('os.id_orden_servicio_pk')
                ->get([
                    'os.id_orden_servicio_pk as id',
                    'os.numero_orden_servicio as numero',
                    'os.fecha_creada',
                    'os.fecha_recepcion',
                    DB::raw('COALESCE(eos.nombre, "") as estado'),
                    DB::raw('COALESCE(CONCAT_WS(" ", t.primer_nombre, t.primer_apellido), "") as tecnico'),
                ]);

            $fmtDate = function ($v) {
                if (!$v) return null; // dejar que UI maneje null
                $s = (string) $v;
                // Asegurar formato YYYY-MM-DD
                return substr($s, 0, 10);
            };

            $items = $rows->map(function ($r) use ($fmtDate) {
                $numero = (string) ($r->numero ?? '');
                if ($numero === '') {
                    // Fallback si no tiene generado el número aún
                    $numero = 'OS-' . now()->format('Ym') . '-' . str_pad((string) ((int) ($r->id ?? 0)), 6, '0', STR_PAD_LEFT);
                }
                return [
                    'id' => (int) ($r->id ?? 0),
                    'numero' => $numero,
                    'fecha_creada' => $fmtDate($r->fecha_creada ?? null),
                    'fecha_recepcion' => $fmtDate($r->fecha_recepcion ?? null),
                    'estado' => (string) ($r->estado ?? ''),
                    'tecnico' => (string) ($r->tecnico ?? ''),
                ];
            });

            return response()->json(['data' => $items]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['data' => []]);
        }
    }

    /**
     * Detalle de una orden de servicio para el cliente autenticado.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona) return response()->json(['error' => 'Not found'], 404);

            $clienteIds = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->pluck('id_cliente_fk')
                ->all();
            if (empty($clienteIds)) return response()->json(['error' => 'Not found'], 404);

            $row = DB::table('tbl_orden_servicio as os')
                ->join('tbl_solicitud as s', 's.id_solicitud_pk', '=', 'os.id_solicitud_servicio_fk')
                ->leftJoin('tbl_estado_orden_servicio as eos', 'eos.id_estado_orden_servicio_pk', '=', 'os.id_estado_orden_servicio_fk')
                ->leftJoin('tbl_persona as t', 't.id_persona_pk', '=', 'os.id_tecnico_fk')
                ->leftJoin('tbl_contacto as co', 'co.id_contacto_pk', '=', 's.id_contacto_fk')
                ->where('os.id_orden_servicio_pk', (int) $id)
                ->whereIn('s.id_cliente_fk', $clienteIds)
                ->select([
                    'os.id_orden_servicio_pk as id',
                    'os.fecha_recepcion',
                    'os.fecha_inicio',
                    'os.fecha_finalizacion',
                    'os.observaciones',
                    'os.diagnostico_tecnico',
                    'os.diagnostico_cliente',
                    'os.calificacion_servicio',
                    'eos.codigo as estado_codigo',
                    'eos.nombre as estado_nombre',
                    's.id_solicitud_pk',
                    's.numero_solicitud_acf',
                    's.numero_solicitud_cliente',
                    's.descripcion_problema',
                    's.id_cliente_fk',
                    't.primer_nombre as t_pn',
                    't.segundo_nombre as t_sn',
                    't.primer_apellido as t_pa',
                    't.segundo_apellido as t_sa',
                    't.dni as t_dni',
                    'co.tipo_contacto as c_tipo',
                    'co.valor_contacto as c_valor'
                ])
                ->first();

            if (!$row) return response()->json(['error' => 'Not found'], 404);

            // Cliente empresa/persona (para el encabezado)
            $client = DB::table('tbl_cliente as cl')
                ->leftJoin('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'cl.id_cliente_pk')
                ->leftJoin('tbl_cliente_persona as cp', 'cp.id_cliente_fk', '=', 'cl.id_cliente_pk')
                ->leftJoin('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                ->where('cl.id_cliente_pk', $row->id_cliente_fk)
                ->selectRaw('COALESCE(ce.nombre_comercial, ce.razon_social, CONCAT_WS(" ", p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido)) as nombre_empresa')
                ->first();

            $payload = [
                'id_orden_servicio_pk' => (int) $row->id,
                'fecha_recepcion' => $row->fecha_recepcion,
                'fecha_inicio' => $row->fecha_inicio,
                'fecha_finalizacion' => $row->fecha_finalizacion,
                'observaciones' => (string) ($row->observaciones ?? ''),
                'diagnostico_tecnico' => (string) ($row->diagnostico_tecnico ?? ''),
                'diagnostico_cliente' => (string) ($row->diagnostico_cliente ?? ''),
                'calificacion_servicio' => (string) ($row->calificacion_servicio ?? ''),
                'estado' => [
                    'codigo' => (string) ($row->estado_codigo ?? ''),
                    'nombre' => (string) ($row->estado_nombre ?? ''),
                ],
                'tecnico' => [
                    'primer_nombre' => (string) ($row->t_pn ?? ''),
                    'segundo_nombre' => (string) ($row->t_sn ?? ''),
                    'primer_apellido' => (string) ($row->t_pa ?? ''),
                    'segundo_apellido' => (string) ($row->t_sa ?? ''),
                    'dni' => (string) ($row->t_dni ?? ''),
                ],
                'solicitud_servicio' => [
                    'id_solicitud_pk' => (int) $row->id_solicitud_pk,
                    'numero_solicitud_acf' => (int) ($row->numero_solicitud_acf ?? 0),
                    'numero_solicitud_cliente' => (int) ($row->numero_solicitud_cliente ?? 0),
                    'descripcion_problema' => (string) ($row->descripcion_problema ?? ''),
                    'cliente' => [
                        'empresa' => [
                            'nombre_comercial' => (string) ($client->nombre_empresa ?? ''),
                        ],
                    ],
                    'contacto' => [
                        'tipo_contacto' => (string) ($row->c_tipo ?? ''),
                        'valor_contacto' => (string) ($row->c_valor ?? ''),
                    ],
                ],
            ];

            return response()->json($payload);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    /**
     * Calificar la orden de servicio por parte del cliente.
     * Requiere que la OS pertenezca a alguno de los clientes del usuario y que el estado sea final/cerrado.
     */
    public function calificar(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'calificacion' => 'required|string|in:excelente,bueno,regular,deficiente',
        ]);

        $user = auth()->user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        if (!$persona) return response()->json(['success' => false, 'message' => 'Perfil no encontrado'], 404);

        $clienteIds = DB::table('tbl_cliente_persona')
            ->where('id_persona_fk', $persona->id_persona_pk)
            ->pluck('id_cliente_fk')
            ->all();
        if (empty($clienteIds)) return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);

        // Validar pertenencia y estado cerrado
        $row = DB::table('tbl_orden_servicio as os')
            ->join('tbl_solicitud as s', 's.id_solicitud_pk', '=', 'os.id_solicitud_servicio_fk')
            ->leftJoin('tbl_estado_orden_servicio as eos', 'eos.id_estado_orden_servicio_pk', '=', 'os.id_estado_orden_servicio_fk')
            ->where('os.id_orden_servicio_pk', (int)$id)
            ->whereIn('s.id_cliente_fk', $clienteIds)
            ->select(['os.id_orden_servicio_pk as id', 'eos.nombre as estado_nombre', 'eos.codigo as estado_codigo'])
            ->first();
        if (!$row) return response()->json(['success' => false, 'message' => 'Orden no encontrada'], 404);

        $isClosed = false;
        $n = strtolower((string)($row->estado_nombre ?? ''));
        $c = strtolower((string)($row->estado_codigo ?? ''));
        if (str_contains($n, 'cerrad') || $c === 'cer') $isClosed = true;
        if (!$isClosed) {
            try {
                $isClosed = (bool) DB::table('tbl_estado_orden_servicio')
                    ->where('codigo', $row->estado_codigo)
                    ->value('es_final');
            } catch (\Throwable $_) {
            }
        }
        if (!$isClosed) {
            return response()->json(['success' => false, 'message' => 'Solo puedes calificar órdenes cerradas'], 422);
        }

        // Actualizar calificación
        $os = OrdenServicio::find((int)$id);
        if (!$os) return response()->json(['success' => false, 'message' => 'Orden no encontrada'], 404);

        $calificacion = $request->string('calificacion')->lower();
        $os->calificacion_servicio = $calificacion;
        $os->save();

        // Notificar al técnico asignado
        try {
            $os->loadMissing(['tecnico.usuario']);
            $tecnicoUser = optional($os->tecnico)->usuario;
            if ($tecnicoUser) {
                $osNumero = $os->numero_orden_servicio
                    ?: ('OS-' . now()->format('Ym') . '-' . str_pad((string)$os->getKey(), 6, '0', STR_PAD_LEFT));
                $payload = [
                    'title' => 'Cliente calificó el servicio',
                    'body' => "La Orden de Servicio {$osNumero} fue calificada como '{$calificacion}'.",
                    'url' => '/admin/orden-servicio',
                    'icon' => 'fa-star',
                    'severity' => 'success',
                    'module' => 'orden_servicio',
                    'meta' => [
                        'id_orden_servicio_pk' => $os->getKey(),
                        'numero_orden_servicio' => $osNumero,
                        'calificacion' => $calificacion,
                    ],
                ];
                try {
                    $tecnicoUser->notify(new \App\Notifications\SystemNotification($payload));
                } catch (\Throwable $e) {
                    // intentionally ignore notification failures for technician
                }
            }
        } catch (\Throwable $_) {
            // no-op
        }

        return response()->json(['success' => true]);
    }
}