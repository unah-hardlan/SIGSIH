<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCotizacionRequest;
use App\Http\Requests\UpdateCotizacionRequest;
use App\Http\Resources\CotizacionResource;
use App\Models\EstadoCotizacion;
use App\Models\Usuario;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CotizacionController extends Controller
{
    /**
     * Viewer HTML para detalle de cotización (Admin).
     * Evita usar un controlador dedicado sólo para retornar una vista.
     */
    public function viewer()
    {
        return view('admin.detalle-cotizacion');
    }

    public function index(Request $request)
    {
        $query = Cotizacion::query()->with(['cliente.empresa', 'cliente.personas', 'estado']);

        if ($cliente = $request->input('id_cliente_fk')) {
            $query->where('id_cliente_fk', $cliente);
        }
        if ($q = $request->input('q')) {

            $query->where(function ($sub) use ($q) {
                $sub->where('subtotal', 'like', "%$q%")
                    ->orWhere('total', 'like', "%$q%")
                    ->orWhere('impuesto', 'like', "%$q%");
            });
        }
        if ($desde = $request->input('desde')) {
            $query->whereDate('fecha_cotizacion', '>=', $desde);
        }
        if ($hasta = $request->input('hasta')) {
            $query->whereDate('fecha_cotizacion', '<=', $hasta);
        }

        $sortable = [
            'fecha' => 'fecha_cotizacion',
            'valido' => 'valido_hasta',
            'total' => 'total',
            'subtotal' => 'subtotal',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortable[$sort] ?? 'id_cotizacion_pk', $direction);

        if ($request->boolean('all')) {
            return CotizacionResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page', 15);
        $items = $query->paginate($perPage);
        return CotizacionResource::collection($items)->additional([
            'meta' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ]
        ]);
    }

    public function store(StoreCotizacionRequest $request)
    {
        $cotizacion = Cotizacion::create($request->validated());
        $cotizacion->load(['cliente.empresa', 'cliente.personas', 'estado']);


        try {
            $clienteId = $cotizacion->id_cliente_fk;
            if ($clienteId) {
                $userIds = DB::table('tbl_cliente_persona as cp')
                    ->join('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                    ->join('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'p.id_usuario_fk')
                    ->where('cp.id_cliente_fk', $clienteId)
                    ->pluck('u.id_usuario_pk')
                    ->all();

                if (!empty($userIds)) {
                    $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();

                    try {
                        $fechaFmt = \Carbon\Carbon::parse($cotizacion->fecha_cotizacion ?? now())->format('Ymd');
                    } catch (\Throwable $e) {
                        $fechaFmt = now()->format('Ymd');
                    }
                    $cotFmt = 'COT-' . $fechaFmt . '-' . $cotizacion->getKey();
                    $clienteNombre = $cotizacion->cliente->nombre
                        ?? ($cotizacion->cliente->empresa->nombre_comercial
                            ?? ($cotizacion->cliente->empresa->razon_social ?? ''));
                    $payload = [
                        'title' => 'Nueva cotización disponible',
                        'body' => ($clienteNombre ? ($clienteNombre . ': ') : '') . "Hemos generado la cotización {$cotFmt} para tu revisión.",
                        'url' => '/cliente/cotizaciones',
                        'icon' => 'fa-file-invoice-dollar',
                        'severity' => 'info',
                        'module' => 'cotizaciones',
                        'meta' => [
                            'id_cotizacion_pk' => $cotizacion->getKey(),
                            'id_cliente_fk' => $clienteId,
                        ],
                    ];

                    foreach ($users as $u) {
                        try {
                            $u->notify(new SystemNotification($payload));
                        } catch (\Throwable $t) {
                            Log::warning('Failed to notify client user about new cotizacion: ' . $t->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error sending cotizacion creation notifications: ' . $e->getMessage());
        }

        return (new CotizacionResource($cotizacion))->response()->setStatusCode(201);
    }

    public function show($id)
    {

        $cotizacion = Cotizacion::with(['cliente.empresa', 'cliente.personas', 'estado'])->find($id);
        if (!$cotizacion) return response()->json(['error' => 'Cotizacion no encontrada'], 404);
        return (new CotizacionResource($cotizacion))->response();
    }

    public function update(UpdateCotizacionRequest $request, $id)
    {
        $cotizacion = Cotizacion::find($id);
        if (!$cotizacion) return response()->json(['error' => 'Cotizacion no encontrada'], 404);
        $validated = $request->validated();
        $oldEstado = $cotizacion->id_estado_cotizacion_fk;
        $cotizacion->update($validated);
        $cotizacion->load(['cliente.empresa', 'cliente.personas', 'estado']);


        try {
            if (array_key_exists('id_estado_cotizacion_fk', $validated)) {
                $newId = (int) $validated['id_estado_cotizacion_fk'];
                if ((int) $oldEstado !== $newId) {
                    $estado = EstadoCotizacion::find($newId);
                    $nombre = strtolower($estado->nombre ?? $estado->codigo ?? '');
                    if (str_contains($nombre, 'enviad')) {
                        $clienteId = $cotizacion->id_cliente_fk;
                        if ($clienteId) {
                            $userIds = DB::table('tbl_cliente_persona as cp')
                                ->join('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                                ->join('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'p.id_usuario_fk')
                                ->where('cp.id_cliente_fk', $clienteId)
                                ->pluck('u.id_usuario_pk')
                                ->all();
                            if (!empty($userIds)) {
                                $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();
                                try {
                                    $fechaFmt = \Carbon\Carbon::parse($cotizacion->fecha_cotizacion ?? now())->format('Ymd');
                                } catch (\Throwable $e) {
                                    $fechaFmt = now()->format('Ymd');
                                }
                                $cotFmt = 'COT-' . $fechaFmt . '-' . $cotizacion->getKey();
                                $payload = [
                                    'title' => 'Tu cotización fue enviada',
                                    'body' => "La cotización {$cotFmt} está disponible para revisión.",
                                    'url' => '/cliente/cotizaciones',
                                    'icon' => 'fa-file-invoice-dollar',
                                    'severity' => 'info',
                                    'module' => 'cotizaciones',
                                    'meta' => ['id_cotizacion_pk' => $cotizacion->getKey()],
                                ];
                                foreach ($users as $u) {
                                    try {
                                        $u->notify(new SystemNotification($payload));
                                    } catch (\Throwable $t) {
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error notifying client on cotizacion status change: ' . $e->getMessage());
        }


        try {
            if (array_key_exists('id_estado_cotizacion_fk', $validated)) {
                $newId = (int) $validated['id_estado_cotizacion_fk'];
                if ((int)$oldEstado !== $newId) {
                    $estado = EstadoCotizacion::find($newId);

                    $tecUserIds = [];
                    $osId = $cotizacion->id_orden_servicio_fk ?: DB::table('tbl_orden_servicio')
                        ->where('id_cotizacion_fk', $cotizacion->getKey())
                        ->value('id_orden_servicio_pk');
                    if ($osId) {
                        $tecPersonaId = DB::table('tbl_orden_servicio')
                            ->where('id_orden_servicio_pk', $osId)
                            ->value('id_tecnico_fk');
                        if ($tecPersonaId) {
                            $id = DB::table('tbl_persona')
                                ->where('id_persona_pk', $tecPersonaId)
                                ->value('id_usuario_fk');
                            if ($id) $tecUserIds[] = (int)$id;
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
                                ->map(fn($v) => (int)$v)
                                ->unique()
                                ->take(10)
                                ->values()
                                ->all();
                        }
                    }
                    if (!empty($tecUserIds)) {
                        try {
                            $fechaFmt = \Carbon\Carbon::parse($cotizacion->fecha_cotizacion ?? now())->format('Ymd');
                        } catch (\Throwable $e) {
                            $fechaFmt = now()->format('Ymd');
                        }
                        $cotFmt = 'COT-' . $fechaFmt . '-' . $cotizacion->getKey();
                        $payloadTec = [
                            'title' => 'Cotización cambió de estado',
                            'body' => "La cotización {$cotFmt} ahora está en estado: " . ($estado->nombre ?? $estado->codigo ?? ''),
                            'url' => '/admin/cotizaciones',
                            'icon' => 'fa-exchange-alt',
                            'severity' => 'info',
                            'module' => 'cotizaciones',
                            'meta' => [
                                'id_cotizacion_pk' => $cotizacion->getKey(),
                                'nuevo_estado' => $estado->codigo ?? $estado->nombre ?? ''
                            ],
                        ];
                        foreach ($tecUserIds as $uid) {
                            $u = Usuario::find($uid);
                            if ($u) {
                                try {
                                    $u->notify(new SystemNotification($payloadTec));
                                } catch (\Throwable $t) {
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        return (new CotizacionResource($cotizacion))->response();
    }

    public function destroy($id)
    {
        $cotizacion = Cotizacion::find($id);
        if (!$cotizacion) return response()->json(['error' => 'Cotizacion no encontrada'], 404);
        $cotizacion->delete();
        return response()->json(['message' => 'Cotizacion eliminada']);
    }
}
