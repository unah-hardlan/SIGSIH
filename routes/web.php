<?php

use App\Http\Controllers\Admin\ViewLoaderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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

// Redirect root to admin dashboard
Route::redirect('/', '/admin/dashboard');

// API-like fallbacks (cookie-based auth) for SPA when Bearer token is missing/expirado
Route::get('/api-web/dashboard/indicadores', [DashboardController::class, 'indicators'])
    ->middleware(['auth.jwt.web'])
    ->name('dashboard.indicators.web');
Route::get('/api-web/dashboard/ordenes-estado', [DashboardController::class, 'ordenesPorEstado'])
    ->middleware(['auth.jwt.web'])
    ->name('dashboard.ordenes.estado.web');
Route::get('/api-web/dashboard/cotizaciones-mes', [DashboardController::class, 'cotizacionesPorMes'])
    ->middleware(['auth.jwt.web'])
    ->name('dashboard.cotizaciones.mes.web');
Route::get('/api-web/dashboard/proyectos-estado', [DashboardController::class, 'proyectosPorEstado'])
    ->middleware(['auth.jwt.web'])
    ->name('dashboard.proyectos.estado.web');

// API-like fallbacks para Reportes (cookie-based auth)
Route::middleware(['auth.jwt.web'])->group(function () {
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
    ->middleware(['spa.init', 'auth.jwt.web', 'jwt.refresh', 'force.profile'])
    ->group(function () {

        // Dashboard
        Route::get('dashboard', fn() => view('layouts.admin')->with('partialView', 'admin.partials.dashboard'))->name('dashboard');

        // Seguridad
        Route::get('gestion-usuarios', fn() => view('layouts.admin')->with('partialView', 'admin.partials.gestion-usuarios'))->name('gestion-usuarios');
        Route::get('parametros', fn() => view('layouts.admin')->with('partialView', 'admin.partials.parametros'))->name('parametros');
        Route::get('configuracion-acceso', fn() => view('layouts.admin')->with('partialView', 'admin.partials.configuracion-acceso'))->name('configuracion-acceso');

        // Clientes
        Route::get('gestion-empresas', fn() => view('layouts.admin')->with('partialView', 'admin.partials.gestion-empresas'))->name('gestion-empresas');
        Route::get('cotizaciones', fn() => view('layouts.admin')->with('partialView', 'admin.partials.cotizaciones'))->name('cotizaciones');

        // Solicitudes
        Route::get('solicitudes', fn() => view('layouts.admin')->with('partialView', 'admin.partials.solicitudes'))->name('solicitudes');

        // Órdenes de Servicio
        Route::get('gestion-ordenes', fn() => view('layouts.admin')->with('partialView', 'admin.partials.gestion-ordenes'))->name('gestion-ordenes');
        Route::get('calificaciones-servicio', fn() => view('layouts.admin')->with('partialView', 'admin.partials.calificaciones-servicio'))->name('calificaciones-servicio');

        // Proyectos
        Route::get('vista-proyectos', fn() => view('layouts.admin')->with('partialView', 'admin.partials.vista-proyectos'))->name('vista-proyectos');
        Route::get('proyectos', fn() => view('layouts.admin')->with('partialView', 'admin.partials.proyectos'))->name('proyectos');

        // Tickets
        Route::get('tickets', fn() => view('layouts.admin')->with('partialView', 'admin.partials.tickets'))->name('tickets');

        // Agencias y Calendario
        Route::get('agencias', fn() => view('layouts.admin')->with('partialView', 'admin.partials.agencias'))->name('agencias');
        Route::get('calendario', fn() => view('layouts.admin')->with('partialView', 'admin.partials.calendario'))->name('calendario');

        // Facturas y CAI
        Route::get('facturas', fn() => view('layouts.admin')->with('partialView', 'admin.partials.facturas'))->name('facturas');
        Route::get('cai', fn() => view('layouts.admin')->with('partialView', 'admin.partials.cai'))->name('cai');

        // Reportes
        Route::get('reportes', fn() => view('layouts.admin')->with('partialView', 'admin.partials.reportes'))->name('reportes');

        // Reportes Header (con mapeo completo)
        Route::get('reportes-header', function (Request $request) {
            $modulo = $request->query('modulo', '');
            $fecha = $request->query('fecha', now()->format('d-M-Y'));
            $moduloLower = strtolower($modulo);

            if ($moduloLower === 'usuarios') {
                return app(\App\Http\Controllers\UsuarioController::class)->reporte($request);
            }
            if ($moduloLower === 'parametros') {
                return app(\App\Http\Controllers\ParametroController::class)->reporte($request);
            }

            $view = match ($moduloLower) {
                'configuracion de accesos', 'configuracion-acceso' => 'admin.reporte-configuracion-accesos',
                'empresas' => 'admin.reporte-empresas',
                'solicitudes' => 'admin.reporte-solicitudes',
                'tickets' => 'admin.reporte-tickets',
                'agencias' => 'admin.reporte-agencias',
                'calendario' => 'admin.reporte-calendario',
                'facturas' => 'admin.reporte-facturas',
                'cai' => 'admin.reporte-cai',
                'bitacora' => 'admin.reporte-bitacora',
                'gestion de personas' => 'admin.reporte-gestion-personas',
                'productos' => 'admin.reporte-productos',
                'kardex' => 'admin.reporte-kardex',
                'proyectos' => 'admin.reporte-proyectos',
                default => 'admin.reporte-generico',
            };

            return view($view, compact('fecha', 'modulo'));
        })->name('reportes-header');

        // Inventario
        Route::get('productos', fn() => view('layouts.admin')->with('partialView', 'admin.partials.productos'))->name('productos');
        Route::get('kardex', fn() => view('layouts.admin')->with('partialView', 'admin.partials.kardex'))->name('kardex');

        // Catálogo
        $catalogos = [
            'genero',
            'estados-solicitud',
            'categorias-ingresos-gastos',
            'estados-proyecto',
            'estados-tickets',
            'ubicaciones',
            'estados-calendario',
            'admin-facturas',
            'estados-cai',
            'tipo-visita',
            'tipo-persona',
            'perfil',
            'tipo-producto',
            'tipo-movimiento',
            'servicios-realizados',
            'acciones-realizadas',
            'servicios-factura',
            'tipo-objeto'
        ];

        foreach ($catalogos as $cat) {
            Route::get("catalogo-$cat", fn() => view('layouts.admin')->with('partialView', "admin.partials.catalogo-$cat"))->name("catalogo-$cat");
        }

        // Administración
        Route::get('gestion-personas', fn() => view('layouts.admin')->with('partialView', 'admin.partials.gestion-personas'))->name('gestion-personas');
        Route::get('perfil', fn() => view('layouts.admin')->with('partialView', 'admin.partials.perfil'))->name('perfil');
        Route::get('bitacora', fn() => view('layouts.admin')->with('partialView', 'admin.partials.bitacora'))->name('bitacora');
        Route::get('gestion-db', fn() => view('layouts.admin')->with('partialView', 'admin.partials.gestion-db'))->name('gestion-db');

        // Mantenimiento
        Route::get('mantenimiento-general', fn() => view('layouts.admin')->with('partialView', 'admin.partials.mantenimiento-general'))->name('mantenimiento-general');

        // Vistas PDF o externas
        Route::get('detalle-cotizacion', fn() => view('admin.detalle-cotizacion'))->name('detalle-cotizacion');
        Route::get('detalle-orden', fn() => view('admin.detalle-orden'))->name('detalle-orden');
        Route::get('formato-factura', fn() => view('admin.formato-factura'))->name('formato-factura');
        Route::get('proyecto-pdf', fn() => view('admin.proyecto-pdf'))->name('proyecto-pdf');
        Route::get('formato-reporte', fn() => view('admin.formato-reporte'))->name('formato-reporte');
    });

// Login view (pública)
Route::get('/login', fn() => view('auth.login'))->name('login');
