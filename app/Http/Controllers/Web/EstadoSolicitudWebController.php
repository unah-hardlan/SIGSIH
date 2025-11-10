<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadoSolicitudWebController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $perm = app(PermissionService::class);
        if (!$perm->can($user, ['Solicitudes', 'Gestión de Solicitudes', 'Gestion de Solicitudes'], 'consultar')) {
            return response()->json(['error' => 'Permiso denegado'], 403);
        }
        $items = DB::table('tbl_estado_solicitud')
            ->select([
                'id_estado_solicitud_pk as id',
                'codigo',
                'nombre as nombre_estado',
                'descripcion as descripcion_estado',
                'es_final',
                'orden',
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
        return response()->json([
            'data' => $items,
            'meta' => ['count' => $items->count()],
        ]);
    }
}
