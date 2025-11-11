<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Http\Resources\OrdenServicioResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Direccion;
use App\Models\OficinaEmpresa;
use App\Models\DetalleOrdenProducto;
use App\Models\Usuario;
use App\Models\Rol;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrdenServicioController extends Controller
{

    public function index(Request $request)
    {
        $query = OrdenServicio::with([

            'solicitudServicio.cliente.empresa',
            'solicitudServicio.cliente.personas',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ]);


        if ($request->has('id_solicitud_servicio_fk')) {
            $query->where('id_solicitud_servicio_fk', $request->id_solicitud_servicio_fk);
        }

        if ($request->has('id_tecnico_fk')) {
            $query->where('id_tecnico_fk', $request->id_tecnico_fk);
        }

        if ($request->has('fecha_recepcion')) {
            $query->whereDate('fecha_recepcion', $request->fecha_recepcion);
        }

        $clienteId = $request->input('cliente_id', $request->input('id_cliente_fk'));
        if (!empty($clienteId)) {
            $query->whereHas('solicitudServicio', function ($q) use ($clienteId) {
                $q->where('id_cliente_fk', $clienteId);
            });
        }


        $perPage = (int) $request->input('per_page', 15);
        $all = $request->boolean('all') || $perPage === -1;
        if ($all) {
            $items = $query->get();
            return OrdenServicioResource::collection($items);
        }
        $ordenesServicio = $query->paginate(max(1, $perPage));
        return OrdenServicioResource::collection($ordenesServicio);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_solicitud_servicio_fk' => 'required|integer|exists:tbl_solicitud,id_solicitud_pk',
            'id_tecnico_fk' => 'required|integer|exists:tbl_persona,id_persona_pk',
            'numero_orden_servicio' => 'nullable|string|max:50',
            'fecha_creada' => 'nullable|date',
            'fecha_asignada' => 'nullable|date',
            'fecha_recepcion' => 'required|date',
            'fecha_inicio' => 'nullable|date|before_or_equal:fecha_finalizacion',
            'fecha_finalizacion' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string|max:500',
            'diagnostico_tecnico' => 'nullable|string|max:500',
            'diagnostico_cliente' => 'nullable|string|max:500',
            'calificacion_servicio' => 'nullable|string|in:excelente,bueno,regular,deficiente',
            'id_estado_orden_servicio_fk' => 'nullable|integer|exists:tbl_estado_orden_servicio,id_estado_orden_servicio_pk',
            'id_cotizacion_fk' => 'nullable|integer|exists:tbl_cotizacion,id_cotizacion_pk',
            'repuestos' => 'nullable|array',
            'repuestos.*.id_producto_fk' => 'required_with:repuestos|integer|exists:tbl_producto,id_producto_pk',
            'repuestos.*.cantidad' => 'required_with:repuestos|integer|min:1',
        ]);

        $ordenServicio = OrdenServicio::create($validatedData);

        try {
            $inputRepuestos = $request->input('repuestos', []);
            if (is_array($inputRepuestos) && count($inputRepuestos)) {
                foreach ($inputRepuestos as $r) {

                    $prodId = $r['id_producto_fk'] ?? ($r['id_producto'] ?? null);
                    $cantidad = $r['cantidad'] ?? ($r['cant'] ?? 1);
                    if ($prodId) {
                        DetalleOrdenProducto::create([
                            'id_orden_servicio_fk' => $ordenServicio->id_orden_servicio_pk,
                            'id_producto_fk' => $prodId,
                            'cantidad' => $cantidad,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
        }
        $ordenServicio->load([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.cliente.personas',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ]);


        try {
            $detalles = DetalleOrdenProducto::with('producto')
                ->where('id_orden_servicio_fk', $ordenServicio->id_orden_servicio_pk)
                ->get();

            if ($detalles && $detalles->count()) {
                $repuestos = $detalles->map(function ($d) {
                    return [
                        'id_producto' => $d->id_producto_fk,
                        'nombre' => $d->producto->nombre_producto ?? ($d->producto->nombre ?? null),
                        'cantidad' => $d->cantidad,
                    ];
                })->values()->all();

                $ordenServicio->forceFill(['repuestos' => $repuestos])->saveQuietly();
            }
        } catch (\Throwable $e) {
        }


        try {
            $clienteId = $ordenServicio->solicitudServicio?->id_cliente_fk;
            if ($clienteId) {
                $userIds = DB::table('tbl_cliente_persona as cp')
                    ->join('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                    ->join('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'p.id_usuario_fk')
                    ->where('cp.id_cliente_fk', $clienteId)
                    ->pluck('u.id_usuario_pk')
                    ->all();

                if (!empty($userIds)) {
                    $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();
                    $clienteNombre = $ordenServicio->solicitudServicio?->cliente->nombre
                        ?? ($ordenServicio->solicitudServicio?->cliente->empresa->nombre_comercial
                            ?? ($ordenServicio->solicitudServicio?->cliente->empresa->razon_social ?? ''));

                    $osNumero = $ordenServicio->numero_orden_servicio
                        ?: sprintf('OS-%s-%06d', now()->format('Ym'), $ordenServicio->getKey());

                    $payload = [
                        'title' => 'Nueva Orden de Servicio creada',
                        'body' => ($clienteNombre ? ($clienteNombre . ': ') : '') . "Hemos creado la Orden de Servicio {$osNumero}. Te avisaremos del avance.",
                        'url' => '/cliente/ordenes',
                        'icon' => 'fa-tools',
                        'severity' => 'info',
                        'module' => 'orden_servicio',
                        'meta' => [
                            'id_orden_servicio_pk' => $ordenServicio->getKey(),
                            'id_cliente_fk' => $clienteId,
                            'numero_orden_servicio' => $osNumero,
                        ],
                    ];

                    foreach ($users as $u) {
                        try {
                            $u->notify(new SystemNotification($payload));
                        } catch (\Throwable $t) {
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error enviando notificación de creación de Orden de Servicio: ' . $e->getMessage());
        }

        return new OrdenServicioResource($ordenServicio);
    }


    public function show($id)
    {
        $ordenServicio = OrdenServicio::with([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.cliente.personas',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ])->findOrFail($id);
        return new OrdenServicioResource($ordenServicio);
    }


    public function update(Request $request, $id)
    {
        $ordenServicio = OrdenServicio::findOrFail($id);

        $validatedData = $request->validate([
            'id_solicitud_servicio_fk' => 'sometimes|required|integer|exists:tbl_solicitud,id_solicitud_pk',
            'id_tecnico_fk' => 'sometimes|required|integer|exists:tbl_persona,id_persona_pk',
            'numero_orden_servicio' => 'nullable|string|max:50',
            'fecha_creada' => 'nullable|date',
            'fecha_asignada' => 'nullable|date',
            'fecha_recepcion' => 'sometimes|required|date',
            'fecha_inicio' => 'nullable|date|before_or_equal:fecha_finalizacion',
            'fecha_finalizacion' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string|max:500',
            'diagnostico_tecnico' => 'nullable|string|max:500',
            'diagnostico_cliente' => 'nullable|string|max:500',
            'calificacion_servicio' => 'nullable|string|in:excelente,bueno,regular,deficiente',
            'id_estado_orden_servicio_fk' => 'nullable|integer|exists:tbl_estado_orden_servicio,id_estado_orden_servicio_pk',
            'id_cotizacion_fk' => 'nullable|integer|exists:tbl_cotizacion,id_cotizacion_pk',
            'repuestos' => 'nullable|array',
            'repuestos.*.id_producto_fk' => 'required_with:repuestos|integer|exists:tbl_producto,id_producto_pk',
            'repuestos.*.cantidad' => 'required_with:repuestos|integer|min:1',
        ]);


        $oldEstadoId = $ordenServicio->id_estado_orden_servicio_fk;
        $ordenServicio->update($validatedData);
        $ordenServicio->load([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.cliente.personas',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ]);


        try {
            $inputRepuestos = $request->input('repuestos', null);
            if (is_array($inputRepuestos)) {

                DetalleOrdenProducto::where('id_orden_servicio_fk', $ordenServicio->id_orden_servicio_pk)->delete();
                foreach ($inputRepuestos as $r) {
                    $prodId = $r['id_producto_fk'] ?? ($r['id_producto'] ?? null);
                    $cantidad = $r['cantidad'] ?? ($r['cant'] ?? 1);
                    if ($prodId) {
                        DetalleOrdenProducto::create([
                            'id_orden_servicio_fk' => $ordenServicio->id_orden_servicio_pk,
                            'id_producto_fk' => $prodId,
                            'cantidad' => $cantidad,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
        }


        try {
            $detalles = DetalleOrdenProducto::with('producto')
                ->where('id_orden_servicio_fk', $ordenServicio->id_orden_servicio_pk)
                ->get();

            if ($detalles && $detalles->count()) {
                $repuestos = $detalles->map(function ($d) {
                    return [
                        'id_producto' => $d->id_producto_fk,
                        'nombre' => $d->producto->nombre_producto ?? ($d->producto->nombre ?? null),
                        'cantidad' => $d->cantidad,
                    ];
                })->values()->all();

                $ordenServicio->forceFill(['repuestos' => $repuestos])->saveQuietly();
            } else {

                $ordenServicio->forceFill(['repuestos' => null])->saveQuietly();
            }
        } catch (\Throwable $e) {
        }


        try {
            $newEstadoId = $ordenServicio->id_estado_orden_servicio_fk;
            if (isset($validatedData['id_estado_orden_servicio_fk']) && $newEstadoId != $oldEstadoId) {

                $oldName = null;
                try {
                    $oldName = \App\Models\EstadoOrdenServicio::find($oldEstadoId)?->nombre;
                } catch (\Throwable $_) {
                    $oldName = null;
                }
                $newName = $ordenServicio->estado?->nombre ?? null;


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
                    $clienteNombre = $ordenServicio->solicitudServicio?->cliente->nombre ?? ($ordenServicio->solicitudServicio?->cliente->nombre_comercial ?? 'Cliente');
                    $payload = [
                        'title' => 'Cambio de estado de Orden de Servicio',
                        'body' => "Orden de Servicio #{$ordenServicio->id_orden_servicio_pk} — Cliente: {$clienteNombre} cambió de " . ($oldName ?? 'N/A') . " a " . ($newName ?? 'N/A'),
                        'url' => '/admin/orden-servicio',
                        'icon' => 'fa-tools',
                        'severity' => 'info',
                        'module' => 'orden_servicio',
                        'meta' => ['id_orden_servicio_pk' => $ordenServicio->getKey(), 'old_estado' => $oldName, 'new_estado' => $newName]
                    ];

                    foreach ($users as $u) {
                        try {
                            $u->notify(new SystemNotification($payload));
                        } catch (\Throwable $e) {
                            Log::warning('Failed to notify user ' . $u->id_usuario_pk . ' about orden servicio state change: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error sending orden servicio state-change notifications: ' . $e->getMessage());
        }


        try {
            if (isset($validatedData['id_estado_orden_servicio_fk']) && $ordenServicio->estado) {
                $isClosed = false;
                $nombreEstado = strtolower($ordenServicio->estado->nombre ?? '');
                $codigoEstado = strtolower($ordenServicio->estado->codigo ?? '');
                if (str_contains($nombreEstado, 'cerrad') || $codigoEstado === 'cer') {
                    $isClosed = true;
                }

                if (!$isClosed) {
                    try {
                        $isClosed = (bool) \App\Models\EstadoOrdenServicio::where('id_estado_orden_servicio_pk', $ordenServicio->id_estado_orden_servicio_fk)
                            ->value('es_final');
                    } catch (\Throwable $_) {
                    }
                }

                if ($isClosed) {
                    $clienteId = $ordenServicio->solicitudServicio?->id_cliente_fk;
                    if ($clienteId) {
                        $userIds = DB::table('tbl_cliente_persona as cp')
                            ->join('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                            ->join('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'p.id_usuario_fk')
                            ->where('cp.id_cliente_fk', $clienteId)
                            ->pluck('u.id_usuario_pk')
                            ->all();
                        if (!empty($userIds)) {
                            $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();
                            $osNumero = $ordenServicio->numero_orden_servicio
                                ?: ('OS-' . now()->format('Ym') . '-' . str_pad((string)$ordenServicio->getKey(), 6, '0', STR_PAD_LEFT));
                            $payload = [
                                'title' => 'Tu orden de servicio fue cerrada',
                                'body' => "La Orden de Servicio {$osNumero} ha sido cerrada. Ahora puedes calificar el servicio.",
                                'url' => '/cliente/detalle-orden?orden=' . $ordenServicio->getKey(),
                                'icon' => 'fa-tools',
                                'severity' => 'info',
                                'module' => 'orden_servicio',
                                'meta' => [
                                    'id_orden_servicio_pk' => $ordenServicio->getKey(),
                                    'numero_orden_servicio' => $osNumero,
                                    'estado' => $ordenServicio->estado->nombre ?? 'Cerrada',
                                ],
                            ];
                            foreach ($users as $u) {
                                try {
                                    $u->notify(new SystemNotification($payload));
                                } catch (\Throwable $_) {
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error sending orden servicio CLOSED notification to client: ' . $e->getMessage());
        }


        return new OrdenServicioResource($ordenServicio);
    }


    public function destroy($id)
    {
        $ordenServicio = OrdenServicio::findOrFail($id);
        $ordenServicio->delete();

        return response()->json([
            'message' => 'Orden de servicio eliminada correctamente'
        ], Response::HTTP_OK);
    }


    public function reporte($id)
    {
        $orden = OrdenServicio::with([
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.cliente.contactos',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'detallesProducto'
        ])->findOrFail($id);

        $cliente = $orden->solicitudServicio->cliente ?? null;
        $empresa = $cliente?->empresa ?? null;


        $contactos = $cliente?->contactos ?? collect();

        $telefonos = $contactos->filter(function ($c) {
            $t = strtolower(trim($c->tipo_contacto ?? ''));
            return in_array($t, ['tel', 'telefono', 'phone', 'movil', 'celular', 'whatsapp', 'wa']);
        })->pluck('valor_contacto')->unique()->values()->all();

        $correo = $contactos->firstWhere('tipo_contacto', 'email')?->valor_contacto
            ?? $contactos->firstWhere('tipo_contacto', 'correo')?->valor_contacto
            ?? '';


        $direccion = '';
        if ($empresa && isset($empresa->id_direccion_fk) && $empresa->id_direccion_fk) {
            $dir = Direccion::with('ciudad.departamento.pais')->find($empresa->id_direccion_fk);
            if ($dir) {
                $direccion = $dir->direccion_completa ?? ($dir->calle . ' ' . $dir->numero . ', ' . $dir->colonia);
            }
        }
        if (!$direccion) {
            $dirContacto = $contactos->first(function ($c) {
                return str_contains(strtolower($c->tipo_contacto ?? ''), 'direc');
            });
            if ($dirContacto) $direccion = $dirContacto->valor_contacto;
        }


        $oficina = '';
        if ($empresa && isset($empresa->id_oficina_fk) && $empresa->id_oficina_fk) {
            $of = OficinaEmpresa::find($empresa->id_oficina_fk);
            if ($of) $oficina = $of->nombre_oficina;
        }


        $ciudad = '';
        if (!empty($direccion) && isset($dir) && $dir?->ciudad) {
            $ciudad = $dir->ciudad->nombre_ciudad ?? '';
        } else {
            $ciContacto = $contactos->first(function ($c) {
                return strtolower($c->tipo_contacto ?? '') === 'ciudad';
            });
            if ($ciContacto) $ciudad = $ciContacto->valor_contacto;
        }


        return view('admin.formato-reporte', compact('orden', 'telefonos', 'correo', 'direccion', 'oficina', 'ciudad'));
    }


    public function detalleOrden(Request $request, $id = null)
    {
        $ordenId = $id ?? $request->query('orden');

        if (!$ordenId) {

            return view('admin.detalle-orden', [
                'clienteNombre' => '—',
                'contactoNombre' => '—',
                'telefonosVal' => '—',
                'correoVal' => '—',
                'direccionVal' => '—',
                'oficinaVal' => '—',
                'ciudadVal' => '—',
                'firmaNombre' => '—',
                'firmaCi' => '—',
                'repuestosList' => [],
                'calificacionServicio' => null,
            ]);
        }

        try {
            $orden = OrdenServicio::with([
                'solicitudServicio.cliente.empresa',
                'solicitudServicio.cliente.contactos',
                'solicitudServicio.contacto',
                'solicitudServicio.cliente.personas.usuario',
                'solicitudServicio.cliente.agencias.direccion.ciudad',
                'detallesProducto.producto',
                'tecnico',
                'estado'
            ])->find($ordenId);

            if (!$orden) {
                return view('admin.detalle-orden', [
                    'clienteNombre' => '—',
                    'contactoNombre' => '—',
                    'telefonosVal' => '—',
                    'correoVal' => '—',
                    'direccionVal' => '—',
                    'oficinaVal' => '—',
                    'ciudadVal' => '—',
                    'firmaNombre' => '—',
                    'firmaCi' => '—',
                    'repuestosList' => [],
                    'calificacionServicio' => null,
                ]);
            }

            $datosCliente = $this->procesarDatosCliente($orden);

            $repuestosList = $this->procesarRepuestos($orden);

            $calificacionServicio = $orden->calificacion_servicio ?? null;

            return view('admin.detalle-orden', compact('orden') + $datosCliente + [
                'repuestosList' => $repuestosList,
                'calificacionServicio' => $calificacionServicio
            ]);
        } catch (\Throwable $e) {
            return view('admin.detalle-orden', [
                'clienteNombre' => '—',
                'contactoNombre' => '—',
                'telefonosVal' => '—',
                'correoVal' => '—',
                'direccionVal' => '—',
                'oficinaVal' => '—',
                'ciudadVal' => '—',
                'firmaNombre' => '—',
                'firmaCi' => '—',
                'repuestosList' => [],
                'calificacionServicio' => null,
            ]);
        }
    }


    private function procesarDatosCliente($orden)
    {
        $clienteNombre = data_get($orden, 'solicitudServicio.cliente.empresa.nombre_comercial')
            ?: data_get($orden, 'solicitudServicio.cliente.empresa.razon_social');

        if (!$clienteNombre) {
            $personasTmp = data_get($orden, 'solicitudServicio.cliente.personas', []);
            $personaCandidate = null;
            if (!empty($personasTmp)) {
                if (is_array($personasTmp)) {
                    $personaCandidate = $personasTmp[0] ?? null;
                } else {
                    $personaCandidate = count($personasTmp) ? $personasTmp->first() : null;
                }
            }

            if ($personaCandidate) {
                $pn = trim(implode(' ', array_filter([
                    data_get($personaCandidate, 'primer_nombre'),
                    data_get($personaCandidate, 'segundo_nombre'),
                    data_get($personaCandidate, 'primer_apellido'),
                    data_get($personaCandidate, 'segundo_apellido'),
                ])));
                $clienteNombre = $pn ?: data_get($personaCandidate, 'full_name') ?: data_get($personaCandidate, 'nombre_completo');
            }
        }

        $clienteNombre = $clienteNombre ?: '—';

        $contactoNombre = $this->procesarNombreContacto($orden);

        $telefonosVal = $this->procesarTelefonos($orden);

        $correoVal = $this->procesarCorreo($orden);

        $ubicacion = $this->procesarUbicacion($orden);

        $firmaNombre = $clienteNombre ?: '—';
        $firmaCi = data_get($orden, 'solicitudServicio.cliente.empresa.rtn')
            ?: data_get($orden, 'solicitudServicio.cliente.personas.0.dni')
            ?: data_get($orden, 'solicitudServicio.cliente.personas.0.identificacion')
            ?: data_get($orden, 'solicitudServicio.cliente.personas.0.ci')
            ?: '—';

        return [
            'clienteNombre' => $clienteNombre,
            'contactoNombre' => $contactoNombre,
            'telefonosVal' => $telefonosVal,
            'correoVal' => $correoVal,
            'direccionVal' => $ubicacion['direccion'],
            'oficinaVal' => $ubicacion['oficina'],
            'ciudadVal' => $ubicacion['ciudad'],
            'firmaNombre' => $firmaNombre,
            'firmaCi' => $firmaCi,
        ];
    }

    private function procesarNombreContacto($orden)
    {
        $contactoNombre = '—';
        $solContacto = data_get($orden, 'solicitudServicio.contacto', []);

        if (!empty($solContacto)) {
            $nombreFromContacto = data_get($solContacto, 'nombre') ?: data_get($solContacto, 'valor_contacto');
            if ($nombreFromContacto && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/', (string) $nombreFromContacto)) {
                $contactoNombre = trim((string) $nombreFromContacto);
            }
        }

        if (empty($contactoNombre) || $contactoNombre === '—') {
            $personasTmp = data_get($orden, 'solicitudServicio.cliente.personas', []);
            $personaCandidate = null;
            if (!empty($personasTmp)) {
                if (is_array($personasTmp)) {
                    $personaCandidate = $personasTmp[0] ?? null;
                } else {
                    $personaCandidate = count($personasTmp) ? $personasTmp->first() : null;
                }
            }
            if ($personaCandidate) {
                $pn = trim(implode(' ', array_filter([
                    data_get($personaCandidate, 'primer_nombre'),
                    data_get($personaCandidate, 'segundo_nombre'),
                    data_get($personaCandidate, 'primer_apellido'),
                    data_get($personaCandidate, 'segundo_apellido'),
                ])));
                if ($pn) $contactoNombre = $pn;
            }
        }

        if (empty($contactoNombre) || $contactoNombre === '—') {
            $ct = data_get($orden, 'solicitudServicio.cliente.contactos', []);
            if (!empty($ct)) {
                foreach ($ct as $c) {
                    $valor = trim((string) data_get($c, 'valor_contacto', ''));
                    if ($valor !== '' && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/', $valor)) {
                        $contactoNombre = $valor;
                        break;
                    }
                }
            }
        }

        return $contactoNombre ?: '—';
    }


    private function procesarTelefonos($orden)
    {
        $telefonosVal = '—';
        $ct = data_get($orden, 'solicitudServicio.cliente.contactos', []);
        $phones = [];

        if (!empty($ct)) {
            foreach ($ct as $c) {
                $tipo = strtolower(trim(data_get($c, 'tipo_contacto', '')));
                $valor = data_get($c, 'valor_contacto', '');
                if (in_array($tipo, ['tel', 'telefono', 'phone', 'movil', 'celular', 'whatsapp', 'wa'])) {
                    if ($valor) $phones[] = $valor;
                }
            }
            $phones = array_unique(array_filter($phones));
            if (count($phones)) $telefonosVal = implode(', ', $phones);
        }

        return $telefonosVal;
    }


    private function procesarCorreo($orden)
    {
        $correoVal = '—';
        $ct = data_get($orden, 'solicitudServicio.cliente.contactos', []);

        if (!empty($ct)) {
            foreach ($ct as $c) {
                $tipo = strtolower(trim(data_get($c, 'tipo_contacto', '')));
                $valor = data_get($c, 'valor_contacto', '');
                if (in_array($tipo, ['email', 'correo']) && $valor) {
                    $correoVal = $valor;
                    break;
                }
            }
        }

        if ($correoVal === '—') {
            $personas = data_get($orden, 'solicitudServicio.cliente.personas', []);
            if (!empty($personas)) {
                foreach ($personas as $p) {
                    $mail = data_get($p, 'usuario.correo_electronico');
                    if ($mail) {
                        $correoVal = $mail;
                        break;
                    }
                }
            }
        }

        return $correoVal;
    }

    private function procesarUbicacion($orden)
    {
        $direccionVal = '—';
        $oficinaVal = '—';
        $ciudadVal = '—';

        $agencias = data_get($orden, 'solicitudServicio.cliente.agencias', []);
        if (!empty($agencias)) {
            $ag = is_array($agencias) ? ($agencias[0] ?? null) : (count($agencias) ? $agencias->first() : null);
            if ($ag) {
                $oficinaVal = data_get($ag, 'nombre_agencia') ?: $oficinaVal;
                $dir = data_get($ag, 'direccion');
                if ($dir) {
                    $direccionVal = data_get($dir, 'direccion_completa')
                        ?: (trim((data_get($dir, 'calle', '') . ' ' . data_get($dir, 'numero', '') . ' ' . data_get($dir, 'colonia', '')))) ?: $direccionVal;
                    $ciudadVal = data_get($dir, 'ciudad.nombre_ciudad') ?: $ciudadVal;
                }
            }
        }

        return [
            'direccion' => $direccionVal,
            'oficina' => $oficinaVal,
            'ciudad' => $ciudadVal,
        ];
    }

    private function procesarRepuestos($orden)
    {
        $repuestosList = [];

        try {
            if (!empty($orden->repuestos)) {
                $tmp = is_array($orden->repuestos) ? $orden->repuestos : json_decode($orden->repuestos, true);
                if (is_array($tmp)) {
                    foreach ($tmp as $r) {
                        $label = data_get($r, 'nombre') ?? data_get($r, 'repuesto') ?? data_get($r, 'producto_nombre') ?? data_get($r, 'id_producto');
                        $cant = data_get($r, 'cantidad');
                        $repuestosList[] = $label . ($cant ? ' x' . $cant : '');
                    }
                }
            } elseif (!empty($orden->detallesProducto)) {
                foreach ($orden->detallesProducto as $d) {
                    $prodName = data_get($d, 'producto.nombre_producto') ?? data_get($d, 'producto.nombre') ?? data_get($d, 'id_producto_fk');
                    $repuestosList[] = ($prodName ?: 'Producto') . ' x' . ($d->cantidad ?? 1);
                }
            }
        } catch (\Throwable $_) {
            $repuestosList = [];
        }

        return $repuestosList;
    }

    /**
     * Catálogo de estados de orden de servicio (antes CatalogosController::estadosOrdenServicio).
     */
    public function estadosCatalog()
    {
        $items = \App\Models\EstadoOrdenServicio::select('id_estado_orden_servicio_pk as id', 'nombre', 'codigo')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
        return response()->json(['data' => $items, 'meta' => ['count' => $items->count()]]);
    }
}
