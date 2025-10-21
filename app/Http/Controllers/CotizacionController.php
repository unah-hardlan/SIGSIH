<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCotizacionRequest;
use App\Http\Requests\UpdateCotizacionRequest;
use App\Http\Resources\CotizacionResource;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::query()->with(['cliente.empresa', 'cliente.personas']);

        if ($cliente = $request->input('id_cliente_fk')) {
            $query->where('id_cliente_fk', $cliente);
        }
        if ($q = $request->input('q')) {
            // Busqueda sobre algunos campos numéricos convertidos a string
            $query->where(function ($sub) use ($q) {
                $sub->where('subtotal', 'like', "%$q%")
                    ->orWhere('total', 'like', "%$q%")
                    ->orWhere('impuesto', 'like', "%$q%");
            });
        }
        if ($desde = $request->input('desde')) {
            $query->whereDate('fecha_cotizacion', '>=', $desde);
        }
        if ($hasta = $request->input('hasta')) {
            $query->whereDate('fecha_cotizacion', '<=', $hasta);
        }

        $sortable = [
            'fecha' => 'fecha_cotizacion',
            'valido' => 'valido_hasta',
            'total' => 'total',
            'subtotal' => 'subtotal',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortable[$sort] ?? 'id_cotizacion_pk', $direction);

        if ($request->boolean('all')) {
            return CotizacionResource::collection($query->get());
        }
        $perPage = (int)$request->input('per_page', 15);
        $items = $query->paginate($perPage);
        return CotizacionResource::collection($items)->additional([
            'meta' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ]
        ]);
    }

    public function store(StoreCotizacionRequest $request)
    {
        $cotizacion = Cotizacion::create($request->validated());
        $cotizacion->load(['cliente.empresa', 'cliente.personas']);
        return (new CotizacionResource($cotizacion))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        // Traer datos del cliente suficientes para imprimir (empresa/persona)
        $cotizacion = Cotizacion::with(['cliente.empresa', 'cliente.personas'])->find($id);
        if (!$cotizacion) return response()->json(['error' => 'Cotizacion no encontrada'], 404);
        return (new CotizacionResource($cotizacion))->response();
    }

    public function update(UpdateCotizacionRequest $request, $id)
    {
        $cotizacion = Cotizacion::find($id);
        if (!$cotizacion) return response()->json(['error' => 'Cotizacion no encontrada'], 404);
        $cotizacion->update($request->validated());
        $cotizacion->load(['cliente.empresa', 'cliente.personas']);
        return (new CotizacionResource($cotizacion))->response();
    }

    public function destroy($id)
    {
        $cotizacion = Cotizacion::find($id);
        if (!$cotizacion) return response()->json(['error' => 'Cotizacion no encontrada'], 404);
        $cotizacion->delete();
        return response()->json(['message' => 'Cotizacion eliminada']);
    }
}
