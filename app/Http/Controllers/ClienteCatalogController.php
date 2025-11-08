<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteCatalogController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $q      = trim((string) $request->input('q', ''));
        $tipo   = strtolower((string) $request->input('tipo', ''));
        $perPage = (int) $request->input('per_page', 15);
        $all    = $request->boolean('all') || $perPage === -1;

        $agenciaId = $request->input('agencia_id');
        $agenciaId = is_numeric($agenciaId) ? (int) $agenciaId : null;


        $empQuery = DB::table('tbl_cliente_empresa as e')
            ->selectRaw('e.id_cliente_fk as id, "empresa" as tipo, COALESCE(NULLIF(e.nombre_comercial, ""), NULLIF(e.razon_social, ""), CONCAT("Empresa ", e.id_cliente_fk)) as nombre, e.rtn as rtn, NULL as dni, NULL as persona_id, NULL as primer_nombre, NULL as primer_apellido');
        if ($q !== '') {
            $empQuery->where(function ($sub) use ($q) {
                $like = "%{$q}%";
                $sub->where('e.nombre_comercial', 'like', $like)
                    ->orWhere('e.razon_social', 'like', $like)
                    ->orWhere('e.rtn', 'like', $like);
            });
        }
        if (!is_null($agenciaId)) {

            $empQuery->whereIn('e.id_cliente_fk', function ($q) use ($agenciaId) {
                $q->from('tbl_agencia_cliente')
                    ->select('id_cliente_fk')
                    ->where('id_agencia_fk', $agenciaId);
            });
        }


        $perQuery = DB::table('tbl_cliente_persona as cp')
            ->join('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
            ->selectRaw('cp.id_cliente_fk as id, "persona" as tipo, TRIM(CONCAT(COALESCE(p.primer_nombre, ""), " ", COALESCE(p.primer_apellido, ""))) as nombre, NULL as rtn, p.dni as dni, p.id_persona_pk as persona_id, p.primer_nombre, p.primer_apellido');
        if ($q !== '') {
            $perQuery->where(function ($sub) use ($q) {
                $like = "%{$q}%";
                $sub->where('p.primer_nombre', 'like', $like)
                    ->orWhere('p.segundo_nombre', 'like', $like)
                    ->orWhere('p.primer_apellido', 'like', $like)
                    ->orWhere('p.segundo_apellido', 'like', $like)
                    ->orWhere('p.dni', 'like', $like);
            });
        }
        if (!is_null($agenciaId)) {

            $perQuery->whereIn('cp.id_cliente_fk', function ($q) use ($agenciaId) {
                $q->from('tbl_agencia_cliente')
                    ->select('id_cliente_fk')
                    ->where('id_agencia_fk', $agenciaId);
            });
        }


        $empresas = ($tipo && $tipo !== 'empresa') ? collect() : collect($empQuery->get());
        $personas = ($tipo && $tipo !== 'persona') ? collect() : collect($perQuery->get());


        $merged = $empresas->concat($personas)
            ->groupBy('id')
            ->map(function ($group) {

                $empresa = $group->firstWhere('tipo', 'empresa');
                if ($empresa) return $empresa;
                return $group->first();
            })
            ->values();


        $sorted = $merged->sort(function ($a, $b) {
            if ($a->tipo === $b->tipo) {
                return strcasecmp($a->nombre, $b->nombre);
            }
            return $a->tipo === 'empresa' ? -1 : 1;
        })->values();

        if ($all) {
            return response()->json([
                'success' => true,
                'data' => $sorted,
                'pagination' => null,
            ]);
        }


        $pageNum = max(1, (int) $request->input('page', 1));
        $total = $sorted->count();
        $slice = $sorted->slice(($pageNum - 1) * $perPage, $perPage)->values();
        $lastPage = (int) ceil($total / $perPage ?: 1);

        return response()->json([
            'success' => true,
            'data' => $slice,
            'pagination' => [
                'current_page' => $pageNum,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $total ? (($pageNum - 1) * $perPage + 1) : null,
                'to' => $total ? (($pageNum - 1) * $perPage + $slice->count()) : null,
            ],
        ]);
    }
}