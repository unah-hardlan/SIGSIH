<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Http\Resources\BitacoraResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BitacoraController extends Controller
{

    public function index(Request $request)
    {
        $q = $this->buildFilteredQuery($request);


        $sort = $request->query('sort', 'fecha_evento');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortable = [
            'fecha_evento' => 'tbl_ms_bitacora.fecha_evento',
            'accion' => 'tbl_ms_bitacora.accion',
            'fecha_creacion' => 'tbl_ms_bitacora.fecha_creacion',
            'usuario' => 'u.usuario',
            'objeto' => 'o.nombre_objeto',
        ];

        if ($sort === 'usuario') {
            $q->leftJoin('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'tbl_ms_bitacora.id_usuario_fk')
                ->select('tbl_ms_bitacora.*');
        } elseif ($sort === 'objeto') {
            $q->leftJoin('tbl_objetos as o', 'o.id_objetos_pk', '=', 'tbl_ms_bitacora.id_objetos_fk')
                ->select('tbl_ms_bitacora.*');
        }
        $q->orderBy($sortable[$sort] ?? 'tbl_ms_bitacora.fecha_evento', $direction);

        $pageSize = (int)($request->query('per_page', 10));
        $pageSize = max(5, min($pageSize, 100));
        $paginator = $q->paginate($pageSize)->appends($request->query());

        return BitacoraResource::collection($paginator);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $q = $this->buildFilteredQuery($request);

        $sort = $request->query('sort', 'fecha_evento');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortable = [
            'fecha_evento' => 'tbl_ms_bitacora.fecha_evento',
            'accion' => 'tbl_ms_bitacora.accion',
            'fecha_creacion' => 'tbl_ms_bitacora.fecha_creacion',
            'usuario' => 'u.usuario',
            'objeto' => 'o.nombre_objeto',
        ];

        if ($sort === 'usuario') {
            $q->leftJoin('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'tbl_ms_bitacora.id_usuario_fk')
                ->select('tbl_ms_bitacora.*');
        } elseif ($sort === 'objeto') {
            $q->leftJoin('tbl_objetos as o', 'o.id_objetos_pk', '=', 'tbl_ms_bitacora.id_objetos_fk')
                ->select('tbl_ms_bitacora.*');
        }
        $q->orderBy($sortable[$sort] ?? 'tbl_ms_bitacora.fecha_evento', $direction);

        $fileName = 'bitacora_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($q) {
            $output = fopen('php://output', 'w');

            // BOM para que Excel abra UTF-8 correctamente
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'ID',
                'Fecha Evento',
                'Usuario',
                'Objeto',
                'Accion',
                'Descripcion',
                'IP',
                'Creado Por',
                'Fecha Creacion',
            ]);

            $q->with(['usuario', 'objeto'])
                ->chunk(500, function ($rows) use ($output) {
                    foreach ($rows as $b) {
                        fputcsv($output, [
                            $b->id_bitacora_pk,
                            optional($b->fecha_evento)->format('Y-m-d H:i:s'),
                            optional($b->usuario)->usuario,
                            optional($b->objeto)->nombre_objeto,
                            $b->accion,
                            $b->descripcion,
                            $b->ip,
                            $b->creado_por,
                            optional($b->fecha_creacion)->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildFilteredQuery(Request $request)
    {
        $q = Bitacora::query();

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $q->where(function ($w) use ($search) {
                $w->where('descripcion', 'like', "%$search%")
                    ->orWhere('accion', 'like', "%$search%");
            });
        }
        if ($request->filled('accion')) {
            $q->where('accion', $request->query('accion'));
        }
        if ($request->filled('usuario')) {
            $usuario = strtoupper(trim($request->query('usuario')));
            $q->whereHas('usuario', function ($w) use ($usuario) {
                $w->where('usuario', 'like', "%$usuario%")
                    ->orWhereHas('persona', function ($personaQuery) use ($usuario) {
                        $personaQuery->where('primer_nombre', 'like', "%$usuario%")
                            ->orWhere('segundo_nombre', 'like', "%$usuario%")
                            ->orWhere('primer_apellido', 'like', "%$usuario%")
                            ->orWhere('segundo_apellido', 'like', "%$usuario%");
                    });
            });
        }
        if ($request->filled('objeto')) {
            $objeto = trim($request->query('objeto'));
            $q->whereHas('objeto', function ($w) use ($objeto) {
                $w->where('nombre_objeto', 'like', "%$objeto%");
            });
        }
        if ($request->filled('desde')) {
            $q->whereDate('fecha_evento', '>=', $request->query('desde'));
        }
        if ($request->filled('hasta')) {
            $q->whereDate('fecha_evento', '<=', $request->query('hasta'));
        }

        return $q->with(['usuario', 'objeto']);
    }


    public function create() {}


    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_evento' => ['nullable', 'date'],
            'id_usuario_fk' => ['required', 'integer', 'exists:tbl_ms_usuario,id_usuario_pk'],
            'id_objetos_fk' => ['nullable', 'integer', 'exists:tbl_objetos,id_objetos_pk'],
            'accion' => ['required', 'string', Rule::in(['Login', 'Insertar', 'Actualizar', 'Eliminar', 'Consulta', 'Logout'])],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        $bit = new Bitacora();
        $bit->fill($data);
        if (empty($bit->fecha_evento)) $bit->fecha_evento = now();
        $bit->save();

        $bit->load(['usuario', 'objeto']);
        return (new BitacoraResource($bit))
            ->response()
            ->setStatusCode(201);
    }


    public function show(string $id)
    {
        $bitacora = Bitacora::find($id);
        if (!$bitacora) {
            return response()->json(['error' => 'Registro de bitácora no encontrado'], 404);
        }
        $bitacora->load(['usuario', 'objeto']);
        return (new BitacoraResource($bitacora))
            ->response()
            ->setStatusCode(200);
    }


    public function edit(string $id) {}


    public function update(Request $request, string $id)
    {
        $bit = Bitacora::find($id);
        if (!$bit) return response()->json(['error' => 'No encontrado'], 404);

        $data = $request->validate([
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);
        $bit->fill($data);
        $bit->save();
        $bit->load(['usuario', 'objeto']);
        return new BitacoraResource($bit);
    }


    public function destroy(string $id)
    {
        $bit = Bitacora::find($id);
        if (!$bit) return response()->json(['error' => 'No encontrado'], 404);
        $bit->delete();
        return response()->json(['ok' => true]);
    }

    public function destroyAll()
    {
        try {
            Bitacora::query()->delete();
            return response()->json(['ok' => true, 'message' => 'Todos los registros de bitácora han sido eliminados']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al limpiar la bitácora: ' . $e->getMessage()], 500);
        }
    }
}
