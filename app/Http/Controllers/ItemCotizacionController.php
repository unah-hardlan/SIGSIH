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
        $item->load('cotizacion');
        return (new ItemCotizacionResource($item))->response();
    }

    public function destroy($id)
    {
        $item = ItemCotizacion::find($id);
        if (!$item) return response()->json(['error' => 'Item no encontrado'], 404);
        $item->delete();
        return response()->json(['message' => 'Item eliminado']);
    }
}
