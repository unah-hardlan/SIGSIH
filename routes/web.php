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
Route::get('/verify-email', [AuthController::class, 'verifyEmailPage']);

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
Route::get('/', [AuthController::class, 'home'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('home.redirect');

// Ruta explícita reutilizable para redirecciones después de login via frontend
Route::get('/post-auth-redirect', [AuthController::class, 'postAuth'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('post-auth.redirect');

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

// Catálogo de Estados de Solicitud (cookie-auth para SPA). Permitir a quien tenga "Leer" en Solicitudes
Route::get('/api-web/estados-solicitud', [\App\Http\Controllers\EstadoSolicitudController::class, 'webIndex'])
    ->middleware(['auth.jwt.web'])
    ->name('api.web.estados.solicitud');

// API-like fallback para cambiar contraseña del perfil (cookie-based auth)
Route::post('/api-web/me/password', [ProfileController::class, 'changePassword'])
    ->middleware(['auth.jwt.web'])
    ->name('perfil.password.web');

// API-like para configuración del sistema (parámetros generales)
// Permitir mediante permisos de "Mantenimiento del sistema"
Route::get('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'show'])
    ->middleware(['auth.jwt.web', 'permiso:Mantenimiento del Sistema,consultar'])
    ->name('system.settings.show.web');
Route::post('/api-web/system-settings', [\App\Http\Controllers\SystemSettingsController::class, 'update'])
    ->middleware(['auth.jwt.web', 'permiso:Mantenimiento del Sistema,actualizacion'])
    ->name('system.settings.update.web');

// Notificaciones (cookie-auth para admin y cliente)
Route::get('/api/notificaciones', [\App\Http\Controllers\NotificationController::class, 'webIndex'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('api.notificaciones');

// Marcar todas como leídas (cookie-auth) para Admin y Cliente
Route::post('/api/notificaciones/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'webMarkAllRead'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('api.notificaciones.markAll');

// Marcar una como leída (cookie-auth)
Route::post('/api/notificaciones/{id}/read', [\App\Http\Controllers\NotificationController::class, 'webMarkRead'])
    ->middleware(['auth.jwt.web', 'jwt.refresh'])
    ->name('api.notificaciones.read');

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
        // Ruta base /admin -> redirige al dashboard (sin closures en rutas)
        Route::get('/', [ViewLoaderController::class, 'root'])->name('root');

        // Dashboard
        Route::get('dashboard', [ViewLoaderController::class, 'showLayout'])->name('dashboard');

        // Vistas parciales administradas por registro
        foreach (AdminModuleRegistry::views() as $viewKey => $definition) {
            if ($viewKey === 'dashboard') {
                continue;
            }
            if (($definition['type'] ?? 'partial') !== 'partial') {
                continue;
            }
            Route::get($viewKey, [ViewLoaderController::class, 'showLayout'])->name($viewKey);
        }

        // Reportes Header (consolidado en ProyectoController)
        Route::get('reportes-header', [\App\Http\Controllers\ProyectoController::class, 'reportesHeader'])->name('reportes-header');

        // Vistas PDF o externas
        Route::get('detalle-cotizacion', [\App\Http\Controllers\CotizacionController::class, 'viewer'])->name('detalle-cotizacion');
        Route::get('detalle-orden/{id?}', [\App\Http\Controllers\OrdenServicioController::class, 'detalleOrden'])->name('detalle-orden');
        // Vista de factura dinámica: recibe el id de factura y delega al controlador para cargar datos
        Route::get('formato-factura/{id}', [\App\Http\Controllers\FacturaController::class, 'formatoFactura'])->name('formato-factura');
        Route::get('reporte-proyecto', [\App\Http\Controllers\ProyectoController::class, 'reporteBasico'])->name('reporte-proyecto');
        Route::get('formato-reporte', [\App\Http\Controllers\ProyectoController::class, 'formatoReporte'])->name('formato-reporte');
    });

// Login view (pública)
Route::get('/login', [AuthController::class, 'showLoginView'])->name('login');

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

        // API route para validar DNI
        Route::post('api/validar-dni', [\App\Http\Controllers\ClienteController::class, 'validarDni'])->name('api.validar-dni');

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
            Route::get('detalle-cotizacion', [\App\Http\Controllers\ClienteController::class, 'cotizacionViewer'])->name('cotizaciones.viewer');

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
            Route::get('detalle-orden/{id?}', [\App\Http\Controllers\OrdenServicioController::class, 'detalleOrden'])->name('detalle-orden');
            Route::get('facturas', [\App\Http\Controllers\ClienteController::class, 'facturas'])->name('facturas');
            // API-like para Facturas del cliente (SPA cookie-auth)
            Route::get('facturas-data', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'index'])->name('facturas.data');
            Route::get('facturas/{id}/data', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'show'])->name('facturas.show');
            // Viewer HTML reutilizando el formato de Admin (valida pertenencia)
            Route::get('formato-factura/{id}', [\App\Http\Controllers\Cliente\FacturaClienteController::class, 'viewer'])->name('facturas.viewer');
            Route::get('solicitudes', [\App\Http\Controllers\ClienteController::class, 'solicitudes'])->name('solicitudes');
            // Tickets (Portal Cliente)
            Route::get('tickets', [\App\Http\Controllers\ClienteController::class, 'tickets'])->name('tickets');
            Route::get('tickets-data', [\App\Http\Controllers\Cliente\TicketClienteController::class, 'index'])->name('tickets.data');

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