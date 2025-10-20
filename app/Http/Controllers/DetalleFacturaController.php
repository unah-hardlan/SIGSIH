<?php

namespace App\Http\Controllers;

use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\Servicio;
use App\Http\Resources\DetalleFacturaResource;
use App\Http\Requests\StoreDetalleFacturaRequest;
use App\Http\Requests\UpdateDetalleFacturaRequest;
use Illuminate\Http\Request;

class DetalleFacturaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DetalleFactura::with(['factura', 'servicio']);
            
            // Filtro por búsqueda general
            if ($q = $request->input('q')) {
                $query->where(function($sub) use ($q) {
                    $sub->where('descripcion', 'like', "%$q%")
                        ->orWhere('precio_unitario', 'like', "%$q%")
                        ->orWhere('cantidad', 'like', "%$q%")
                        ->orWhereHas('servicio', function($servicio) use ($q) {
                            $servicio->where('nombre_servicio', 'like', "%$q%");
                        })
                        ->orWhereHas('factura', function($factura) use ($q) {
                            $factura->where('numero_factura', 'like', "%$q%");
                        });
                });
            }
            
            // Filtro por servicio
            if ($servicio = $request->input('servicio')) {
                $query->where('id_servicio_fk', $servicio);
            }
            
            // Filtro por factura
            if ($factura = $request->input('factura')) {
                $query->where('id_factura_fk', $factura);
            }
            
            // Ordenamiento
            $sortable = [
                'fecha_servicio' => 'fecha_servicio',
                'horas' => 'horas',
                'descuento' => 'descuento',
                'precio_unitario' => 'precio_unitario',
                'cantidad' => 'cantidad',
                'total_linea' => 'total_linea'
            ];
            $sort = $request->input('sort');
            $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
            $query->orderBy($sortable[$sort] ?? 'id_detalle_pk', $direction);
            
            // Headers anti-caché
            $headers = [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];
            
            if ($request->boolean('all')) {
                return response()->json([
                    'success' => true,
                    'data' => DetalleFacturaResource::collection($query->get()),
                    'timestamp' => now()->toISOString()
                ], 200, $headers);
            }
            
            $perPage = (int)$request->input('per_page', 15);
            $items = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => DetalleFacturaResource::collection($items),
                'meta' => [
                    'page' => $items->currentPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'last_page' => $items->lastPage(),
                ],
                'timestamp' => now()->toISOString()
            ], 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles de factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreDetalleFacturaRequest $request)
    {
        try {
            $detalle = DetalleFactura::create($request->validated());
            $detalle->load(['factura', 'servicio']);
            
            return response()->json([
                'success' => true,
                'message' => 'Detalle de factura creado exitosamente',
                'data' => new DetalleFacturaResource($detalle)
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el detalle de factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $detalle = DetalleFactura::with(['factura', 'servicio'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => new DetalleFacturaResource($detalle)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detalle de factura no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateDetalleFacturaRequest $request, $id)
    {
        try {
            $detalle = DetalleFactura::findOrFail($id);
            $detalle->update($request->validated());
            $detalle->load(['factura', 'servicio']);
            
            return response()->json([
                'success' => true,
                'message' => 'Detalle de factura actualizado exitosamente',
                'data' => new DetalleFacturaResource($detalle)
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el detalle de factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $detalle = DetalleFactura::findOrFail($id);
            $detalle->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Detalle de factura eliminado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el detalle de factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener servicios para dropdown
     */
    public function getServicios(Request $request)
    {
        try {
            $headers = [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];
            
            $servicios = Servicio::select('id_servicio_pk', 'nombre_servicio', 'tarifa')->get();
            
            $serviciosFormateados = $servicios->map(function($servicio) {
                return [
                    'id' => $servicio->id_servicio_pk,
                    'nombre' => $servicio->nombre_servicio,
                    'tarifa' => $servicio->tarifa
                ];
            });
            
            return response()->json($serviciosFormateados, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener servicios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener facturas para dropdown
     */
    public function getFacturas(Request $request)
    {
        try {
            $headers = [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];
            
            $facturas = Factura::select('id_factura_pk', 'numero_factura')->get();
            
            $facturasFormateadas = $facturas->map(function($factura) {
                return [
                    'id' => $factura->id_factura_pk,
                    'numero' => $factura->numero_factura
                ];
            });
            
            return response()->json($facturasFormateadas, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
