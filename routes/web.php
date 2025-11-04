<?php

use App\Http\Controllers\Admin\ViewLoaderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SessionTokenController;
use App\Http\Controllers\ProfileController;
// use App\Notifications\SystemNotification;
use App\Services\PermissionService;
use App\Support\AdminModuleRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas web de tu aplicación. Estas rutas
| son cargadas por el RouteServiceProvider y todas ellas estarán
| asignadas al grupo de middleware "web".
|
*/

// Auth routes (públicas)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Página bonita de verificación de correo (ES) y alias en EN para compatibilidad
Route::get('/verificar-correo', [AuthController::class, 'verifyEmailPage'])->name('verify.email.page');
Route::get('/verify-email', function (\Illuminate\Http\Request $request) {
    return redirect()->route('verify.email.page', [
        'token' => $request->query('token'),
        'email' => $request->query('email'),
    ]);
});

// Logout (protegido) usado por portal admin y cliente
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('logout');

// Intercambia la sesión web autenticada por un JWT para el SPA
Route::get('/session/token', [SessionTokenController::class, 'issue'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('session.token');



Route::get('/password/reset', [AuthController::class, 'showPasswordRecoverForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendPasswordResetEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset.form');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Redirección dinámica según rol autenticado
Route::get('/', function () {
    // Si no está autenticado todavía, mostrar login
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    $rolNombre = strtolower($user->rol->rol ?? '');
    if (in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {
        // Verificar si el cliente necesita configurar su perfil
        $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();

        if (
            !$persona ||
            empty($persona->primer_nombre) ||
            empty($persona->primer_apellido) ||
            empty($persona->dni) ||
            empty($persona->id_genero_fk)
        ) {
            return redirect()->route('cliente.configurar-perfil');
        }

        return redirect()->route('cliente.perfil');
    }
    // Por defecto admin
    return redirect()->route('admin.dashboard');
})->middleware(['auth.jwt.web', 'jwt.refresh'])->name('home.redirect');

// Ruta explícita reutilizable para redirecciones después de login via frontend
Route::get('/post-auth-redirect', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    $rolNombre = strtolower($user->rol->rol ?? '');
    if (in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {
        // Verificar si el cliente necesita configurar su perfil
        $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();

        if (
            !$persona ||
            empty($persona->primer_nombre) ||
            empty($persona->primer_apellido) ||
            empty($persona->dni) ||
            empty($persona->id_genero_fk)
        ) {
            return redirect()->route('cliente.configurar-perfil');
        }

        return redirect()->route('cliente.perfil');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth.jwt.web', 'jwt.refresh'])->name('post-auth.redirect');

// API-like fallbacks (cookie-based auth) for SPA when Bearer token is missing/expirado
Route::get('/api-web/dashboard/indicadores', [DashboardController::class, 'indicators'])
    ->middleware(['auth.jwt.web', 'admin.only'])
    ->name('dashboard.indicators.web');
Route::get('/api-web/dashboard/ordenes-estado', [DashboardController::class, 'ordenesPorEstado'])
    ->middleware(['auth.jwt.web', 'admin.only'])
    ->name('dashboard.ordenes.estado.web');
Route::get('/api-web/dashboard/cotizaciones-mes', [DashboardController::class, 'cotizacionesPorMes'])
    ->middleware(['auth.jwt.web', 'admin.only'])
    ->name('dashboard.cotizaciones.mes.web');
Route::get('/api-web/dashboard/proyectos-estado', [DashboardController::class, 'proyectosPorEstado'])
    ->middleware(['auth.jwt.web', 'admin.only'])
    ->name('dashboard.proyectos.estado.web');

// Catálogo de Estados de Solicitud (cookie-auth para SPA admin)
Route::get('/api-web/estados-solicitud', function (\Illuminate\Http\Request $request) {
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
})->middleware(['auth.jwt.web', 'admin.only'])->name('api.web.estados.solicitud');

// API-like fallback para cambiar contraseña del perfil (cookie-based auth)
Route::post('/api-web/me/password', [ProfileController::class, 'changePassword'])
    ->middleware(['auth.jwt.web'])
    ->name('perfil.password.web');

// API-like para configuración del sistema (parámetros generales)
Route::get('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'show'])
    ->middleware(['auth.jwt.web', 'admin.only'])
    ->name('system.settings.show.web');
Route::post('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'update'])
    ->middleware(['auth.jwt.web', 'admin.only'])
    ->name('system.settings.update.web');

// Notificaciones (cookie-auth para admin y cliente)
Route::get('/api/notificaciones', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['data' => [], 'meta' => ['unread' => 0]]);
    }

    try {
        $items = \App\Models\DbNotification::query()
            ->where('tipo_notificable', \App\Models\Usuario::class)
            ->where('id_notificable', $user->id_usuario_pk)
            ->orderByDesc('fecha_creacion')
            ->limit(20)
            ->get();

        $mapped = $items->map(function ($n) {
            return [
                'id' => $n->id_notificacion ?? $n->id,
                'title' => $n->data['title'] ?? '',
                'body' => $n->data['body'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'icon' => $n->data['icon'] ?? 'fa-bell',
                'severity' => $n->data['severity'] ?? 'info',
                'module' => $n->data['module'] ?? null,
                'created_at' => optional($n->fecha_creacion)->toDateTimeString(),
                'read_at' => optional($n->fecha_lectura)->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $mapped,
            'meta' => [
                'unread' => $items->whereNull('fecha_lectura')->count(),
            ],
        ]);
    } catch (\Throwable $e) {
        // Si hay algún problema con la tabla/columnas, devolver vacío para no romper el SPA
        return response()->json(['data' => [], 'meta' => ['unread' => 0]]);
    }
})->middleware(['auth.jwt.web', 'jwt.refresh'])->name('api.notificaciones');

// Marcar todas como leídas (cookie-auth) para Admin y Cliente
Route::post('/api/notificaciones/mark-all-read', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['ok' => false], 401);
    }
    try {

        \App\Models\DbNotification::query()
            ->where('tipo_notificable', \App\Models\Usuario::class)
            ->where('id_notificable', $user->id_usuario_pk)
            ->whereNull('fecha_lectura')
            ->update(['fecha_lectura' => now()]);

        return response()->json(['ok' => true]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false], 500);
    }
})->middleware(['auth.jwt.web', 'jwt.refresh'])->name('api.notificaciones.markAll');

// Marcar una como leída (cookie-auth)
Route::post('/api/notificaciones/{id}/read', function ($id) {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['ok' => false], 401);
    }
    try {
        $n = \App\Models\DbNotification::query()
            ->where('id_notificacion', $id)
            ->where('tipo_notificable', \App\Models\Usuario::class)
            ->where('id_notificable', $user->id_usuario_pk)
            ->first();
        if ($n) {
            $n->markAsRead();
        }
        return response()->json(['ok' => true]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false], 500);
    }
})->middleware(['auth.jwt.web', 'jwt.refresh'])->name('api.notificaciones.read');

// API-like fallbacks para Reportes (cookie-based auth)
Route::middleware(['auth.jwt.web', 'admin.only'])->group(function () {
    // Reportes de visita CRUD básico
    Route::get('/api-web/reportes-visita', [\App\Http\Controllers\ReporteVisitaController::class, 'index']);
    Route::get('/api-web/reportes-visita/{id}', [\App\Http\Controllers\ReporteVisitaController::class, 'show']);
    Route::post('/api-web/reportes-visita', [\App\Http\Controllers\ReporteVisitaController::class, 'store']);
    Route::match(['put', 'patch'], '/api-web/reportes-visita/{id}', [\App\Http\Controllers\ReporteVisitaController::class, 'update']);
    Route::delete('/api-web/reportes-visita/{id}', [\App\Http\Controllers\ReporteVisitaController::class, 'destroy']);

    // Catálogos necesarios (solo index)
    Route::get('/api-web/tipos-visita', [\App\Http\Controllers\TipoVisitaController::class, 'index']);
    Route::get('/api-web/servicios-realizados', [\App\Http\Controllers\ServicioRealizadoController::class, 'index']);
    Route::get('/api-web/acciones-realizadas', [\App\Http\Controllers\AccionRealizadaController::class, 'index']);
    Route::get('/api-web/ordenes-servicio', [\App\Http\Controllers\OrdenServicioController::class, 'index']);
});

// Partial view loading for SPA (protegido)
Route::get('/load-view', [ViewLoaderController::class, 'load'])
    ->name('load-view')
    ->middleware(['auth.jwt.web', 'force.profile']);

// Admin routes group - SPA Entry Point (PROTEGIDO)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['spa.init', 'auth.jwt.web', 'jwt.refresh', 'force.profile', 'block.client'])
    ->group(function () {

        // Dashboard
        Route::get('dashboard', fn() => view('layouts.admin')->with('partialView', 'admin.partials.dashboard'))->name('dashboard');

        // Vistas parciales administradas por registro
        foreach (AdminModuleRegistry::views() as $viewKey => $definition) {
            if ($viewKey === 'dashboard') {
                continue; // ya definido
            }
            if (($definition['type'] ?? 'partial') !== 'partial') {
                continue; // se manejan más abajo (p.ej., reportes completos)
            }

            Route::get($viewKey, function () use ($viewKey, $definition) {
                $user = auth()->user();
                $candidates = AdminModuleRegistry::permissionCandidates($viewKey);
                if (!empty($candidates)) {
                    $perm = app(PermissionService::class);
                    if (!$perm->can($user, $candidates, 'consultar')) {
                        abort(403, 'Permiso denegado');
                    }
                }

                $partialBlade = $definition['blade'] ?? "admin.partials.{$viewKey}";

                return view('layouts.admin')->with('partialView', $partialBlade);
            })->name($viewKey);
        }

        // Reportes Header (con mapeo completo)
        Route::get('reportes-header', function (Request $request) {
            $modulo = $request->query('modulo', '');
            $fecha = $request->query('fecha', now()->format('d-M-Y'));
            $moduloLower = strtolower($modulo);
            $ordenarPor = $request->query('ordenar_por');
            $search = $request->query('search');
            $estadoEmpresa = $request->query('estado_empresa');
            $fechaGeneracion = $request->query('fecha_generacion');

            if ($moduloLower === 'usuarios') {
                return app(\App\Http\Controllers\UsuarioController::class)->reporte($request);
            }
            if ($moduloLower === 'parametros') {
                return app(\App\Http\Controllers\ParametroController::class)->reporte($request);
            }
            if ($moduloLower === 'proyectos') {
                return app(\App\Http\Controllers\ProyectoController::class)->reporte($request);
            }
            if ($moduloLower === 'movimientos-proyecto') {
                return app(\App\Http\Controllers\ProyectoController::class)->reporteMovimientos($request);
            }
            if ($moduloLower === 'proyecto-financiero') {
                return app(\App\Http\Controllers\ProyectoController::class)->reporteFinanciero($request);
            }
            if ($moduloLower === 'tickets') {
                return app(\App\Http\Controllers\TicketController::class)->reporte($request);
            }
            if ($moduloLower === 'agencias') {
                return app(\App\Http\Controllers\AgenciasController::class)->reporte($request);
            }
            if ($moduloLower === 'calendario') {
                return app(\App\Http\Controllers\CalendarioController::class)->reporte($request);
            }
            if ($moduloLower === 'cai') {
                return app(\App\Http\Controllers\CaiController::class)->reporte($request);
            }
            if ($moduloLower === 'productos') {
                return app(\App\Http\Controllers\ProductoController::class)->reporte($request);
            }
            if ($moduloLower === 'kardex') {
                return app(\App\Http\Controllers\KardexController::class)->reporte($request);
            }

            $view = match ($moduloLower) {
                'configuracion de accesos', 'configuracion-acceso' => null,
                'empresas' => 'admin.reporte-empresas',
                'solicitudes' => 'admin.reporte-solicitudes',
                'tickets' => 'admin.reporte-tickets',
                'agencias' => 'admin.reporte-agencias',
                'calendario' => 'admin.reporte-calendario',
                'facturas' => 'admin.reporte-facturas',
                'cai' => 'admin.reporte-cai',
                'bitacora' => 'admin.reporte-bitacora',
                'productos' => 'admin.reporte-productos',
                'kardex' => 'admin.reporte-kardex',
                'proyectos' => 'admin.reporte-proyectos',
                default => 'admin.reporte-generico',
            };
            if ($moduloLower === 'configuracion de accesos' || $moduloLower === 'configuracion-acceso') {
                return app(\App\Http\Controllers\ConfiguracionAccesoReporteController::class)->reporte($request);
            }
            if ($moduloLower === 'gestion de personas') {
                return app(\App\Http\Controllers\PersonaController::class)->reporte($request);
            }
            // Capa dinámica específica para Empresas (según esquema actual)
            if ($moduloLower === 'empresas') {
                $query = \App\Models\Cliente::query()
                    ->where('tipo_cliente', 'empresa')
                    ->join('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'tbl_cliente.id_cliente_pk')
                    ->select([
                        'tbl_cliente.id_cliente_pk',
                        'tbl_cliente.fecha_registro',
                        'tbl_cliente.estado_cliente',
                        'ce.nombre_comercial',
                        'ce.razon_social',
                        'ce.rtn',
                        'ce.descripcion_empresa',
                        'ce.horario_atencion',
                    ]);

                if (!empty($search)) {
                    $s = "%" . $search . "%";
                    $query->where(function ($q) use ($s) {
                        $q->where('ce.nombre_comercial', 'like', $s)
                            ->orWhere('ce.razon_social', 'like', $s)
                            ->orWhere('ce.rtn', 'like', $s);
                    });
                }

                if ($estadoEmpresa && in_array(strtolower($estadoEmpresa), ['activo', 'inactivo'])) {
                    $query->where('tbl_cliente.estado_cliente', strtolower($estadoEmpresa));
                }

                $allowedOrden = [
                    'nombre_empresa' => 'ce.nombre_comercial',
                    'estado_empresa' => 'tbl_cliente.estado_cliente',
                    'fecha_registro' => 'tbl_cliente.fecha_registro',
                ];
                if ($ordenarPor && isset($allowedOrden[$ordenarPor])) {
                    $query->orderBy($allowedOrden[$ordenarPor], 'asc');
                } else {
                    $query->orderBy('tbl_cliente.fecha_registro', 'desc');
                }

                $empresas = $query->get();

                // Catálogos opcionales; si no existen las tablas, devolver colecciones vacías para no romper el reporte
                try {
                    $nombresEmpresa = \App\Models\NombreEmpresa::select('id_nombre_empresa_pk', 'nombre_empresa', 'descripcion_empresa')
                        ->orderBy('nombre_empresa')
                        ->get();
                } catch (\Throwable $e) {
                    $nombresEmpresa = collect();
                }
                try {
                    $oficinasEmpresa = \App\Models\OficinaEmpresa::select('id_oficina_empresa_pk', 'nombre_oficina')
                        ->orderBy('nombre_oficina')
                        ->get();
                } catch (\Throwable $e) {
                    $oficinasEmpresa = collect();
                }

                return view($view, compact('fecha', 'modulo', 'empresas', 'ordenarPor', 'search', 'estadoEmpresa', 'fechaGeneracion', 'nombresEmpresa', 'oficinasEmpresa'));
            }
            // Capa dinámica específica para Solicitudes (según filtros del UI)
            if ($moduloLower === 'solicitudes') {
                $ordenarPor = $request->query('ordenar_por');
                $search = $request->query('search');
                $estadoSolicitud = $request->query('estado_solicitud');

                $clienteNombreExpr = "COALESCE(ce.nombre_comercial, ce.razon_social, CONCAT_WS(' ', p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido))";

                $query = DB::table('tbl_solicitud as s')
                    ->join('tbl_cliente as c', 'c.id_cliente_pk', '=', 's.id_cliente_fk')
                    ->leftJoin('tbl_cliente_empresa as ce', 'ce.id_cliente_fk', '=', 'c.id_cliente_pk')
                    ->leftJoin('tbl_cliente_persona as cp', 'cp.id_cliente_fk', '=', 'c.id_cliente_pk')
                    ->leftJoin('tbl_persona as p', 'p.id_persona_pk', '=', 'cp.id_persona_fk')
                    ->leftJoin('tbl_estado_solicitud as es', 'es.id_estado_solicitud_pk', '=', 's.id_estado_solicitud_fk')
                    ->leftJoin('tbl_contacto as co', 'co.id_contacto_pk', '=', 's.id_contacto_fk')
                    ->select([
                        's.id_solicitud_pk as id',
                        's.numero_solicitud_acf',
                        's.numero_solicitud_cliente',
                        's.descripcion_problema',
                        's.id_estado_solicitud_fk',
                        's.id_cliente_fk',
                        'es.nombre as estado_nombre',
                        'co.valor_contacto',
                        DB::raw($clienteNombreExpr . ' as cliente_nombre'),
                    ]);

                if (!empty($estadoSolicitud)) {
                    // Si viene un id numérico, filtra por FK. Si viene texto, intenta por nombre/código
                    if (is_numeric($estadoSolicitud)) {
                        $query->where('s.id_estado_solicitud_fk', (int) $estadoSolicitud);
                    } else {
                        $query->where(function ($q) use ($estadoSolicitud) {
                            $q->where('es.nombre', 'like', '%' . $estadoSolicitud . '%')
                                ->orWhere('es.codigo', 'like', '%' . $estadoSolicitud . '%');
                        });
                    }
                }

                if (!empty($search)) {
                    $s = '%' . $search . '%';
                    $query->where(function ($q) use ($s, $clienteNombreExpr) {
                        $q->where('s.numero_solicitud_acf', 'like', $s)
                            ->orWhere('s.numero_solicitud_cliente', 'like', $s)
                            ->orWhere('s.descripcion_problema', 'like', $s)
                            ->orWhere('es.nombre', 'like', $s)
                            ->orWhere('es.codigo', 'like', $s)
                            ->orWhere(DB::raw($clienteNombreExpr), 'like', $s);
                    });
                }

                // Map de orden permitido
                $allowedOrden = [
                    'estado_solicitud' => 'es.nombre',
                    'cliente' => DB::raw($clienteNombreExpr),
                    'solicitud_acf' => 's.numero_solicitud_acf',
                    'solicitud_cliente' => 's.numero_solicitud_cliente',
                ];
                if ($ordenarPor && isset($allowedOrden[$ordenarPor])) {
                    $orderColumn = $allowedOrden[$ordenarPor];
                    // orderColumn puede ser raw expression o string
                    $query->orderBy($orderColumn, 'asc');
                } else {
                    $query->orderBy('es.nombre', 'asc')
                        ->orderBy('s.id_solicitud_pk', 'asc');
                }

                // Evitar duplicados por join con personas
                $solicitudes = $query->distinct()->get();

                return view($view, compact('fecha', 'modulo', 'solicitudes', 'ordenarPor', 'search', 'estadoSolicitud', 'fechaGeneracion'));
            }
            // Capa dinámica específica para Bitácora (según filtros del UI)
            if ($moduloLower === 'bitacora') {
                $search = $request->query('search');
                $accion = $request->query('accion');
                $usuario = $request->query('usuario');
                $objeto = $request->query('objeto');
                $desde = $request->query('desde');
                $hasta = $request->query('hasta');
                $sort = $request->query('sort', 'fecha_evento');
                $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

                $q = DB::table('tbl_ms_bitacora as b')
                    ->leftJoin('tbl_ms_usuario as u', 'u.id_usuario_pk', '=', 'b.id_usuario_fk')
                    ->leftJoin('tbl_objetos as o', 'o.id_objetos_pk', '=', 'b.id_objetos_fk')
                    ->select([
                        'b.id_bitacora_pk as id',
                        'b.fecha_evento',
                        'b.accion',
                        'b.descripcion',
                        'b.creado_por',
                        'b.fecha_creacion',
                        'u.usuario as usuario_nombre',
                        'o.nombre_objeto as objeto_nombre',
                    ]);

                if (!empty($accion)) {
                    $q->where('b.accion', $accion);
                }
                if (!empty($usuario)) {
                    $uLike = '%' . $usuario . '%';
                    $q->where('u.usuario', 'like', $uLike);
                }
                if (!empty($objeto)) {
                    $oLike = '%' . $objeto . '%';
                    $q->where('o.nombre_objeto', 'like', $oLike);
                }
                if (!empty($search)) {
                    $s = '%' . $search . '%';
                    $q->where(function ($w) use ($s) {
                        $w->where('b.accion', 'like', $s)
                            ->orWhere('b.descripcion', 'like', $s);
                    });
                }
                if (!empty($desde)) {
                    $q->whereDate('b.fecha_evento', '>=', $desde);
                }
                if (!empty($hasta)) {
                    $q->whereDate('b.fecha_evento', '<=', $hasta);
                }

                $allowedSort = [
                    'fecha_evento' => 'b.fecha_evento',
                    'usuario' => 'u.usuario',
                    'objeto' => 'o.nombre_objeto',
                    'accion' => 'b.accion',
                    'fecha_creacion' => 'b.fecha_creacion',
                ];
                $q->orderBy($allowedSort[$sort] ?? 'b.fecha_evento', $direction);

                $rows = $q->get();
                // Mapear a estructura esperada por la vista (con claves anidadas usuario/objeto)
                $items = $rows->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'fecha_evento' => $r->fecha_evento,
                        'accion' => $r->accion,
                        'descripcion' => $r->descripcion,
                        'creado_por' => $r->creado_por,
                        'fecha_creacion' => $r->fecha_creacion,
                        'usuario' => ['usuario' => $r->usuario_nombre],
                        'objeto' => ['nombre_objeto' => $r->objeto_nombre],
                    ];
                });

                return view($view, compact('fecha', 'modulo', 'items', 'search', 'accion', 'usuario', 'objeto', 'desde', 'hasta', 'sort', 'direction'));
            }
            // Capa dinámica específica para Facturas
            if ($moduloLower === 'facturas') {
                $search = $request->query('search');
                $estadoFactura = $request->query('estado_factura');

                $query = \App\Models\Factura::with(['cliente.empresa', 'cliente.personas', 'estadoFactura', 'cai', 'detalles.servicio']);

                if (!empty($estadoFactura)) {
                    if (is_numeric($estadoFactura)) {
                        $query->where('id_estado_factura_fk', (int) $estadoFactura);
                    } else {
                        $query->whereHas('estadoFactura', function ($q) use ($estadoFactura) {
                            $q->where('nombre_estado', 'like', '%' . $estadoFactura . '%');
                        });
                    }
                }

                if (!empty($search)) {
                    $s = '%' . $search . '%';
                    $query->where(function ($q) use ($s) {
                        $q->where('numero', 'like', $s)
                            ->orWhereHas('cliente', function ($clienteQuery) use ($s) {
                                $clienteQuery->where('nombre', 'like', $s);
                            });
                    });
                }

                $facturas = $query->orderBy('fecha', 'desc')->get();

                // Calcular resúmenes
                $totalFacturas = $facturas->count();
                $pagadas = $facturas->where('estadoFactura.nombre', 'Pagada')->sum('total');
                $pendientes = $facturas->where('estadoFactura.nombre', 'Pendiente')->sum('total');
                $anuladas = $facturas->where('estadoFactura.nombre', 'Anulada')->sum('total');

                return view($view, compact('fecha', 'modulo', 'facturas', 'totalFacturas', 'pagadas', 'pendientes', 'anuladas', 'search', 'estadoFactura'));
            }
            return view($view, compact('fecha', 'modulo'));
        })->name('reportes-header');

        // Vistas PDF o externas
        Route::get('detalle-cotizacion', fn() => view('admin.detalle-cotizacion'))->name('detalle-cotizacion');
        Route::get('detalle-orden', fn() => view('admin.detalle-orden'))->name('detalle-orden');
        // Vista de factura dinámica: recibe el id de factura y delega al controlador para cargar datos
        Route::get('formato-factura/{id}', [\App\Http\Controllers\FacturaController::class, 'formatoFactura'])->name('formato-factura');
        Route::get('reporte-proyecto', function (Request $request) {
            $fecha = $request->query('fecha', now()->format('d-M-Y'));
            $modulo = $request->query('modulo', 'Proyecto BAC');
            return view('admin.reporte-proyecto', compact('fecha', 'modulo'));
        })->name('reporte-proyecto');
        Route::get('formato-reporte', fn() => view('admin.formato-reporte'))->name('formato-reporte');
    });

