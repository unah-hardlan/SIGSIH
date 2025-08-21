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
        if($cot = $request->input('id_cotizacion_fk')){ $query->where('id_cotizacion_fk',$cot); }
        if($q = $request->input('q')){
            $query->where('descripcion','like',"%$q%");
        }

        $sortable = [
            'descripcion' => 'descripcion',
            'precio' => 'precio_unitario',
            'cantidad' => 'cantidad',
            'total' => 'total',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','asc'))==='desc'?'desc':'asc';
        $query->orderBy($sortable[$sort] ?? 'id_item_cotizacion_pk',$direction);

        if($request->boolean('all')){
            return ItemCotizacionResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return ItemCotizacionResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreItemCotizacionRequest $request)
    {
        $item = ItemCotizacion::create($request->validated());
        $item->load('cotizacion');
        return (new ItemCotizacionResource($item))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $item = ItemCotizacion::with('cotizacion')->find($id);
        if(!$item) return response()->json(['error'=>'Item no encontrado'],404);
        return (new ItemCotizacionResource($item))->response();
    }

    public function update(UpdateItemCotizacionRequest $request, $id)
    {
        $item = ItemCotizacion::find($id);
        if(!$item) return response()->json(['error'=>'Item no encontrado'],404);
        $item->update($request->validated());
        $item->load('cotizacion');
        return (new ItemCotizacionResource($item))->response();
    }

    public function destroy($id)
    {
        $item = ItemCotizacion::find($id);
        if(!$item) return response()->json(['error'=>'Item no encontrado'],404);
        $item->delete();
        return response()->json(['message'=>'Item eliminado']);
    }
}
