<?php

namespace App\Http\Controllers;

use App\Models\ItemCotizacion;
use Illuminate\Http\Request;
use App\Http\Requests\StoreItemCotizacionRequest;
use App\Http\Requests\UpdateItemCotizacionRequest;
use App\Http\Resources\ItemCotizacionResource;

class ItemCotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemCotizacion::query()->with('cotizacion');
        if ($cot = $request->input('id_cotizacion_fk')) {
            $query->where('id_cotizacion_fk', $cot);
        }
        if ($q = $request->input('q')) {
            $query->where('descripcion', 'like', "%$q%");
        }

        $sortable = [
            'descripcion' => 'descripcion',
            'precio' => 'precio_unitario',
            'cantidad' => 'cantidad',
            'total' => 'total',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortable[$sort] ?? 'id_item_cotizacion_pk', $direction);

        if ($request->boolean('all')) {
            return ItemCotizacionResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page', 15);
        $items = $query->paginate($perPage);
        return ItemCotizacionResource::collection($items)->additional([
            'meta' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ]
        ]);
    }

    public function store(StoreItemCotizacionRequest $request)
    {
        $data = $request->validated();
        // If user provided a product reference, snapshot key fields from the product
        if (!empty($data['id_producto_fk'])) {
            $producto = \App\Models\Producto::find($data['id_producto_fk']);
            if ($producto) {
                $data['descripcion'] = $producto->nombre_producto ?? ($data['descripcion'] ?? '');
                // prefer precio_unitario from product unless payload explicitly provided
                $data['precio_unitario'] = $data['precio_unitario'] ?? $producto->precio_unitario ?? 0;
                $data['impuesto'] = $data['impuesto'] ?? $producto->impuesto ?? 0;
            }
        }
        $item = ItemCotizacion::create($data);
        // Recompute parent cotización totals to ensure subtotal (base), impuestos and total are consistent
        try {
            $this->recomputeCotizacionTotals($item->id_cotizacion_fk);
        } catch (
            \Throwable $e
        ) {
        }
        $item->load('cotizacion');
        return (new ItemCotizacionResource($item))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $item = ItemCotizacion::with('cotizacion')->find($id);
        if (!$item) return response()->json(['error' => 'Item no encontrado'], 404);
        return (new ItemCotizacionResource($item))->response();
    }

    public function update(UpdateItemCotizacionRequest $request, $id)
    {
        $item = ItemCotizacion::find($id);
        if (!$item) return response()->json(['error' => 'Item no encontrado'], 404);
        $data = $request->validated();
        if (!empty($data['id_producto_fk'])) {
            $producto = \App\Models\Producto::find($data['id_producto_fk']);
            if ($producto) {
                $data['descripcion'] = $producto->nombre_producto ?? ($data['descripcion'] ?? $item->descripcion);
                $data['precio_unitario'] = $data['precio_unitario'] ?? $producto->precio_unitario ?? $item->precio_unitario;
                $data['impuesto'] = $data['impuesto'] ?? $producto->impuesto ?? $item->impuesto;
            }
        }
        $item->update($data);
        try {
            $this->recomputeCotizacionTotals($item->id_cotizacion_fk);
        } catch (\Throwable $e) {
        }
        $item->load('cotizacion');
        return (new ItemCotizacionResource($item))->response();
    }

    public function destroy($id)
    {
        $item = ItemCotizacion::find($id);
        if (!$item) return response()->json(['error' => 'Item no encontrado'], 404);
        $cotId = $item->id_cotizacion_fk;
        $item->delete();
        try {
            $this->recomputeCotizacionTotals($cotId);
        } catch (\Throwable $e) {
        }
        return response()->json(['message' => 'Item eliminado']);
    }

    // Recompute totals for a cotización based on its items and update the cotización record.
    protected function recomputeCotizacionTotals($cotizacionId)
    {
        if (!$cotizacionId) return;
        $items = ItemCotizacion::where('id_cotizacion_fk', $cotizacionId)->get();
        $imponible = 0.0;
        $totalImpuesto = 0.0;
        foreach ($items as $it) {
            $pu = (float) ($it->precio_unitario ?? 0);
            $cant = (float) ($it->cantidad ?? 0);
            $imponible += $pu * $cant;
            $totalImpuesto += (float) ($it->impuesto ?? 0);
        }
        $cot = \App\Models\Cotizacion::find($cotizacionId);
        if (!$cot) return;
        $cot->imponible = $imponible;
        // subtotal = imponible + impuestos de items (sin incluir impuesto_otros)
        $cot->subtotal = $imponible;
        $otros = (float) ($cot->otros_cargos ?? 0);
        // No recalcular aquí: respetar el valor persistido de impuesto_otros proveniente del flujo de UI
        $otrosImp = (float) ($cot->impuesto_otros ?? 0.0);
        $totalImp = round($totalImpuesto + $otrosImp, 2);
        $cot->total_impuesto = $totalImp;
        $cot->impuesto = $totalImp;
        $cot->total = $imponible + $totalImpuesto + $otros + $otrosImp;
        // Reglas de negocio: anticipo es 50% del total
        try {
            $cot->anticipo_requerido = round(($cot->total ?? 0) * 0.5, 2);
        } catch (\Throwable $e) {
            // no-op si falla el cálculo
        }
        $cot->save();
    }
}
