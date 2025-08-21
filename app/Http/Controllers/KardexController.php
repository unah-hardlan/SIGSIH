<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKardexRequest;
use App\Http\Requests\UpdateKardexRequest;
use App\Http\Resources\KardexResource;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $query = Kardex::query()->with(['producto','tipoMovimiento','tecnico']);
        if($prod = $request->input('id_producto_fk')){ $query->where('id_producto_fk',$prod); }
        if($tipo = $request->input('id_tipo_movimiento_fk')){ $query->where('id_tipo_movimiento_fk',$tipo); }
        if($tec = $request->input('id_tecnico_fk')){ $query->where('id_tecnico_fk',$tec); }
        if($desde = $request->input('desde')){ $query->whereDate('fecha_movimiento','>=',$desde); }
        if($hasta = $request->input('hasta')){ $query->whereDate('fecha_movimiento','<=',$hasta); }

        $sortable = [
            'fecha' => 'fecha_movimiento',
            'cantidad' => 'cantidad',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction','desc'))==='asc'?'asc':'desc';
        $query->orderBy($sortable[$sort] ?? 'id_kardex_pk',$direction);

        if($request->boolean('all')){ return KardexResource::collection($query->get()); }
        $perPage = (int)$request->input('per_page',15);
        $items = $query->paginate($perPage);
        return KardexResource::collection($items)->additional([
            'meta'=>[
                'page'=>$items->currentPage(),
                'per_page'=>$items->perPage(),
                'total'=>$items->total(),
                'last_page'=>$items->lastPage(),
            ]
        ]);
    }

    public function store(StoreKardexRequest $request)
    {
        $kardex = Kardex::create($request->validated());
        $kardex->load(['producto','tipoMovimiento','tecnico']);
        return (new KardexResource($kardex))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $kardex = Kardex::with(['producto','tipoMovimiento','tecnico'])->find($id);
        if(!$kardex) return response()->json(['error'=>'Kardex no encontrado'],404);
        return (new KardexResource($kardex))->response();
    }

    public function update(UpdateKardexRequest $request, $id)
    {
        $kardex = Kardex::find($id);
        if(!$kardex) return response()->json(['error'=>'Kardex no encontrado'],404);
        $kardex->update($request->validated());
        $kardex->load(['producto','tipoMovimiento','tecnico']);
        return (new KardexResource($kardex))->response();
    }

    public function destroy($id)
    {
        $kardex = Kardex::find($id);
        if(!$kardex) return response()->json(['error'=>'Kardex no encontrado'],404);
        $kardex->delete();
        return response()->json(['message'=>'Kardex eliminado']);
    }
}
