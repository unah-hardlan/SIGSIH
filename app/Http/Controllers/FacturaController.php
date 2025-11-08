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
            'cliente.agencias.direccion',
            'cliente.contactos',
            'cotizacion'
        ])->findOrFail($id);

        $detalles = DetalleFactura::where('id_factura_fk', $id)->get();

        return view('admin.formato-factura', compact('factura', 'detalles'));
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