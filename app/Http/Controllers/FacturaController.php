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
        try {
            $validatedData = $request->validated();

            $factura = Factura::create($validatedData);

            $factura->load(['estadoFactura', 'cai', 'cliente.persona', 'cliente.empresa']);

            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => new FacturaResource($factura)
            ], 201);
        } catch (\Exception $e) {
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
        $factura->update($request->validated());
        $factura->load(['estadoFactura', 'cai', 'cliente.persona', 'cliente.empresa']);
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
            $clientes = Cliente::with(['empresa', 'persona'])->get();

            // Filtrar solo clientes que tienen datos válidos
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
                    // cliente->persona puede ser una colección; tomar el primer elemento si es necesario
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

            return response()->json($result->values()); // values() reindexar array

        } catch (\Exception $e) {

            return response()->json([
                ['id' => 1, 'nombre' => 'Error al cargar clientes']
            ], 500);
        }
    }
}