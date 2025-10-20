<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Http\Resources\FacturaResource;
use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\UpdateFacturaRequest;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $facturas = Factura::with([
                'estadoFactura', 
                'cai', 
                'cliente.personas',   // Usar 'personas' en lugar de 'persona'
                'cliente.empresa'
            ])->get();
            
            $response = [
                'success' => true,
                'data' => FacturaResource::collection($facturas)->toArray($request)
            ];
            
            return response()->json($response, 200);
        } catch (\Exception $e) {
            \Log::error('Error in facturas index:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreFacturaRequest $request)
    {
        try {
            $validatedData = $request->validated();
            \Log::info('Creating factura with data:', $validatedData);
            
            $factura = Factura::create($validatedData);
            \Log::info('Factura created with ID:', ['id' => $factura->id_factura_pk]);
            
            $factura->load(['estadoFactura', 'cai', 'cliente.personas', 'cliente.empresa']);
            
            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => new FacturaResource($factura)
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating factura:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Factura $factura)
    {
        $factura->load(['estadoFactura', 'cai', 'cliente.personas', 'cliente.empresa']);
        return new FacturaResource($factura);
    }

    public function update(UpdateFacturaRequest $request, Factura $factura)
    {
        $factura->update($request->validated());
        $factura->load(['estadoFactura', 'cai', 'cliente.personas', 'cliente.empresa']);
        return new FacturaResource($factura);
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        return response()->json(['success' => true, 'message' => 'Factura eliminada']);
    }

    /**
     * Obtener clientes para el dropdown
     */
    public function getClientes()
    {
        try {
            $clientes = Cliente::with(['empresa', 'personas'])->get(); // Usar 'personas' en lugar de 'persona'
            
            // Filtrar solo clientes que tienen datos válidos
            $clientesValidos = $clientes->filter(function($cliente) {
                if ($cliente->tipo_cliente === 'empresa') {
                    return $cliente->empresa && 
                           ($cliente->empresa->nombre_comercial || $cliente->empresa->razon_social);
                } elseif ($cliente->tipo_cliente === 'persona') {
                    // Obtener la primera persona de la colección
                    $persona = $cliente->personas->first();
                    return $persona && 
                           ($persona->primer_nombre || $persona->primer_apellido);
                }
                return false;
            });
            
            $result = $clientesValidos->map(function($cliente) {
                $nombre = 'Cliente sin datos';
                
                if ($cliente->tipo_cliente === 'empresa' && $cliente->empresa) {
                    $nombre = $cliente->empresa->nombre_comercial ?? $cliente->empresa->razon_social ?? 'Empresa sin nombre';
                } elseif ($cliente->tipo_cliente === 'persona') {
                    // Obtener la primera persona de la colección
                    $persona = $cliente->personas->first();
                    if ($persona) {
                        $nombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? ''));
                        if (empty($nombre)) {
                            $nombre = 'Persona sin nombre';
                        }
                    }
                }
                
                return [
                    'id' => $cliente->id_cliente_pk,
                    'nombre' => $nombre,
                    'tipo' => $cliente->tipo_cliente
                ];
            });
            
            return response()->json($result->values()); // values() reindexar array
            
        } catch (\Exception $e) {
            \Log::error('Error fetching clientes: ' . $e->getMessage());
            
            return response()->json([
                ['id' => 1, 'nombre' => 'Error al cargar clientes']
            ], 500);
        }
    }
}
