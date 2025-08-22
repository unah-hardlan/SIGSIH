<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Http\Resources\FacturaResource;
use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\UpdateFacturaRequest;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['estadoFactura', 'cai', 'cliente'])->paginate(10);
        return FacturaResource::collection($facturas);
    }

    public function store(StoreFacturaRequest $request)
    {
        $factura = Factura::create($request->validated());
        $factura->load(['estadoFactura', 'cai', 'cliente']);
        return new FacturaResource($factura);
    }

    public function show(Factura $factura)
    {
        $factura->load(['estadoFactura', 'cai', 'cliente']);
        return new FacturaResource($factura);
    }

    public function update(UpdateFacturaRequest $request, Factura $factura)
    {
        $factura->update($request->validated());
        $factura->load(['estadoFactura', 'cai', 'cliente']);
        return new FacturaResource($factura);
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        return response()->json(['success' => true, 'message' => 'Factura eliminada']);
    }
}
