<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketClienteController extends Controller
{
    /**
     * Listado de tickets del cliente autenticado para el portal cliente.
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

            $clienteIds = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->pluck('id_cliente_fk')
                ->all();
            if (empty($clienteIds)) {
                return response()->json(['data' => []]);
            }

            $rows = DB::table('tbl_tickets as t')
                ->leftJoin('tbl_estado_ticket as et', 'et.id_estado_ticket_pk', '=', 't.id_estado_ticket_fk')
                ->leftJoin('tbl_persona as p', 'p.id_persona_pk', '=', 't.id_tecnico_fk')
                ->whereIn('t.id_cliente_fk', $clienteIds)
                ->orderByDesc('t.id_ticket_pk')
                ->get([
                    't.id_ticket_pk as id',
                    't.fecha_creacion',
                    't.descripcion_ticket',
                    DB::raw('COALESCE(et.nombre, "") as estado'),
                    DB::raw('COALESCE(CONCAT_WS(" ", p.primer_nombre, p.primer_apellido), "") as tecnico'),
                ]);

            $fmtDate = function ($v) {
                if (!$v) return null;
                $s = (string)$v;
                return substr($s, 0, 10);
            };

            $items = $rows->map(function ($r) use ($fmtDate) {
                $numero = 'TCK-' . now()->format('Ym') . '-' . str_pad((string)((int)($r->id ?? 0)), 6, '0', STR_PAD_LEFT);
                return [
                    'id' => (int)($r->id ?? 0),
                    'numero' => $numero,
                    'fecha_creacion' => $fmtDate($r->fecha_creacion ?? null),
                    'estado' => (string)($r->estado ?? ''),
                    'tecnico' => (string)($r->tecnico ?? ''),
                    'descripcion' => (string)($r->descripcion_ticket ?? ''),
                ];
            });

            return response()->json(['data' => $items]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['data' => []]);
        }
    }
}
