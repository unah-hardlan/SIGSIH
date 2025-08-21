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
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_producto','like',"%$q%")
                    ->orWhere('descripcion_producto','like',"%$q%");
            });
        }
        if($tipo = $request->input('id_tipo_producto_fk')) $query->where('id_tipo_producto_fk',$tipo);
        $sortable = [
            'nombre' => 'nombre_producto',
            'precio_unitario' => 'precio_unitario',
            'precio_venta' => 'precio_venta',
            'stock' => 'stock_minimo',
            'fecha' => 'fecha_registro',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_producto_pk', $direction);
        if($request->boolean('all')){
            return ProductoResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',10);
        $items = $query->paginate($perPage);
        return ProductoResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreProductoRequest $request)
    {
        $producto = Producto::create($request->validated());
        $producto->load('tipoProducto');
        return (new ProductoResource($producto))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $producto = Producto::with('tipoProducto')->find($id);
        if(!$producto) return response()->json(['error'=>'Producto no encontrado'],404);
        return (new ProductoResource($producto))->response();
    }

    public function update(UpdateProductoRequest $request, $id)
    {
        $producto = Producto::find($id);
        if(!$producto) return response()->json(['error'=>'Producto no encontrado'],404);
        $producto->update($request->validated());
        $producto->load('tipoProducto');
        return (new ProductoResource($producto))->response();
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);
        if(!$producto) return response()->json(['error'=>'Producto no encontrado'],404);
        $producto->delete();
        return response()->json(['message'=>'Producto eliminado']);
    }
}
