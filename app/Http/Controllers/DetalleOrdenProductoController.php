<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrdenProducto;
use App\Http\Resources\DetalleOrdenProductoResource;
use App\Http\Requests\StoreDetalleOrdenProductoRequest;
use App\Http\Requests\UpdateDetalleOrdenProductoRequest;
use Illuminate\Http\Request;

class DetalleOrdenProductoController extends Controller
{
    public function index()
    {
        $detalles = DetalleOrdenProducto::with(['ordenServicio', 'producto'])->paginate(10);
        return DetalleOrdenProductoResource::collection($detalles);
    }

    public function store(StoreDetalleOrdenProductoRequest $request)
    {
        $detalle = DetalleOrdenProducto::create($request->validated());
        $detalle->load(['ordenServicio', 'producto']);
        return new DetalleOrdenProductoResource($detalle);
    }

    public function show(DetalleOrdenProducto $detalleOrdenProducto)
    {
        $detalleOrdenProducto->load(['ordenServicio', 'producto']);
        return new DetalleOrdenProductoResource($detalleOrdenProducto);
    }

    public function update(UpdateDetalleOrdenProductoRequest $request, DetalleOrdenProducto $detalleOrdenProducto)
    {
        $detalleOrdenProducto->update($request->validated());
        $detalleOrdenProducto->load(['ordenServicio', 'producto']);
        return new DetalleOrdenProductoResource($detalleOrdenProducto);
    }

    public function destroy(DetalleOrdenProducto $detalleOrdenProducto)
    {
        $detalleOrdenProducto->delete();
        return response()->json(['success' => true, 'message' => 'Detalle de orden producto eliminado']);
    }
}
