<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteCatalogController extends Controller
{
    /**
     * Endpoint unificado de clientes basado únicamente en:
     *  - tbl_cliente_empresa (empresas)
     *  - tbl_cliente_persona + tbl_persona (personas)
     * NO se consulta directamente tbl_cliente (solo se usan los id_cliente_fk que ya existen en esas tablas).
     *
     * Parámetros:
     *  - q: texto de búsqueda (nombre comercial, razón social, rtn, nombre persona, dni)
     *  - tipo: empresa|persona (filtrado opcional)
     *  - per_page: tamaño de página (default 15). all=1 o per_page=-1 devuelve todo.
     *
     * IMPORTANTE: Se omite filtrado por estado_cliente porque ese dato está en tbl_cliente.
     */
    public function index(Request $request): JsonResponse
    {
        $q      = trim((string) $request->input('q', ''));
        $tipo   = strtolower((string) $request->input('tipo', ''));
        $perPage = (int) $request->input('per_page', 15);
        $all    = $request->boolean('all') || $perPage === -1;

        // -------- Empresas --------
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

        // -------- Personas --------
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

        // Obtener colecciones (filtrando por tipo si se solicitó)
        $empresas = ($tipo && $tipo !== 'empresa') ? collect() : collect($empQuery->get());
        $personas = ($tipo && $tipo !== 'persona') ? collect() : collect($perQuery->get());

        // Unir y agrupar por id para evitar duplicados accidentales (ej: múltiples personas enlazadas mismo cliente)
        $merged = $empresas->concat($personas)
            ->groupBy('id')
            ->map(function ($group) {
                // Preferimos empresa si existe; si hay varias personas del mismo cliente tomar la primera.
                $empresa = $group->firstWhere('tipo', 'empresa');
                if ($empresa) return $empresa;
                return $group->first();
            })
            ->values();

        // Orden natural: empresas primero luego personas, y alfabético dentro de cada tipo.
        $sorted = $merged->sort(function ($a, $b) {
            if ($a->tipo === $b->tipo) {
                return strcasecmp($a->nombre, $b->nombre);
            }
            return $a->tipo === 'empresa' ? -1 : 1; // empresa antes que persona
        })->values();

        if ($all) {
            return response()->json([
                'success' => true,
                'data' => $sorted,
                'pagination' => null,
            ]);
        }

        // Paginación manual sobre la colección ya unificada
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