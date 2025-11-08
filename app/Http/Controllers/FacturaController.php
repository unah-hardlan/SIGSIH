<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Cai;
use App\Models\DetalleFactura;
use App\Http\Resources\FacturaResource;
use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\UpdateFacturaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $facturas = Factura::with([
                'estadoFactura',
                'cai',
                'cliente.persona',
                'cliente.empresa'
            ])->get();

            $response = [
                'success' => true,
                'data' => FacturaResource::collection($facturas)->toArray($request)
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreFacturaRequest $request)
    {
        Log::info('=== INICIO store() factura ===', ['request_data' => $request->all()]);
        try {
            $validated = $request->validated();
            Log::info('Validation passed', ['validated' => $validated]);

            
            if (empty($validated['id_cai_fk'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar un CAI para generar la factura.'
                ], 422);
            }

            $factura = DB::transaction(function () use ($validated) {
                
                $cai = Cai::where('id_cai_pk', $validated['id_cai_fk'])
                    ->lockForUpdate()
                    ->firstOrFail();

                
                if (!empty($cai->fecha_limite) && $cai->fecha_limite < now()->format('Y-m-d')) {
                    throw new \Exception('El CAI seleccionado está vencido (fecha límite superada).');
                }

                
                $cai->loadMissing('estadoCai');
                if ($cai->estadoCai) {
                    $estadoNombre = Str::lower(trim((string)($cai->estadoCai->nombre_estado_cai
                        ?? $cai->estadoCai->nombre
                        ?? $cai->estadoCai->nombre_estado
                        ?? '')));
                    $estadoCodigo = Str::lower(trim((string)($cai->estadoCai->codigo ?? '')));
                    $esActivo = Str::contains($estadoNombre, 'activ') || Str::startsWith($estadoCodigo, 'act');
                    
                    $esVencido = Str::contains($estadoNombre, 'vencid') || Str::contains($estadoCodigo, 'venc');
                    if (!$esActivo || $esVencido) {
                        throw new \Exception('El CAI seleccionado no está ACTIVO (estado actual: ' . ($cai->estadoCai->nombre ?? $estadoCodigo ?: 'desconocido') . ') y no puede usarse en facturas.');
                    }
                }

                
                $parseRango = function (string $rango) {
                    $rango = (string) $rango;
                    
                    $digits = preg_replace('/\D+/', '', $rango ?? '');
                    
                    $digits = str_pad(substr($digits, -16), 16, '0', STR_PAD_LEFT);
                    $p1 = substr($digits, 0, 3);
                    $p2 = substr($digits, 3, 3);
                    $p3 = substr($digits, 6, 2);
                    $p4 = substr($digits, 8, 8);
                    return [$p1, $p2, $p3, $p4];
                };

                [$i1, $i2, $i3, $ini] = $parseRango((string) $cai->rango_inicio);
                [$f1, $f2, $f3, $fin] = $parseRango((string) $cai->rango_fin);

                
                $prefix = sprintf('%s-%s-%s-', $i1, $i2, $i3);
                $startNum = (int) $ini;
                $endNum = (int) $fin;

                
                $proximo = max(((int) $cai->consecutivo_actual) + 1, $startNum);

                if ($proximo > $endNum) {
                    throw new \Exception('El CAI seleccionado ya no tiene números disponibles en su rango.');
                }

                $payload = $validated;
                
                $payload['numero'] = 'TMP-'.now()->format('His').'-'.rand(1000, 9999); 
                $payload['id_cai_fk'] = $cai->id_cai_pk;

                
                $payload['subtotal'] = isset($payload['subtotal']) ? (float)$payload['subtotal'] : 0.0;
                $payload['impuesto'] = isset($payload['impuesto']) ? (float)$payload['impuesto'] : 0.0;
                $payload['total'] = isset($payload['total']) ? (float)$payload['total'] : 0.0;

                $factura = Factura::create($payload);

                
                try {
                    $fechaStr = $payload['fecha'] ?? ($factura->fecha ?? now()->format('Y-m-d'));
                    $fecha = Carbon::parse($fechaStr);
                    $fechaFmt = $fecha->format('Ymd');
                } catch (\Throwable $e) {
                    $fechaFmt = now()->format('Ymd');
                }
                $numeroFactura = sprintf('FAC-%s-%s', $fechaFmt, (string) $factura->getKey());
                $factura->numero = $numeroFactura;
                $factura->save();

                
                $cai->consecutivo_actual = $proximo;
                $cai->save();

                return $factura;
            });

            $factura->load(['estadoFactura', 'cai', 'cliente.persona', 'cliente.empresa']);

            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => new FacturaResource($factura)
            ], 201);
        } catch (\Throwable $e) {
            
            Log::error('Error al crear factura: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            
            
            $message = $e->getMessage();
            if (str_contains($message, 'CAI') && (
                str_contains($message, 'vencido') || 
                str_contains($message, 'ACTIVO') || 
                str_contains($message, 'números disponibles')
            )) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Factura $factura)
    {
        $factura->load(['estadoFactura', 'cai', 'cliente.persona', 'cliente.empresa']);
        return new FacturaResource($factura);
    }

    public function update(UpdateFacturaRequest $request, Factura $factura)
    {
        
        $validated = $request->validated();
        if (array_key_exists('id_cai_fk', $validated) && (int)$validated['id_cai_fk'] !== (int)$factura->id_cai_fk) {
            return response()->json([
                'success' => false,
                'message' => 'No se permite cambiar el CAI de una factura ya generada.'
            ], 422);
        }
        if (array_key_exists('numero', $validated) && $validated['numero'] !== $factura->numero) {
            return response()->json([
                'success' => false,
                'message' => 'No se permite modificar el número de factura.'
            ], 422);
        }

        $factura->update($validated);
        $factura->load(['estadoFactura', 'cai', 'cliente.persona', 'cliente.empresa']);
        return new FacturaResource($factura);
    }

    public function destroy(Factura $factura)
    {
        
        if ($factura->detalles()->count() > 0) {
            return response()->json([
                'success' => false, 
                'error' => 'No se puede eliminar la factura porque tiene detalles asociados. Elimine primero los detalles de la factura.'
            ], 422);
        }

        $factura->delete();
        return response()->json(['success' => true, 'message' => 'Factura eliminada']);
    }

    
    public function formatoFactura($id)
    {
        $factura = Factura::with([
            'estadoFactura',
            'cai',
            'cliente.persona',
            'cliente.empresa',
            'cliente.agencias.direccion.ciudad.departamento',
            'cliente.contactos',
            'cotizacion'
        ])->findOrFail($id);

        $detalles = DetalleFactura::where('id_factura_fk', $id)->get();

        $datosCliente = $this->procesarDatosCliente($factura->cliente);

        $totales = $this->calcularTotales($factura, $detalles);

        $datosContacto = $this->procesarDatosContacto($factura->cliente);

        $fechaLimite = $this->calcularFechaLimite($factura);

        return view('admin.formato-factura', compact(
            'factura', 
            'detalles', 
            'datosCliente', 
            'totales', 
            'datosContacto', 
            'fechaLimite'
        ));
    }

    private function procesarDatosCliente($cliente)
    {
        $cliente_nombre = 'Sin cliente';
        $cliente_direccion = '';
        $cliente_telefono = '';
        $cliente_correo = '';
        $cliente_contacto = '';

        if ($cliente) {
            if (($cliente->tipo_cliente ?? null) === 'empresa' && $cliente->empresa) {
                $cliente_nombre = $cliente->empresa->nombre_comercial ?? $cliente->empresa->razon_social ?? $cliente_nombre;
                $cliente_direccion = $cliente->empresa->direccion ?? '';
                $cliente_telefono = $cliente->empresa->telefono ?? '';
                $cliente_correo = $cliente->empresa->correo_electronico ?? '';
                $cliente_contacto = $cliente->empresa->contacto ?? '';
            } else {
                // persona
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

        return compact('cliente_nombre', 'cliente_direccion', 'cliente_telefono', 'cliente_correo', 'cliente_contacto');
    }

    private function calcularTotales($factura, $detalles)
    {
        $computedBaseSubtotal = 0.0;
        $computedTotalImpuesto = 0.0;
        
        if (!empty($detalles) && is_iterable($detalles)) {
            foreach ($detalles as $d) {
                $qty = (float) ($d->cantidad ?? $d->horas ?? 1);
                $precio = (float) ($d->precio_unitario ?? ($d->precio ?? 0));
                $desc = (float) ($d->descuento ?? 0);
                $impLinea = (float) ($d->impuesto ?? 0);

                $baseLine = $precio * $qty - $desc;
                $computedBaseSubtotal += $baseLine;

                $computedTotalImpuesto += $impLinea;
            }
        }

        $facturaSubtotal = (isset($factura->subtotal) && $factura->subtotal !== null && (float) $factura->subtotal > 0.0)
            ? (float) $factura->subtotal
            : $computedBaseSubtotal;

   
        if (isset($factura->total) && $factura->total !== null && (float) $factura->total > 0.0) {
            $facturaTotal = (float) $factura->total;
        } else {
            $impuestoFromFactura = (isset($factura->impuesto) && $factura->impuesto !== null && (float) $factura->impuesto > 0.0)
                ? (float) $factura->impuesto
                : $computedTotalImpuesto;
            $facturaTotal = $facturaSubtotal + $impuestoFromFactura;
        }

        
        if (isset($factura->impuesto) && $factura->impuesto !== null && (float) $factura->impuesto > 0.0) {
            $impuesto = (float) $factura->impuesto;
        } elseif ($computedTotalImpuesto > 0) {
            $impuesto = $computedTotalImpuesto;
        } else {
            $impuesto = round($facturaSubtotal * 0.15, 2);
        }

        $facturaTotal = round($facturaSubtotal + $impuesto, 2);

        return compact('facturaSubtotal', 'impuesto', 'facturaTotal');
    }

    private function procesarDatosContacto($cliente)
    {
        $ag = optional($cliente->agencias->first());
        $agDireccion = optional($ag->direccion);
        
        $telefono_fallback = '';
        $correo_fallback = '';
        
        if ($cliente) {
            if (($cliente->tipo_cliente ?? null) === 'empresa' && $cliente->empresa) {
                $telefono_fallback = $cliente->empresa->telefono ?? '';
                $correo_fallback = $cliente->empresa->correo_electronico ?? '';
            } else {
                $persona = $cliente->persona;
                if ($persona) {
                    if ($persona instanceof \Illuminate\Database\Eloquent\Collection) {
                        $persona = $persona->first();
                    }
                    $telefono_fallback = $persona->telefono ?? '';
                    $correo_fallback = $persona->correo_electronico ?? '';
                }
            }
        }

        if (empty($telefono_fallback) || empty($correo_fallback)) {
            $contactos = $cliente->contactos ?? collect();
            foreach ($contactos as $c) {
                $tipo = strtolower(trim($c->tipo_contacto ?? ''));
                $valor = $c->valor_contacto ?? '';
                if (empty($telefono_fallback) && in_array($tipo, ['telefono','tel','phone','movil','mobile'])) {
                    $telefono_fallback = $valor;
                }
                if (empty($correo_fallback) && in_array($tipo, ['email','correo','mail'])) {
                    $correo_fallback = $valor;
                }
            }
        }

        $addr_cp = '';
        $cliente_direccion = '';
        
        if ($cliente) {
            if (($cliente->tipo_cliente ?? null) === 'empresa' && $cliente->empresa) {
                $cliente_direccion = $cliente->empresa->direccion ?? '';
            } else {
                $persona = $cliente->persona;
                if ($persona) {
                    if ($persona instanceof \Illuminate\Database\Eloquent\Collection) {
                        $persona = $persona->first();
                    }
                    $cliente_direccion = $persona->direccion ?? '';
                }
            }
        }

        if (!empty($cliente_direccion)) {
            $addr_line1 = $cliente_direccion;
            $addr_colonia = '';
            $addr_city = '';
            $addr_depto = '';
        } elseif ($agDireccion) {
            $addr_line1 = trim(($agDireccion->calle ?? '') . ' ' . ($agDireccion->numero ?? '')) ?: ($agDireccion->direccion_completa ?? '');
            $addr_colonia = $agDireccion->colonia ?? '';
            $addr_cp = $agDireccion->codigo_postal ?? '';
            $addr_city = optional($agDireccion->ciudad)->nombre_ciudad ?? '';
            $addr_depto = optional(optional($agDireccion->ciudad)->departamento)->nombre_departamento ?? '';
        } else {
            $addr_line1 = '';
            $addr_colonia = '';
            $addr_city = '';
            $addr_depto = '';
        }

        $contactoNombre = '';
        if ($cliente) {
            if (($cliente->tipo_cliente ?? null) === 'empresa' && $cliente->empresa) {
                $contactoNombre = $cliente->empresa->contacto ?? '';
            } else {
                $persona = $cliente->persona;
                if ($persona) {
                    if ($persona instanceof \Illuminate\Database\Eloquent\Collection) {
                        $persona = $persona->first();
                    }
                    $contactoNombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) ?: ($persona->primer_nombre ?? '');
                }
            }
        }

        if (empty($contactoNombre)) {
            $contactos = $cliente->contactos ?? collect();
            $preferKeys = ['nombre','contacto','representante','contacto_persona','contacto_nombre'];
            foreach ($contactos as $c) {
                $tipo = strtolower(trim($c->tipo_contacto ?? ''));
                $valor = trim((string) ($c->valor_contacto ?? ''));
                if (in_array($tipo, $preferKeys) && $valor !== '') {
                    $contactoNombre = $valor;
                    break;
                }
            }
            if (empty($contactoNombre)) {
                foreach ($contactos as $c) {
                    $valor = trim((string) ($c->valor_contacto ?? ''));
                    if ($valor !== '' && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/', $valor)) {
                        $contactoNombre = $valor;
                        break;
                    }
                }
            }
        }

        return compact(
            'telefono_fallback', 
            'correo_fallback', 
            'addr_line1', 
            'addr_colonia', 
            'addr_cp', 
            'addr_city', 
            'addr_depto', 
            'contactoNombre'
        );
    }

    private function calcularFechaLimite($factura)
    {
        
        $fecha_limite = null;
        if (!empty($factura->fecha_limite_emision)) {
            $fecha_limite = $factura->fecha_limite_emision;
        } elseif (!empty($factura->fecha_limite)) {
            $fecha_limite = $factura->fecha_limite;
        } elseif (!empty(optional($factura->cai)->fecha_limite_emision)) {
            $fecha_limite = optional($factura->cai)->fecha_limite_emision;
        } elseif (!empty(optional($factura->cai)->fecha_limite)) {
            $fecha_limite = optional($factura->cai)->fecha_limite;
        }
        
        return $fecha_limite;
    }

    
    public function getClientes()
    {
        try {
            $clientes = Cliente::with(['empresa', 'persona'])->get();
 
            
            $clientesValidos = $clientes->filter(function ($cliente) {
                if ($cliente->tipo_cliente === 'empresa') {
                    return $cliente->empresa &&
                        ($cliente->empresa->nombre_comercial || $cliente->empresa->razon_social);
                } elseif ($cliente->tipo_cliente === 'persona') {
                    return $cliente->persona &&
                        ($cliente->persona->primer_nombre || $cliente->persona->primer_apellido);
                }
                return false;
            });

            $result = $clientesValidos->map(function ($cliente) {
                $nombre = 'Cliente sin datos';

                if ($cliente->tipo_cliente === 'empresa' && $cliente->empresa) {
                    $nombre = $cliente->empresa->nombre_comercial ?? $cliente->empresa->razon_social ?? 'Empresa sin nombre';
                } elseif ($cliente->type === 'persona' || $cliente->tipo_cliente === 'persona') {
                   
                    $persona = $cliente->persona;
                    if ($persona instanceof \Illuminate\Database\Eloquent\Collection) {
                        $persona = $persona->first();
                    }
                    $nombre = 'Persona sin nombre';
                    if ($persona) {
                        $nombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) ?: 'Persona sin nombre';
                    }
                }

                return [
                    'id' => $cliente->id_cliente_pk,
                    'nombre' => $nombre,
                    'tipo' => $cliente->tipo_cliente
                ];
            });

            return response()->json($result->values()); 

        } catch (\Exception $e) {

            return response()->json([
                ['id' => 1, 'nombre' => 'Error al cargar clientes']
            ], 500);
        }
    }
}