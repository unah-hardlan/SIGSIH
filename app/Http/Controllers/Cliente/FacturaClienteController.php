<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Factura;
use App\Models\DetalleFactura;

class FacturaClienteController extends Controller
{

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


            $subDescuentos = DB::table('tbl_detalle_factura as df')
                ->select([
                    'df.id_factura_fk',
                    DB::raw('SUM(COALESCE(df.descuento,0)) as total_descuento')
                ])
                ->groupBy('df.id_factura_fk');

            $subTotales = DB::table('tbl_detalle_factura as df')
                ->select([
                    'df.id_factura_fk',

                    DB::raw('SUM(COALESCE(df.precio_unitario,0) * (
                        CASE 
                            WHEN COALESCE(df.cantidad,0) <> 0 THEN COALESCE(df.cantidad,0)
                            WHEN COALESCE(df.horas,0) <> 0 THEN COALESCE(df.horas,0)
                            ELSE 1
                        END
                    ) - COALESCE(df.descuento,0)) as base_subtotal'),
                    DB::raw('SUM(COALESCE(df.impuesto,0)) as sum_impuesto')
                ])
                ->groupBy('df.id_factura_fk');

            $rows = DB::table('tbl_factura as f')
                ->leftJoin('tbl_estado_factura as e', 'e.id_estado_factura_pk', '=', 'f.id_estado_factura_fk')
                ->leftJoinSub($subDescuentos, 'd', function ($join) {
                    $join->on('d.id_factura_fk', '=', 'f.id_factura_pk');
                })
                ->leftJoinSub($subTotales, 't', function ($join) {
                    $join->on('t.id_factura_fk', '=', 'f.id_factura_pk');
                })
                ->whereIn('f.id_cliente_fk', $clienteIds)
                ->orderByDesc('f.id_factura_pk')
                ->get([
                    'f.id_factura_pk as id',
                    'f.numero',
                    'f.fecha',
                    'f.oc',
                    'f.subtotal',
                    'f.impuesto',
                    'f.total',
                    DB::raw('COALESCE(t.base_subtotal,0) as computed_subtotal'),
                    DB::raw('COALESCE(t.sum_impuesto,0) as computed_impuesto'),
                    DB::raw('COALESCE(d.total_descuento,0) as descuento'),
                    DB::raw('COALESCE(e.nombre, "") as estado')
                ]);

            $fmtMoney = function ($v) {
                return is_null($v) ? 0 : (float) $v;
            };

            $items = $rows->map(function ($r) use ($fmtMoney) {
                $fecha = $r->fecha ? substr((string)$r->fecha, 0, 10) : null;

                $subtotal = (float) ($r->subtotal ?? 0);
                $impuesto = (float) ($r->impuesto ?? 0);
                $computedSubtotal = (float) ($r->computed_subtotal ?? 0);
                $computedImpuesto = (float) ($r->computed_impuesto ?? 0);

                $subtotalFinal = $subtotal > 0 ? $subtotal : $computedSubtotal;
                $impuestoFinal = $impuesto > 0 ? $impuesto : ($computedImpuesto > 0 ? $computedImpuesto : round($subtotalFinal * 0.15, 2));
                $totalDb = (float) ($r->total ?? 0);
                $totalFinal = $totalDb > 0 ? $totalDb : ($subtotalFinal + $impuestoFinal);
                return [
                    'id' => (int) ($r->id ?? 0),
                    'numero' => (string) ($r->numero ?? ''),
                    'fecha' => $fecha,
                    'oc' => (string) ($r->oc ?? ''),
                    'subtotal' => $fmtMoney($subtotalFinal),
                    'impuesto' => $fmtMoney($impuestoFinal),
                    'descuento' => $fmtMoney($r->descuento ?? null),
                    'total' => $fmtMoney($totalFinal),
                    'estado' => (string) ($r->estado ?? ''),
                ];
            });

            return response()->json(['data' => $items]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['data' => []]);
        }
    }


    public function viewer(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        if (!$persona) abort(404);

        $clienteIds = DB::table('tbl_cliente_persona')
            ->where('id_persona_fk', $persona->id_persona_pk)
            ->pluck('id_cliente_fk')
            ->all();
        if (empty($clienteIds)) abort(404);

        $factura = Factura::with([
            'estadoFactura',
            'cai',
            'cliente.persona',
            'cliente.empresa',
            'cliente.agencias.direccion.ciudad.departamento',
            'cliente.contactos',
            'cotizacion'
        ])
            ->whereIn('id_cliente_fk', $clienteIds)
            ->findOrFail((int)$id);

        $detalles = DetalleFactura::where('id_factura_fk', (int)$id)->get();

        // === Datos Cliente (similar a FacturaController@procesarDatosCliente) ===
        $cliente_nombre = 'Sin cliente';
        $cliente_direccion = '';
        $cliente_telefono = '';
        $cliente_correo = '';
        $cliente_contacto = '';
        $cliente = $factura->cliente;
        if ($cliente) {
            if (($cliente->tipo_cliente ?? null) === 'empresa' && $cliente->empresa) {
                $cliente_nombre = $cliente->empresa->nombre_comercial ?? $cliente->empresa->razon_social ?? $cliente_nombre;
                $cliente_direccion = $cliente->empresa->direccion ?? '';
                $cliente_telefono = $cliente->empresa->telefono ?? '';
                $cliente_correo = $cliente->empresa->correo_electronico ?? '';
                $cliente_contacto = $cliente->empresa->contacto ?? '';
            } else {
                $persona = $cliente->persona;
                if ($persona) {
                    if ($persona instanceof \Illuminate\Database\Eloquent\Collection) {
                        $persona = $persona->first();
                    }
                    $cliente_nombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) ?: $cliente_nombre;
                    $cliente_direccion = $persona->direccion ?? '';
                    $cliente_telefono = $persona->telefono ?? '';
                    $cliente_correo = $persona->correo_electronico ?? '';
                    $cliente_contacto = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) ?: ($persona->primer_nombre ?? '');
                }
            }
        }

        // === Totales (similar a FacturaController@calcularTotales) ===
        $computedBaseSubtotal = 0.0;
        $computedTotalImpuesto = 0.0;
        foreach ($detalles as $d) {
            $qty = (float) ($d->cantidad ?? $d->horas ?? 1);
            $precio = (float) ($d->precio_unitario ?? ($d->precio ?? 0));
            $desc = (float) ($d->descuento ?? 0);
            $impLinea = (float) ($d->impuesto ?? 0);
            $baseLine = $precio * $qty - $desc;
            $computedBaseSubtotal += $baseLine;
            $computedTotalImpuesto += $impLinea;
        }
        $facturaSubtotal = (isset($factura->subtotal) && $factura->subtotal !== null && (float)$factura->subtotal > 0)
            ? (float)$factura->subtotal : $computedBaseSubtotal;
        if (isset($factura->impuesto) && $factura->impuesto !== null && (float)$factura->impuesto > 0) {
            $impuesto = (float)$factura->impuesto;
        } elseif ($computedTotalImpuesto > 0) {
            $impuesto = $computedTotalImpuesto;
        } else {
            $impuesto = round($facturaSubtotal * 0.15, 2);
        }
        $facturaTotal = round($facturaSubtotal + $impuesto, 2);

        // === Datos de contacto y dirección (similar a FacturaController@procesarDatosContacto) ===
        $ag = optional($cliente?->agencias->first());
        $agDireccion = optional($ag?->direccion);
        $telefono_fallback = $cliente_telefono;
        $correo_fallback = $cliente_correo;
        if (empty($telefono_fallback) || empty($correo_fallback)) {
            $contactos = $cliente?->contactos ?? collect();
            foreach ($contactos as $c) {
                $tipo = strtolower(trim($c->tipo_contacto ?? ''));
                $valor = $c->valor_contacto ?? '';
                if (empty($telefono_fallback) && in_array($tipo, ['telefono', 'tel', 'phone', 'movil', 'mobile'])) {
                    $telefono_fallback = $valor;
                }
                if (empty($correo_fallback) && in_array($tipo, ['email', 'correo', 'mail'])) {
                    $correo_fallback = $valor;
                }
            }
        }
        $addr_line1 = '';
        $addr_colonia = '';
        $addr_cp = '';
        $addr_city = '';
        $addr_depto = '';
        if (!empty($cliente_direccion)) {
            $addr_line1 = $cliente_direccion;
        } elseif ($agDireccion) {
            $addr_line1 = trim(($agDireccion->calle ?? '') . ' ' . ($agDireccion->numero ?? '')) ?: ($agDireccion->direccion_completa ?? '');
            $addr_colonia = $agDireccion->colonia ?? '';
            $addr_cp = $agDireccion->codigo_postal ?? '';
            $addr_city = optional($agDireccion->ciudad)->nombre_ciudad ?? '';
            $addr_depto = optional(optional($agDireccion->ciudad)->departamento)->nombre_departamento ?? '';
        }
        $contactoNombre = $cliente_contacto;
        if (empty($contactoNombre)) {
            $contactos = $cliente?->contactos ?? collect();
            foreach ($contactos as $c) {
                $valor = trim((string)($c->valor_contacto ?? ''));
                if ($valor !== '' && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/', $valor)) {
                    $contactoNombre = $valor;
                    break;
                }
            }
        }

        // === Fecha límite (similar a FacturaController@calcularFechaLimite) ===
        $fechaLimite = null;
        if (!empty($factura->fecha_limite_emision)) {
            $fechaLimite = $factura->fecha_limite_emision;
        } elseif (!empty($factura->fecha_limite)) {
            $fechaLimite = $factura->fecha_limite;
        } elseif (!empty(optional($factura->cai)->fecha_limite_emision)) {
            $fechaLimite = optional($factura->cai)->fecha_limite_emision;
        } elseif (!empty(optional($factura->cai)->fecha_limite)) {
            $fechaLimite = optional($factura->cai)->fecha_limite;
        }

        // Si existe vista cliente específica usarla, si no, la admin
        $viewName = view()->exists('cliente.formato-factura') ? 'cliente.formato-factura' : 'admin.formato-factura';

        return view($viewName, compact(
            'factura',
            'detalles',
            'fechaLimite',
            'cliente_nombre',
            'addr_line1',
            'addr_colonia',
            'addr_cp',
            'addr_city',
            'addr_depto',
            'telefono_fallback',
            'correo_fallback',
            'contactoNombre',
            'facturaSubtotal',
            'impuesto',
            'facturaTotal'
        ));
    }


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


            $factura = DB::table('tbl_factura')
                ->where('id_factura_pk', (int)$id)
                ->whereIn('id_cliente_fk', $clienteIds)
                ->first(['id_factura_pk as id', 'numero', 'fecha', 'oc']);
            if (!$factura) return response()->json(['error' => 'Not found'], 404);

            $detalles = DB::table('tbl_detalle_factura')
                ->where('id_factura_fk', (int)$id)
                ->orderBy('id_detalle_pk')
                ->get([
                    'id_detalle_pk as id',
                    'descripcion',
                    'precio_unitario',
                    'cantidad',
                    'impuesto',
                    'total_linea'
                ])
                ->map(function ($d) {
                    return [
                        'id' => (int) ($d->id ?? 0),
                        'descripcion' => (string) ($d->descripcion ?? ''),
                        'precio_unitario' => (float) ($d->precio_unitario ?? 0),
                        'cantidad' => (float) ($d->cantidad ?? 0),
                        'impuesto' => (float) ($d->impuesto ?? 0),
                        'total_linea' => (float) ($d->total_linea ?? 0),
                    ];
                });

            return response()->json([
                'id' => (int) ($factura->id ?? 0),
                'numero' => (string) ($factura->numero ?? ''),
                'fecha' => $factura->fecha ? substr((string)$factura->fecha, 0, 10) : null,
                'oc' => (string) ($factura->oc ?? ''),
                'detalles' => $detalles,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
