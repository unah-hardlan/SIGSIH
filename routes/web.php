<?php

use App\Http\Controllers\Admin\ViewLoaderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SessionTokenController;
use App\Http\Controllers\ProfileController;
use App\Services\PermissionService;
use App\Support\AdminModuleRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    ->middleware(['auth.jwt.web','jwt.refresh'])
    ->name('logout');

// Intercambia la sesión web autenticada por un JWT para el SPA
Route::get('/session/token', [SessionTokenController::class, 'issue'])
    ->middleware(['auth.jwt.web','jwt.refresh'])
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
    if (in_array($rolNombre, ['cliente','client','usuario','user'])) {
        // Verificar si el cliente necesita configurar su perfil
        $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        
        if (!$persona || 
            empty($persona->primer_nombre) || 
            empty($persona->primer_apellido) || 
            empty($persona->dni) || 
            empty($persona->id_genero_fk)) {
            return redirect()->route('cliente.configurar-perfil');
        }
        
        return redirect()->route('cliente.perfil');
    }
    // Por defecto admin
    return redirect()->route('admin.dashboard');
})->middleware(['auth.jwt.web','jwt.refresh'])->name('home.redirect');

// Ruta explícita reutilizable para redirecciones después de login via frontend
Route::get('/post-auth-redirect', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    $rolNombre = strtolower($user->rol->rol ?? '');
    if (in_array($rolNombre, ['cliente','client','usuario','user'])) {
        // Verificar si el cliente necesita configurar su perfil
        $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        
        if (!$persona || 
            empty($persona->primer_nombre) || 
            empty($persona->primer_apellido) || 
            empty($persona->dni) || 
            empty($persona->id_genero_fk)) {
            return redirect()->route('cliente.configurar-perfil');
        }
        
        return redirect()->route('cliente.perfil');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth.jwt.web','jwt.refresh'])->name('post-auth.redirect');

// API-like fallbacks (cookie-based auth) for SPA when Bearer token is missing/expirado
Route::get('/api-web/dashboard/indicadores', [DashboardController::class, 'indicators'])
    ->middleware(['auth.jwt.web','admin.only'])
    ->name('dashboard.indicators.web');
Route::get('/api-web/dashboard/ordenes-estado', [DashboardController::class, 'ordenesPorEstado'])
    ->middleware(['auth.jwt.web','admin.only'])
    ->name('dashboard.ordenes.estado.web');
Route::get('/api-web/dashboard/cotizaciones-mes', [DashboardController::class, 'cotizacionesPorMes'])
    ->middleware(['auth.jwt.web','admin.only'])
    ->name('dashboard.cotizaciones.mes.web');
Route::get('/api-web/dashboard/proyectos-estado', [DashboardController::class, 'proyectosPorEstado'])
    ->middleware(['auth.jwt.web','admin.only'])
    ->name('dashboard.proyectos.estado.web');

// API-like fallback para cambiar contraseña del perfil (cookie-based auth)
Route::post('/api-web/me/password', [ProfileController::class, 'changePassword'])
    ->middleware(['auth.jwt.web'])
    ->name('perfil.password.web');

// API-like para configuración del sistema (parámetros generales)
Route::get('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'show'])
    ->middleware(['auth.jwt.web','admin.only'])
    ->name('system.settings.show.web');
Route::post('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'update'])
    ->middleware(['auth.jwt.web','admin.only'])
    ->name('system.settings.update.web');

// API-like fallbacks para Reportes (cookie-based auth)
Route::middleware(['auth.jwt.web','admin.only'])->group(function () {
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
                } catch (\Throwable $e) { $nombresEmpresa = collect(); }
                try {
                    $oficinasEmpresa = \App\Models\OficinaEmpresa::select('id_oficina_empresa_pk', 'nombre_oficina')
                        ->orderBy('nombre_oficina')
                        ->get();
                } catch (\Throwable $e) { $oficinasEmpresa = collect(); }

                return view($view, compact('fecha', 'modulo', 'empresas', 'ordenarPor', 'search', 'estadoEmpresa', 'fechaGeneracion', 'nombresEmpresa', 'oficinasEmpresa'));
            }
            return view($view, compact('fecha', 'modulo'));
        })->name('reportes-header');

        // Vistas PDF o externas
        Route::get('detalle-cotizacion', fn() => view('admin.detalle-cotizacion'))->name('detalle-cotizacion');
        Route::get('detalle-orden', fn() => view('admin.detalle-orden'))->name('detalle-orden');
        Route::get('formato-factura', fn() => view('admin.formato-factura'))->name('formato-factura');
        Route::get('proyecto-pdf', fn() => view('admin.proyecto-pdf'))->name('proyecto-pdf');
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
    ->middleware(['auth.jwt.web','jwt.refresh','client.only'])
    ->group(function () {
        // Rutas para configuración inicial del perfil (sin middleware de verificación de perfil)
        Route::get('configurar-perfil', [\App\Http\Controllers\ClienteController::class, 'configurarPerfil'])->name('configurar-perfil');
        Route::post('configurar-perfil', [\App\Http\Controllers\ClienteController::class, 'configurarPerfilStore'])->name('configurar-perfil.store');
        
        // Rutas para configuración de empresa
        Route::get('configurar-empresa', [\App\Http\Controllers\ClienteController::class, 'configurarEmpresa'])->name('configurar-empresa');
        Route::post('configurar-empresa', [\App\Http\Controllers\ClienteController::class, 'configurarEmpresaStore'])->name('configurar-empresa.store');
        
        // Rutas que requieren perfil completo
        Route::middleware(['check.cliente.perfil'])->group(function () {
            Route::get('perfil', [\App\Http\Controllers\ClienteController::class, 'perfil'])->name('perfil');
            Route::put('perfil', [\App\Http\Controllers\ClienteController::class, 'perfilUpdate'])->name('perfil.update');
            Route::put('empresa', [\App\Http\Controllers\ClienteController::class, 'empresaUpdate'])->name('empresa.update');
            Route::get('cotizaciones', [\App\Http\Controllers\ClienteController::class, 'cotizaciones'])->name('cotizaciones');
            Route::get('ordenes', [\App\Http\Controllers\ClienteController::class, 'ordenes'])->name('ordenes');
            Route::get('facturas', [\App\Http\Controllers\ClienteController::class, 'facturas'])->name('facturas');
            Route::get('solicitudes', [\App\Http\Controllers\ClienteController::class, 'solicitudes'])->name('solicitudes');
            
            // Rutas de 2FA para clientes (mismo patrón que admin)
            Route::get('2fa/status', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'status'])->name('2fa.status');
            Route::post('2fa/setup/start', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'startSetup'])->name('2fa.setup.start');
            Route::post('2fa/setup/confirm', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'confirmSetup'])->name('2fa.setup.confirm');
            Route::post('2fa/disable', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'disable'])->name('2fa.disable');
            Route::post('2fa/recovery-codes', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'recoveryCodes'])->name('2fa.recovery-codes');
        });
    });
