<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\ItemCotizacion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionPdfController extends Controller
{
    public function show($id)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        if (!$persona) {
            abort(403);
        }

        $clienteIds = DB::table('tbl_cliente_persona')
            ->where('id_persona_fk', $persona->id_persona_pk)
            ->pluck('id_cliente_fk')
            ->all();

        $cot = Cotizacion::with(['cliente.empresa', 'cliente.personas', 'estado'])
            ->find($id);
        if (!$cot) {
            abort(404);
        }
        if (!in_array($cot->id_cliente_fk, $clienteIds)) {
            // No revelar si existe: devolver 404 para privacidad
            abort(404);
        }

        $items = ItemCotizacion::where('id_cotizacion_fk', $cot->id_cotizacion_pk)->get();

        $fechaStr = optional($cot->fecha_cotizacion)->format('Ymd') ?: now()->format('Ymd');
        $codigo = 'COT-' . $fechaStr . '-' . $cot->id_cotizacion_pk;

        $pdf = Pdf::loadView('cliente.pdf.cotizacion', [
            'cotizacion' => $cot,
            'items' => $items,
            'codigo' => $codigo,
        ])->setPaper('a4');

        $filename = 'cotizacion_' . $codigo . '.pdf';
        return $pdf->stream($filename);
    }
}
