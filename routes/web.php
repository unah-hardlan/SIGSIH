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
use Illuminate\Support\Facades\DB;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth-login');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth-register');
Route::get('/verificar-correo', [AuthController::class, 'verifyEmailPage'])->name('verify.email.page');
Route::get('/verify-email', [AuthController::class, 'verifyEmailPage']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('logout');

Route::get('/session/token', [SessionTokenController::class, 'issue'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('session.token');

Route::get('/password/reset', [AuthController::class, 'showPasswordRecoverForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendPasswordResetEmail'])
    ->middleware('throttle:auth-password-recovery')
    ->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset.form');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/', [AuthController::class, 'home'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('home.redirect');

Route::get('/post-auth-redirect', [AuthController::class, 'postAuth'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('post-auth.redirect');

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

Route::get('/api-web/estados-solicitud', [\App\Http\Controllers\EstadoSolicitudController::class, 'webIndex'])
    ->middleware(['auth.jwt.web'])
    ->name('api.web.estados.solicitud');

Route::post('/api-web/me/password', [ProfileController::class, 'changePassword'])
    ->middleware(['auth.jwt.web'])
    ->name('perfil.password.web');

Route::get('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'show'])
    ->middleware(['auth.jwt.web', 'permiso:Mantenimiento del Sistema,consultar'])
    ->name('system.settings.show.web');
Route::post('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'update'])
    ->middleware(['auth.jwt.web', 'permiso:Mantenimiento del Sistema,actualizacion'])
    ->name('system.settings.update.web');

Route::get('/api/notificaciones', [\App\Http\Controllers\NotificationController::class, 'webIndex'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('api.notificaciones');

Route::post('/api/notificaciones/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'webMarkAllRead'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('api.notificaciones.markAll');

Route::post('/api/notificaciones/{id}/read', [\App\Http\Controllers\NotificationController::class, 'webMarkRead'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('api.notificaciones.read');

Route::middleware(['auth.jwt.web', 'admin.only'])->group(function () {
    Route::get('/api-web/reportes-visita', [\App\Http\Controllers\ReporteVisitaController::class, 'index']);
    Route::get('/api-web/reportes-visita/{id}', [\App\Http\Controllers\ReporteVisitaController::class, 'show']);
    Route::post('/api-web/reportes-visita', [\App\Http\Controllers\ReporteVisitaController::class, 'store']);
    Route::match(['put', 'patch'], '/api-web/reportes-visita/{id}', [\App\Http\Controllers\ReporteVisitaController::class, 'update']);
    Route::delete('/api-web/reportes-visita/{id}', [\App\Http\Controllers\ReporteVisitaController::class, 'destroy']);

    Route::get('/api-web/tipos-visita', [\App\Http\Controllers\TipoVisitaController::class, 'index']);
    Route::get('/api-web/servicios-realizados', [\App\Http\Controllers\ServicioRealizadoController::class, 'index']);
    Route::get('/api-web/acciones-realizadas', [\App\Http\Controllers\AccionRealizadaController::class, 'index']);
    Route::get('/api-web/ordenes-servicio', [\App\Http\Controllers\OrdenServicioController::class, 'index']);
});

Route::get('/load-view', [ViewLoaderController::class, 'load'])
    ->name('load-view')
    ->middleware(['auth.jwt.web', 'force.profile']);

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['spa.init', 'auth.jwt.web', 'jwt.refresh', 'force.profile', 'block.client'])
    ->group(function () {
        Route::get('/', [ViewLoaderController::class, 'root'])->name('root');

        Route::get('dashboard', [ViewLoaderController::class, 'showLayout'])->name('dashboard');

        foreach (AdminModuleRegistry::views() as $viewKey => $definition) {
            if ($viewKey === 'dashboard') {
                continue;
            }
            if (($definition['type'] ?? 'partial') !== 'partial') {
                continue;
            }
            Route::get($viewKey, [ViewLoaderController::class, 'showLayout'])->name($viewKey);
        }

        Route::get('reportes-header', [\App\Http\Controllers\ProyectoController::class, 'reportesHeader'])->name('reportes-header');

        Route::get('detalle-cotizacion', [\App\Http\Controllers\CotizacionController::class, 'viewer'])->name('detalle-cotizacion');
        Route::get('detalle-orden/{id?}', [\App\Http\Controllers\OrdenServicioController::class, 'detalleOrden'])->name('detalle-orden');
        Route::get('formato-factura/{id}', [\App\Http\Controllers\FacturaController::class, 'formatoFactura'])->name('formato-factura');
        Route::get('reporte-proyecto', [\App\Http\Controllers\ProyectoController::class, 'reporteBasico'])->name('reporte-proyecto');
        Route::get('formato-reporte', [\App\Http\Controllers\ProyectoController::class, 'formatoReporte'])->name('formato-reporte');
    });

Route::get('/login', [AuthController::class, 'showLoginView'])->name('login');

Route::redirect('/cliente', '/cliente/perfil');

Route::prefix('cliente')
    ->name('cliente.')
    ->middleware(['auth.jwt.web', 'jwt.refresh', 'client.only'])
    ->group(function () {
        Route::get('configurar-perfil', [\App\Http\Controllers\ClienteController::class, 'configurarPerfil'])->name('configurar-perfil');
        Route::post('configurar-perfil', [\App\Http\Controllers\ClienteController::class, 'configurarPerfilStore'])->name('configurar-perfil.store');

        Route::get('configurar-empresa', [\App\Http\Controllers\ClienteController::class, 'configurarEmpresa'])->name('configurar-empresa');
        Route::post('configurar-empresa', [\App\Http\Controllers\ClienteController::class, 'configurarEmpresaStore'])->name('configurar-empresa.store');

        Route::get('api/departamentos/{paisId}', [\App\Http\Controllers\ClienteController::class, 'getDepartamentosByPais'])->name('api.departamentos');
        Route::get('api/ciudades/{departamentoId}', [\App\Http\Controllers\ClienteController::class, 'getCiudadesByDepartamento'])->name('api.ciudades');

        Route::post('api/validar-dni', [\App\Http\Controllers\ClienteController::class, 'validarDni'])->name('api.validar-dni');

        Route::middleware(['check.cliente.perfil'])->group(function () {
            Route::get('perfil', [\App\Http\Controllers\ClienteController::class, 'perfil'])->name('perfil');
            Route::put('perfil', [\App\Http\Controllers\ClienteController::class, 'perfilUpdate'])->name('perfil.update');
            Route::put('empresa', [\App\Http\Controllers\ClienteController::class, 'empresaUpdate'])->name('empresa.update');
            Route::get('cotizaciones', [\App\Http\Controllers\ClienteController::class, 'cotizaciones'])->name('cotizaciones');
            Route::get('cotizaciones-data', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'index'])->name('cotizaciones.data');
            Route::get('cotizaciones/{id}/pdf', [\App\Http\Controllers\Cliente\CotizacionPdfController::class, 'show'])->name('cotizaciones.pdf');
            Route::get('detalle-cotizacion', [\App\Http\Controllers\ClienteController::class, 'cotizacionViewer'])->name('cotizaciones.viewer');

            Route::get('cotizaciones/{id}/data', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'show'])
                ->name('cotizaciones.show');
            Route::get('cotizaciones/{id}/items', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'items'])
                ->name('cotizaciones.items');
            Route::post('cotizaciones/{id}/cambiar-estado', [\App\Http\Controllers\Cliente\CotizacionClienteController::class, 'updateEstado'])
                ->name('cotizaciones.cambiar-estado');
            Route::get('ordenes', [\App\Http\Controllers\ClienteController::class, 'ordenes'])->name('ordenes');
            Route::get('ordenes-data', [\App\Http\Controllers\Cliente\OrdenServicioClienteController::class, 'index'])->name('ordenes.data');
            Route::get('ordenes/{id}/data', [\App\Http\Controllers\Cliente\OrdenServicioClienteController::class, 'show'])->name('ordenes.show');
            Route::post('ordenes/{id}/calificar', [\App\Http\Controllers\Cliente\OrdenServicioClienteController::class, 'calificar'])->name('ordenes.calificar');
            Route::get('detalle-orden/{id?}', [\App\Http\Controllers\OrdenServicioController::class, 'detalleOrden'])->name('detalle-orden');
            Route::get('facturas', [\App\Http\Controllers\ClienteController::class, 'facturas'])->name('facturas');
            Route::get('facturas-data', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'index'])->name('facturas.data');
            Route::get('facturas/{id}/data', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'show'])->name('facturas.show');
            Route::get('formato-factura/{id}', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'viewer'])->name('facturas.viewer');
            Route::get('solicitudes', [\App\Http\Controllers\ClienteController::class, 'solicitudes'])->name('solicitudes');
            Route::get('tickets', [\App\Http\Controllers\ClienteController::class, 'tickets'])->name('tickets');
            Route::get('tickets-data', [\App\Http\Controllers\Cliente\TicketClienteController::class, 'index'])->name('tickets.data');

            Route::get('solicitudes-data', [\App\Http\Controllers\Cliente\SolicitudClienteController::class, 'index'])->name('solicitudes.data');
            Route::post('solicitudes', [\App\Http\Controllers\Cliente\SolicitudClienteController::class, 'store'])->name('solicitudes.store');

            Route::get('2fa/status', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'status'])->name('2fa.status');
            Route::post('2fa/setup/start', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'startSetup'])->name('2fa.setup.start');
            Route::post('2fa/setup/confirm', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'confirmSetup'])->name('2fa.setup.confirm');
            Route::post('2fa/disable', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'disable'])->name('2fa.disable');
            Route::post('2fa/recovery-codes', [\App\Http\Controllers\Cliente\TwoFactorController::class, 'recoveryCodes'])->name('2fa.recovery-codes');
        });
    });
