<?php

namespace App\Http\Controllers;

use App\Models\EstadoCotizacion;
use Illuminate\Http\Request;

class EstadoCotizacionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $query = EstadoCotizacion::query();
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'like', "%$q%")
                    ->orWhere('nombre', 'like', "%$q%")
                    ->orWhere('descripcion', 'like', "%$q%");
            });
        }
        $query->orderBy('orden')->orderBy('nombre');
        $items = $query->get(['id_estado_cotizacion_pk as id', 'codigo', 'nombre', 'descripcion', 'es_final', 'orden']);
        return response()->json(['data' => $items]);
    }
}
