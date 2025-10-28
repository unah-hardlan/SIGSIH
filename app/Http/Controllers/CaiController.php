<?php

namespace App\Http\Controllers;

use App\Models\Cai;
use App\Http\Resources\CaiResource;
use App\Http\Requests\StoreCaiRequest;
use App\Http\Requests\UpdateCaiRequest;
use Illuminate\Http\Request;

class CaiController extends Controller
{
    public function index()
    {
        try {
            $cais = Cai::with('estadoCai')->get();
            return response()->json([
                'success' => true,
                'data' => $cais->toArray()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 
    public function store(StoreCaiRequest $request)
    {
        try {
            $cai = Cai::create($request->validated());
            $cai->load('estadoCai');
            return response()->json([
                'success' => true,
                'message' => 'CAI creado exitosamente',
                'data' => new CaiResource($cai)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Cai $cai)
    {
        $cai->load('estadoCai');
        return new CaiResource($cai);
    }

    public function update(UpdateCaiRequest $request, $id)
    {
        try {
            $cai = Cai::findOrFail($id);
            $cai->update($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'CAI actualizado exitosamente',
                'data' => $cai->toArray()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cai = Cai::findOrFail($id);
            $deleted = $cai->delete();
            
            if ($deleted) {
                return response()->json([
                    'success' => true, 
                    'message' => 'CAI eliminado exitosamente'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo eliminar el CAI'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el CAI',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate cai report
     */
    public function reporte(Request $request)
    {
        $query = Cai::with(['estadoCai']);

        // Filtro por estado del CAI
        if ($estado = $request->input('estado')) {
            $query->whereHas('estadoCai', function($q) use ($estado) {
                $q->where('codigo', $estado);
            });
        }

        // Filtro de búsqueda por código
        if ($q = $request->input('q')) {
            $query->where('codigo', 'like', "%$q%");
        }

        // Filtro por rango de fechas
        if ($desde = $request->input('desde')) {
            $query->where('fecha_limite', '>=', $desde);
        }

        if ($hasta = $request->input('hasta')) {
            $query->where('fecha_limite', '<=', $hasta);
        }

        // Orden
        $sortable = [
            'codigo' => 'codigo',
            'fecha_limite' => 'fecha_limite',
            'estado' => 'id_estado_cai_fk',
        ];
        $sort = $request->input('sort');
        $direction = strtolower($request->input('direction', 'desc')) === 'desc' ? 'desc' : 'asc';
        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderBy('fecha_limite', 'desc');
        }

        $cais = $query->get();
        $total = $cais->count();
        $activos = $cais->filter(function($c) { return $c->estadoCai && strtolower($c->estadoCai->codigo) === 'act'; })->count();
        $agotados = $cais->filter(function($c) { return $c->estadoCai && strtolower($c->estadoCai->codigo) === 'cai-agt'; })->count();
        $cerrados = $cais->filter(function($c) { return $c->estadoCai && strtolower($c->estadoCai->codigo) === 'cai-cer'; })->count();

        $fecha = now()->format('d/m/Y');
        $modulo = 'cai';

        return view('admin.reporte-cai', compact('cais', 'total', 'activos', 'agotados', 'cerrados', 'fecha', 'modulo', 'sort', 'direction'));
    }
}
