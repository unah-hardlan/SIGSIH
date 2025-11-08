<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CotizacionClienteController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) return response()->json(['data' => []]);

            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona) return response()->json(['data' => []]);

            
            $clienteIds = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->pluck('id_cliente_fk')
                ->all();

            if (empty($clienteIds)) return response()->json(['data' => []]);

            $rows = DB::table('tbl_cotizacion as c')
                ->leftJoin('tbl_estado_cotizacion as e', 'e.id_estado_cotizacion_pk', '=', 'c.id_estado_cotizacion_fk')
                ->whereIn('c.id_cliente_fk', $clienteIds)
                ->orderByDesc('c.id_cotizacion_pk')
                ->get([
                    'c.id_cotizacion_pk as id',
                    'c.fecha_cotizacion',
                    'c.valido_hasta',
                    'c.subtotal',
                    'c.total',
                    'c.total_impuesto',
                    'c.otros_cargos',
                    'c.impuesto_otros',
                    'c.id_estado_cotizacion_fk',
                    DB::raw('COALESCE(e.nombre, "") as estado_nombre'),
                    DB::raw('COALESCE(e.codigo, "") as estado_codigo'),
                ]);

            $fmtMoney = function ($v) {
                return is_null($v) ? 0 : (float) $v;
            };

            $items = $rows->map(function ($r) use ($fmtMoney) {
                
                $dateDigits = preg_replace('/[^0-9]/', '', (string)($r->fecha_cotizacion ?? ''));
                $yymmdd = $dateDigits ? substr($dateDigits, 0, 8) : now()->format('Ymd');
                $codigo = 'COT-' . $yymmdd . '-' . (int) ($r->id ?? 0);
                return [
                    'id' => (int) ($r->id ?? 0),
                    'codigo' => $codigo,
                    'fecha' => $r->fecha_cotizacion ? substr((string)$r->fecha_cotizacion, 0, 10) : null,
                    'valido_hasta' => $r->valido_hasta ? substr((string)$r->valido_hasta, 0, 10) : null,
                    'subtotal' => $fmtMoney($r->subtotal ?? null),
                    'total' => $fmtMoney($r->total ?? null),
                    'total_impuesto' => $fmtMoney($r->total_impuesto ?? null),
                    'otros_cargos' => $fmtMoney($r->otros_cargos ?? null),
                    'impuesto_otros' => $fmtMoney($r->impuesto_otros ?? null),
                    'estado_nombre' => (string) ($r->estado_nombre ?? ''),
                    'estado_codigo' => (string) ($r->estado_codigo ?? ''),
                ];
            });

            return response()->json(['data' => $items]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['data' => []]);
        }
    }

    
    public function show($id): JsonResponse
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

            $row = DB::table('tbl_cotizacion as c')
                ->leftJoin('tbl_estado_cotizacion as e', 'e.id_estado_cotizacion_pk', '=', 'c.id_estado_cotizacion_fk')
                ->leftJoin('tbl_cliente as cl', 'cl.id_cliente_pk', '=', 'c.id_cliente_fk')
                ->leftJoin('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'cl.id_cliente_pk')
                ->leftJoin('tbl_cliente_persona as cp', 'cp.id_cliente_fk', '=', 'cl.id_cliente_pk')
                ->leftJoin('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                ->where('c.id_cotizacion_pk', (int)$id)
                ->whereIn('c.id_cliente_fk', $clienteIds)
                ->select([
                    'c.id_cotizacion_pk',
                    'c.fecha_cotizacion',
                    'c.valido_hasta',
                    'c.subtotal',
                    'c.imponible',
                    'c.total_impuesto',
                    'c.otros_cargos',
                    'c.impuesto_otros',
                    'c.anticipo_requerido',
                    'c.total',
                    'c.id_cliente_fk',
                    DB::raw('COALESCE(e.nombre, "") as estado_nombre'),
                    DB::raw('COALESCE(e.codigo, "") as estado_codigo'),
                    DB::raw('COALESCE(ce.nombre_comercial, ce.razon_social, CONCAT_WS(" ", p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido)) as cliente_nombre'),
                ])
                ->first();

            if (!$row) return response()->json(['error' => 'Not found'], 404);

            return response()->json([
                'id_cotizacion_pk' => (int)$row->id_cotizacion_pk,
                'fecha_cotizacion' => $row->fecha_cotizacion,
                'valido_hasta' => $row->valido_hasta,
                'subtotal' => (float)($row->subtotal ?? 0),
                'imponible' => (float)($row->imponible ?? 0),
                'total_impuesto' => (float)($row->total_impuesto ?? 0),
                'otros_cargos' => (float)($row->otros_cargos ?? 0),
                'impuesto_otros' => (float)($row->impuesto_otros ?? 0),
                'anticipo_requerido' => (float)($row->anticipo_requerido ?? 0),
                'total' => (float)($row->total ?? 0),
                'id_cliente_fk' => (int)($row->id_cliente_fk ?? 0),
                'estado_nombre' => (string)($row->estado_nombre ?? ''),
                'estado_codigo' => (string)($row->estado_codigo ?? ''),
                'cliente_nombre' => (string)($row->cliente_nombre ?? ''),
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    
    public function items($id): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) return response()->json(['data' => []], 401);

            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona) return response()->json(['data' => []], 404);

            $clienteIds = DB::table('tbl_cliente_persona')
                ->where('id_persona_fk', $persona->id_persona_pk)
                ->pluck('id_cliente_fk')
                ->all();
            if (empty($clienteIds)) return response()->json(['data' => []], 404);

            
            $owns = DB::table('tbl_cotizacion')
                ->where('id_cotizacion_pk', (int)$id)
                ->whereIn('id_cliente_fk', $clienteIds)
                ->exists();
            if (!$owns) return response()->json(['data' => []], 404);

            $items = DB::table('tbl_item_cotizacion')
                ->where('id_cotizacion_fk', (int)$id)
                ->orderBy('id_item_cotizacion_pk')
                ->get([
                    'id_item_cotizacion_pk',
                    'descripcion',
                    'precio_unitario',
                    'cantidad',
                    'impuesto',
                    'total'
                ]);

            return response()->json(['data' => $items]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['data' => []], 500);
        }
    }

    
    public function updateEstado(Request $request, $id): JsonResponse
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

            $cot = \App\Models\Cotizacion::query()->where('id_cotizacion_pk', (int)$id)
                ->whereIn('id_cliente_fk', $clienteIds)
                ->first();
            if (!$cot) return response()->json(['error' => 'Not found'], 404);

            
            $estadoInput = trim((string)($request->input('estado') ?? ''));
            $estadoIdInput = $request->input('estado_id');

            $allowed = ['aprobada', 'aprobado', 'rechazada', 'rechazado'];
            $estado = null;
            if ($estadoIdInput) {
                $e = \App\Models\EstadoCotizacion::find((int)$estadoIdInput);
                if ($e) {
                    $nom = strtolower($e->nombre ?? $e->codigo ?? '');
                    if (in_array($nom, $allowed)) {
                        $estado = $e;
                    }
                }
            }
            if (!$estado && $estadoInput !== '') {
                
                $estado = \App\Models\EstadoCotizacion::query()
                    ->whereRaw('LOWER(codigo) = ?', [strtolower($estadoInput)])
                    ->orWhereRaw('LOWER(nombre) = ?', [strtolower($estadoInput)])
                    ->first();
                if ($estado) {
                    $nom = strtolower($estado->nombre ?? $estado->codigo ?? '');
                    if (!in_array($nom, $allowed)) {
                        $estado = null;
                    }
                }
            }
            if (!$estado) {
                return response()->json(['error' => 'Estado no permitido'], 422);
            }

            
            if ((int)$cot->id_estado_cotizacion_fk === (int)$estado->id_estado_cotizacion_pk) {
                return response()->json(['ok' => true]);
            }

            
            $old = $cot->id_estado_cotizacion_fk;
            $cot->id_estado_cotizacion_fk = (int)$estado->id_estado_cotizacion_pk;
            $cot->save();

            
            try {
                $tecUserIds = [];
                
                $osId = $cot->id_orden_servicio_fk ?: DB::table('tbl_orden_servicio')
                    ->where('id_cotizacion_fk', $cot->id_cotizacion_pk)
                    ->value('id_orden_servicio_pk');
                if ($osId) {
                    $tecPersonaId = DB::table('tbl_orden_servicio')
                        ->where('id_orden_servicio_pk', $osId)
                        ->value('id_tecnico_fk');
                    if ($tecPersonaId) {
                        $id = DB::table('tbl_persona')->where('id_persona_pk', $tecPersonaId)->value('id_usuario_fk');
                        if ($id) $tecUserIds[] = (int) $id;
                    }
                }
                
                if (empty($tecUserIds)) {
                    $rolIds = DB::table('tbl_ms_rol')
                        ->where(function ($q) {
                            $q->whereRaw('LOWER(rol) IN (?, ?, ?, ?)', ['tecnico', 'técnico', 'tecnicos', 'técnicos'])
                                ->orWhereRaw('LOWER(rol) LIKE ?', ['%tecn%']);
                        })
                        ->pluck('id_rol_pk')
                        ->all();
                    if (!empty($rolIds)) {
                        $fromPivot = DB::table('tbl_usuario_rol')
                            ->whereIn('id_rol_fk', $rolIds)
                            ->pluck('id_usuario_fk')
                            ->all();
                        $fromUsers = DB::table('tbl_ms_usuario')
                            ->whereIn('id_rol_fk', $rolIds)
                            ->pluck('id_usuario_pk')
                            ->all();
                        $tecUserIds = collect(array_merge($fromPivot, $fromUsers))
                            ->map(fn($v) => (int) $v)
                            ->unique()
                            ->take(10)
                            ->values()
                            ->all();
                    }
                }
                if (!empty($tecUserIds)) {
                    
                    $clienteNombre = DB::table('tbl_cotizacion as c')
                        ->leftJoin('tbl_cliente as cl', 'cl.id_cliente_pk', '=', 'c.id_cliente_fk')
                        ->leftJoin('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'cl.id_cliente_pk')
                        ->leftJoin('tbl_cliente_persona as cp', 'cp.id_cliente_fk', '=', 'cl.id_cliente_pk')
                        ->leftJoin('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                        ->where('c.id_cotizacion_pk', $cot->getKey())
                        ->value(DB::raw('COALESCE(ce.nombre_comercial, ce.razon_social, CONCAT_WS(" ", p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido))'));
                    $clienteNombre = trim((string)($clienteNombre ?? '')) ?: 'Cliente';


                    $fechaFmt = now()->format('Ymd');
                    try {
                        $fechaFmt = \Carbon\Carbon::parse($cot->fecha_cotizacion ?? now())->format('Ymd');
                    } catch (\Throwable $e) {
                    }
                    $cotFmt = 'COT-' . $fechaFmt . '-' . $cot->getKey();
                    $payload = [
                        'title' => $clienteNombre . ' cambió estado de cotización',
                        'body' => "La cotización {$cotFmt} ahora está en estado: " . ($estado->nombre ?? $estado->codigo ?? ''),
                        'url' => '/admin/cotizaciones',
                        'icon' => 'fa-exchange-alt',
                        'severity' => 'info',
                        'module' => 'cotizaciones',
                        'meta' => [
                            'id_cotizacion_pk' => $cot->getKey(),
                            'nuevo_estado' => $estado->codigo ?? $estado->nombre ?? ''
                        ],
                    ];
                    foreach ($tecUserIds as $uid) {
                        $u = \App\Models\Usuario::find($uid);
                        if ($u) {
                            try {
                                $u->notify(new \App\Notifications\SystemNotification($payload));
                            } catch (\Throwable $e) {
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}