// Login view (pública)
Route::get('/login', fn() => view('auth.login'))->name('login');

// Redirección rápida para raíz de cliente
Route::redirect('/cliente', '/cliente/perfil');

// Grupo de rutas Cliente (Portal Cliente) - con middleware propio
// Usa autenticación JWT web + refresh + validación de rol cliente
Route::prefix('cliente')
    ->name('cliente.')
    ->middleware(['auth.jwt.web', 'jwt.refresh', 'client.only'])
    ->group(function () {
        // Rutas para configuración inicial del perfil (sin middleware de verificación de perfil)
        Route::get('configurar-perfil', [\App\Http\Controllers\ClienteController::class, 'configurarPerfil'])->name('configurar-perfil');
        Route::post('configurar-perfil', [\App\Http\Controllers\ClienteController::class, 'configurarPerfilStore'])->name('configurar-perfil.store');

        // Rutas para configuración de empresa
        Route::get('configurar-empresa', [\App\Http\Controllers\ClienteController::class, 'configurarEmpresa'])->name('configurar-empresa');
        Route::post('configurar-empresa', [\App\Http\Controllers\ClienteController::class, 'configurarEmpresaStore'])->name('configurar-empresa.store');

        // API routes for location data
        Route::get('api/departamentos/{paisId}', [\App\Http\Controllers\ClienteController::class, 'getDepartamentosByPais'])->name('api.departamentos');
        Route::get('api/ciudades/{departamentoId}', [\App\Http\Controllers\ClienteController::class, 'getCiudadesByDepartamento'])->name('api.ciudades');

        // Rutas que requieren perfil completo
        Route::middleware(['check.cliente.perfil'])->group(function () {
            Route::get('perfil', [\App\Http\Controllers\ClienteController::class, 'perfil'])->name('perfil');
            Route::put('perfil', [\App\Http\Controllers\ClienteController::class, 'perfilUpdate'])->name('perfil.update');
            Route::put('empresa', [\App\Http\Controllers\ClienteController::class, 'empresaUpdate'])->name('empresa.update');
            Route::get('cotizaciones', [\App\Http\Controllers\ClienteController::class, 'cotizaciones'])->name('cotizaciones');
            // API-like para Cotizaciones del cliente (SPA cookie-auth)
            Route::get('cotizaciones-data', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'index'])->name('cotizaciones.data');
            // PDF de cotización (cliente)
            Route::get('cotizaciones/{id}/pdf', [\App\Http\Controllers\Cliente\CotizacionPdfController::class, 'show'])->name('cotizaciones.pdf');
            // Viewer HTML con el mismo diseño que admin (cliente decide imprimir)
            Route::get('detalle-cotizacion', function (Request $request) {
                // Endpoints cliente para alimentar el diseño existente sin cambiarlo
                $base = [
                    'cot' => url('/cliente/cotizaciones/{id}/data'),
                    'items' => url('/cliente/cotizaciones/{id}/items'),
                ];
                return view('admin.detalle-cotizacion', [
                    'COTI_ENDPOINTS' => $base,
                ]);
            })->name('cotizaciones.viewer');

            // Data endpoints para el viewer del cliente
            Route::get('cotizaciones/{id}/data', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'show'])
                ->name('cotizaciones.show');
            Route::get('cotizaciones/{id}/items', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'items'])
                ->name('cotizaciones.items');
            // Cambiar estado desde el cliente (aprobar/rechazar)
            Route::post('cotizaciones/{id}/cambiar-estado', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'updateEstado'])
                ->name('cotizaciones.cambiar-estado');
            Route::get('ordenes', [\App\Http\Controllers\ClienteController::class, 'ordenes'])->name('ordenes');
            // API-like para Órdenes de Servicio del cliente (SPA cookie-auth)
            Route::get('ordenes-data', [\App\Http\Controllers\Cliente\OrdenServicioClienteController::class, 'index'])->name('ordenes.data');
            Route::get('ordenes/{id}/data', [\App\Http\Controllers\Cliente\OrdenServicioClienteController::class, 'show'])->name('ordenes.show');
            // Calificar Orden de Servicio (cliente)
            Route::post('ordenes/{id}/calificar', [\App\Http\Controllers\Cliente\OrdenServicioClienteController::class, 'calificar'])->name('ordenes.calificar');
            // Viewer de Orden para cliente con el mismo diseño que admin
            Route::get('detalle-orden', fn() => view('admin.detalle-orden'))->name('detalle-orden');
            Route::get('facturas', [\App\Http\Controllers\ClienteController::class, 'facturas'])->name('facturas');
            // API-like para Facturas del cliente (SPA cookie-auth)
            Route::get('facturas-data', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'index'])->name('facturas.data');
            Route::get('facturas/{id}/data', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'show'])->name('facturas.show');
            // Viewer HTML reutilizando el formato de Admin (valida pertenencia)
            Route::get('formato-factura/{id}', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'viewer'])->name('facturas.viewer');
            Route::get('solicitudes', [\App\Http\Controllers\ClienteController::class, 'solicitudes'])->name('solicitudes');

            // API-like para Solicitudes del cliente (SPA cookie-auth)
            Route::get('solicitudes-data', [\App\Http\Controllers\Cliente\SolicitudClienteController::class, 'index'])->name('solicitudes.data');
            Route::post('solicitudes', [\App\Http\Controllers\Cliente\SolicitudClienteController::class, 'store'])->name('solicitudes.store');

            // Rutas de 2FA para clientes (mismo patrón que admin)
            Route::get('2fa/status', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'status'])->name('2fa.status');
            Route::post('2fa/setup/start', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'startSetup'])->name('2fa.setup.start');
            Route::post('2fa/setup/confirm', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'confirmSetup'])->name('2fa.setup.confirm');
            Route::post('2fa/disable', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'disable'])->name('2fa.disable');
            Route::post('2fa/recovery-codes', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'recoveryCodes'])->name('2fa.recovery-codes');
        });
    });
