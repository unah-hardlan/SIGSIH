<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Http\Resources\OrdenServicioResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Direccion;
use App\Models\OficinaEmpresa;
use App\Models\DetalleOrdenProducto;

class OrdenServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OrdenServicio::with([
            // Ensure cliente.personas is loaded so we can display person-type clients
            'solicitudServicio.cliente.empresa',
            'solicitudServicio.cliente.personas',
            'solicitudServicio.contacto',
            'tecnico',
            'estado',
            'cotizacion',
            'cotizacionGenerada',
        ]);

        // Filtros opcionales
        if ($request->has('id_solicitud_servicio_fk')) {
            $query->where('id_solicitud_servicio_fk', $request->id_solicitud_servicio_fk);
        }

        if ($request->has('id_tecnico_fk')) {
            $query->where('id_tecnico_fk', $request->id_tecnico_fk);
        }

        if ($request->has('fecha_recepcion')) {
            $query->whereDate('fecha_recepcion', $request->fecha_recepcion);
        }

        $ordenesServicio = $query->paginate(15);

        return OrdenServicioResource::collection($ordenesServicio);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_solicitud_servicio_fk' => 'required|integer|exists:tbl_solicitud,id_solicitud_pk',
            'id_tecnico_fk' => 'required|integer|exists:tbl_persona,id_persona_pk',
            'numero_orden_servicio' => 'nullable|string|max:50',
            'fecha_creada' => 'nullable|date',
            'fecha_asignada' => 'nullable|date',
            'fecha_recepcion' => 'required|date',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'observaciones' => 'nullable|string|max:500',
            'diagnostico_tecnico' => 'nullable|string|max:500',
            'diagnostico_cliente' => 'nullable|string|max:500',
            'calificacion_servicio' => 'nullable|string|in:excelente,bueno,regular,deficiente',
            'id_estado_orden_servicio_fk' => 'nullable|integer|exists:tbl_estado_orden_servicio,id_estado_orden_servicio_pk',
            'id_cotizacion_fk' => 'nullable|integer|exists:tbl_cotizacion,id_cotizacion_pk'
            ,
            'repuestos' => 'nullable|array',
            'repuestos.*.id_producto_fk' => 'required_with:repuestos|integer|exists:tbl_producto,id_producto_pk',
            'repuestos.*.cantidad' => 'required_with:repuestos|integer|min:1',
        ]);

        $ordenServicio = OrdenServicio::create($validatedData);
        // If the client sent repuestos inline, persist them into detalle table
        try {
            $inputRepuestos = $request->input('repuestos', []);
            if (is_array($inputRepuestos) && count($inputRepuestos)) {
                foreach ($inputRepuestos as $r) {
                    // Defensive mapping
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
            // Don't break creation on detalle errors
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

        // Construir y almacenar lista de repuestos asociados a la orden (si existen detalles registrados)
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
            // No interrumpir la creación por un problema al poblar repuestos
        }

        return new OrdenServicioResource($ordenServicio);
    }

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
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
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'observaciones' => 'nullable|string|max:500',
            'diagnostico_tecnico' => 'nullable|string|max:500',
            'diagnostico_cliente' => 'nullable|string|max:500',
            'calificacion_servicio' => 'nullable|string|in:excelente,bueno,regular,deficiente',
            'id_estado_orden_servicio_fk' => 'nullable|integer|exists:tbl_estado_orden_servicio,id_estado_orden_servicio_pk',
            'id_cotizacion_fk' => 'nullable|integer|exists:tbl_cotizacion,id_cotizacion_pk'
            ,
            'repuestos' => 'nullable|array',
            'repuestos.*.id_producto_fk' => 'required_with:repuestos|integer|exists:tbl_producto,id_producto_pk',
            'repuestos.*.cantidad' => 'required_with:repuestos|integer|min:1',
        ]);

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
        // Actualizar campo 'repuestos' tras la actualización (recalcular desde detalles existentes)
        // If the client provided repuestos in the request, replace existing detalle rows with the provided list
        try {
            $inputRepuestos = $request->input('repuestos', null);
            if (is_array($inputRepuestos)) {
                // Remove existing detalle rows for this order and recreate
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
            // Ignore detalle persistence errors — we still try to sync cached repuestos below
        }

        // Actualizar campo 'repuestos' tras la actualización (recalcular desde detalles existentes)
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
                // Si no quedan detalles, limpiar el campo
                $ordenServicio->forceFill(['repuestos' => null])->saveQuietly();
            }
        } catch (\Throwable $e) {
            // Ignorar errores de sincronización de repuestos
        }

        return new OrdenServicioResource($ordenServicio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ordenServicio = OrdenServicio::findOrFail($id);
        $ordenServicio->delete();

        return response()->json([
            'message' => 'Orden de servicio eliminada correctamente'
        ], Response::HTTP_OK);
    }

    /**
     * Renderiza el formato de reporte (vista imprimible) con todos los datos del cliente.
     * Intenta armar teléfono, correo, dirección y oficina a partir de las relaciones existentes
     * para mantener compatibilidad con esquemas que guardan estos valores en tbl_contacto o en
     * referencias a direccion/oficina.
     */
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

        // Contactos (tel/email/direccion) almacenados en tbl_contacto
        $contactos = $cliente?->contactos ?? collect();

        $telefonos = $contactos->filter(function ($c) {
            $t = strtolower(trim($c->tipo_contacto ?? ''));
            return in_array($t, ['tel', 'telefono', 'phone', 'movil', 'celular', 'whatsapp', 'wa']);
        })->pluck('valor_contacto')->unique()->values()->all();

        $correo = $contactos->firstWhere('tipo_contacto', 'email')?->valor_contacto
            ?? $contactos->firstWhere('tipo_contacto', 'correo')?->valor_contacto
            ?? '';

        // Dirección: preferir referencia en empresa (id_direccion_fk) si existe, si no, buscar contacto tipo 'direccion'
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

        // Oficina: preferir id_oficina_fk en empresa y resolver nombre si existe
        $oficina = '';
        if ($empresa && isset($empresa->id_oficina_fk) && $empresa->id_oficina_fk) {
            $of = OficinaEmpresa::find($empresa->id_oficina_fk);
            if ($of) $oficina = $of->nombre_oficina;
        }

        // Ciudad: preferir la ciudad de la direccion cargada o contacto con tipo ciudad
        $ciudad = '';
        if (!empty($direccion) && isset($dir) && $dir?->ciudad) {
            $ciudad = $dir->ciudad->nombre_ciudad ?? '';
        } else {
            $ciContacto = $contactos->first(function ($c) {
                return strtolower($c->tipo_contacto ?? '') === 'ciudad';
            });
            if ($ciContacto) $ciudad = $ciContacto->valor_contacto;
        }

        // Pasar variables útiles a la vista junto con el modelo completo
        return view('admin.formato-reporte', compact('orden', 'telefonos', 'correo', 'direccion', 'oficina', 'ciudad'));
    }
}
