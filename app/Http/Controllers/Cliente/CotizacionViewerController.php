<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CotizacionViewerController extends Controller
{
    public function show(Request $request)
    {
        $base = [
            'cot' => url('/cliente/cotizaciones/{id}/data'),
            'items' => url('/cliente/cotizaciones/{id}/items'),
        ];
        return view('admin.detalle-cotizacion', [
            'COTI_ENDPOINTS' => $base,
        ]);
    }
}
