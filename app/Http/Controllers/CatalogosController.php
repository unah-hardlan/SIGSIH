<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\EstadoOrdenServicio;

class CatalogosController extends Controller
{
    public function generos()
    {
        $items = \App\Models\Genero::select('id_genero_pk as id', 'genero')->orderBy('genero')->get();
        return response()->json(['data' => $items, 'meta' => ['count' => $items->count()]]);
    }

    public function estadosOrdenServicio()
    {
        $items = EstadoOrdenServicio::select('id_estado_orden_servicio_pk as id', 'nombre', 'codigo')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
        return response()->json(['data' => $items, 'meta' => ['count' => $items->count()]]);
    }

    public function tecnicos()
    {
        $roles = \App\Models\Rol::query()->where('rol', 'like', '%tecn%')->get(['id_rol_pk', 'rol']);
        if ($roles->isEmpty()) {
            return response()->json(['data' => [], 'meta' => ['count' => 0]]);
        }
        $roleIds = $roles->pluck('id_rol_pk')->all();
        $userIdsPrimary = \App\Models\Usuario::whereIn('id_rol_fk', $roleIds)->pluck('id_usuario_pk')->all();
        $userIdsPivot = DB::table('tbl_usuario_rol')->whereIn('id_rol_fk', $roleIds)->pluck('id_usuario_fk')->all();
        $userIds = collect($userIdsPrimary)->merge($userIdsPivot)->unique()->values()->all();
        if (empty($userIds)) {
            return response()->json(['data' => [], 'meta' => ['count' => 0]]);
        }
        $personas = \App\Models\Persona::whereIn('id_usuario_fk', $userIds)
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get(['id_persona_pk as id', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'id_usuario_fk']);
        return response()->json(['data' => $personas, 'meta' => ['count' => $personas->count()]]);
    }
}
