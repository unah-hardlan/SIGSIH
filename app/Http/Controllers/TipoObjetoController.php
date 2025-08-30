<?php

namespace App\Http\Controllers;

use App\Models\TipoObjeto;
use Illuminate\Http\Request;
use App\Http\Resources\TipoObjetoResource;

class TipoObjetoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoObjeto::query();
        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q){
                $sub->where('nombre_tipo_objeto','like',"%$q%")
                    ->orWhere('descripcion_tipo_objeto','like',"%$q%");
            });
        }
        $query->orderBy('nombre_tipo_objeto','asc');

        if ($request->boolean('all')) {
            return TipoObjetoResource::collection($query->get());
        }

        $perPage = (int) $request->input('per_page', 20);
        $items = $query->paginate($perPage);
        return TipoObjetoResource::collection($items)->additional([
            'meta' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }
}
