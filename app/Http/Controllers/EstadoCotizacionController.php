<?php

namespace App\Http\Controllers;

use App\Models\EstadoCotizacion;
use Illuminate\Http\Request;

class EstadoCotizacionController extends Controller
{
    public function index(Request $request)
    {
        // Ensure base states exist so cotizacion forms can always load a valid catalog.
        if (EstadoCotizacion::count() === 0) {
            EstadoCotizacion::insert([
                [
                    'codigo' => 'borrador',
                    'nombre' => 'Borrador',
                    'descripcion' => 'Cotizacion en borrador',
                    'es_final' => 0,
                    'orden' => 1,
                ],
                [
                    'codigo' => 'enviada',
                    'nombre' => 'Enviada',
                    'descripcion' => 'Cotizacion enviada al cliente',
                    'es_final' => 0,
                    'orden' => 2,
                ],
                [
                    'codigo' => 'aprobada',
                    'nombre' => 'Aprobada',
                    'descripcion' => 'Cotizacion aprobada por el cliente',
                    'es_final' => 1,
                    'orden' => 3,
                ],
                [
                    'codigo' => 'rechazada',
                    'nombre' => 'Rechazada',
                    'descripcion' => 'Cotizacion rechazada por el cliente',
                    'es_final' => 1,
                    'orden' => 4,
                ],
            ]);
        }

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
