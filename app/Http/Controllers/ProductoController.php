<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Http\Resources\ProductoResource;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query()->with('tipoProducto');

        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('sku', 'like', "%$q%")
                    ->orWhere('nombre_producto', 'like', "%$q%")
                    ->orWhere('descripcion_producto', 'like', "%$q%");
            });
        }
        if ($tipo = $request->input('id_tipo_producto_fk')) {
            $query->where('id_tipo_producto_fk', $tipo);
        }

        $sortable = [
            'nombre_producto' => 'nombre_producto',
            'precio_venta' => 'precio_venta',
            'id_producto_pk' => 'id_producto_pk',
        ];

        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortable[$sort] ?? 'nombre_producto', $direction);

        $productos = $query->get();

        return ProductoResource::collection($productos);
    }

    public function store(StoreProductoRequest $request)
    {
        $producto = Producto::create($request->validated());
        $producto->load('tipoProducto');
        return (new ProductoResource($producto))->response()->setStatusCode(201);
    }

    public function show(Producto $producto) 
    {
        $producto->load('tipoProducto');
        return new ProductoResource($producto);
    }

    public function update(UpdateProductoRequest $request, Producto $producto) 
    {
        $producto->update($request->validated());
        $producto->load('tipoProducto');
        return new ProductoResource($producto);
    }

    public function destroy(Producto $producto) 
    {
        $producto->delete();
        return response()->json(null, 204);
    }
}