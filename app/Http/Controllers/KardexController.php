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
        $query = Kardex::query()->with(['producto', 'tipoMovimiento', 'origen']);

        // Aplicar filtro de búsqueda desde el frontend (filtroKardex)
        if ($filtro = $request->input('filtroKardex')) {
            $query->where(function ($q) use ($filtro) {
                $q->where('motivo', 'like', "%{$filtro}%")
                    ->orWhereHas('producto', function ($q2) use ($filtro) {
                        $q2->where('nombre_producto', 'like', "%{$filtro}%")
                            ->orWhere('sku', 'like', "%{$filtro}%");
                    });
            });
        }

        $ordenarPor = $request->input('ordenarPor', 'fecha_movimiento');
        $ordenarDirection = $request->input('ordenarDirection', 'desc');

        $allowedColumns = ['fecha_movimiento', 'cantidad'];
        $allowedDirections = ['asc', 'desc'];
        if (!in_array($ordenarPor, $allowedColumns)) {
            $ordenarPor = 'fecha_movimiento';
        }
        if (!in_array($ordenarDirection, $allowedDirections)) {
            $ordenarDirection = 'desc';
        }

        $query->orderBy($ordenarPor, $ordenarDirection);

        $kardexItems = $query->get();

        return KardexResource::collection($kardexItems);
    }

    public function store(StoreKardexRequest $request)
    {
        $kardex = Kardex::create($request->validated());
        $kardex->load(['producto', 'tipoMovimiento', 'origen']);
        return (new KardexResource($kardex))->response()->setStatusCode(201);
    }

    public function show(Kardex $kardex)
    {
        $kardex->load(['producto', 'tipoMovimiento']);
        return new KardexResource($kardex);
    }

    public function update(UpdateKardexRequest $request, Kardex $kardex)
    {
        $kardex->update($request->validated());
        $kardex->load(['producto', 'tipoMovimiento']);
        return new KardexResource($kardex);
    }

    public function destroy(Kardex $kardex)
    {
        $kardex->delete();
        return response()->json(null, 204);
    }

    public function reporte(Request $request)
    {
        $query = Kardex::with(['producto', 'tipoMovimiento', 'origen']);


        if ($producto = $request->input('id_producto_fk')) {
            $query->where('id_producto_fk', $producto);
        }


        if ($tipo_movimiento = $request->input('id_tipo_movimiento_fk')) {
            $query->where('id_tipo_movimiento_fk', $tipo_movimiento);
        }


        if ($origen = $request->input('id_origen_fk')) {
            $query->where('id_origen_fk', $origen);
        }


        if ($fecha_desde = $request->input('fecha_desde')) {
            $query->where('fecha_movimiento', '>=', $fecha_desde);
        }

        if ($fecha_hasta = $request->input('fecha_hasta')) {
            $query->where('fecha_movimiento', '<=', $fecha_hasta);
        }


        if ($q = $request->input('q')) {
            $query->where('motivo', 'like', "%$q%");
        }


        $sortable = [
            'fecha_movimiento' => 'fecha_movimiento',
            'cantidad' => 'cantidad',
            'id_kardex_pk' => 'id_kardex_pk',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'desc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('fecha_movimiento', 'desc');
        }

        $kardex = $query->get();
        $total = $kardex->count();

        $fecha = \App\Helpers\DateHelper::nowFormatted('d/m/Y');
        $modulo = 'kardex';

        return view('admin.reporte-kardex', compact('kardex', 'total', 'fecha', 'modulo', 'sort', 'direction'));
    }
}
