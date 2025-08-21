<?php

namespace App\Http\Controllers;

use App\Models\TipoProducto;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTipoProductoRequest;
use App\Http\Requests\UpdateTipoProductoRequest;
use App\Http\Resources\TipoProductoResource;

class TipoProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoProducto::query();
        if($q = $request->input('q')){
            $query->where(function($sub) use ($q){
                $sub->where('nombre_tipo_producto','like',"%$q%")
                    ->orWhere('descripcion_tipo_producto','like',"%$q%");
            });
        }
        $sortable = [
            'nombre' => 'nombre_tipo_producto',
            'descripcion' => 'descripcion_tipo_producto',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc' ? 'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_tipo_producto_pk', $direction);
        if($request->boolean('all')){
            return TipoProductoResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',10);
        $items = $query->paginate($perPage);
        return TipoProductoResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreTipoProductoRequest $request)
    {
        $tipo = TipoProducto::create($request->validated());
        return (new TipoProductoResource($tipo))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $tipo = TipoProducto::find($id);
        if(!$tipo) return response()->json(['error'=>'Tipo de producto no encontrado'],404);
        return (new TipoProductoResource($tipo))->response();
    }

    public function update(UpdateTipoProductoRequest $request, $id)
    {
        $tipo = TipoProducto::find($id);
        if(!$tipo) return response()->json(['error'=>'Tipo de producto no encontrado'],404);
        $tipo->update($request->validated());
        return (new TipoProductoResource($tipo))->response();
    }

    public function destroy($id)
    {
        $tipo = TipoProducto::find($id);
        if(!$tipo) return response()->json(['error'=>'Tipo de producto no encontrado'],404);
        $tipo->delete();
        return response()->json(['message'=>'Tipo de producto eliminado']);
    }
}
