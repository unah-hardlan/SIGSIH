<?php

use App\Http\Controllers\AgenciasController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\CalificacionServicioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CiudadesController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\DepartamentosController;
use App\Http\Controllers\DireccionesController;
use App\Http\Controllers\EmpresasClienteController;
use App\Http\Controllers\EstadoCalendarioController;
use App\Http\Controllers\EstadoProyectoController;
use App\Http\Controllers\EstadoSolicitudController;
use App\Http\Controllers\EstadoTicketController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\HistorialContrasenasController;
use App\Http\Controllers\IngresosController;
use App\Http\Controllers\NombresEmpresaController;
use App\Http\Controllers\OficinasEmpresaController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\PaisesController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TipoMantenimientoController;
use App\Http\Controllers\EstadoFacturaController;
use App\Http\Controllers\EstadoCaiController;
use App\Http\Controllers\CaiController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\DetalleFacturaController;
use App\Http\Controllers\EstadoCotizacionController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\DetalleOrdenProductoController;
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\TipoObjetoController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\EstadoOrdenServicio;

Route::middleware('auth:sanctum')->get('/user', [ProfileController::class, 'sanctumUser']);

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);
Route::post('email/resend', [AuthController::class, 'resendVerification']);
Route::get('verify-email', [AuthController::class, 'verifyEmail']);
Route::post('2fa/verify', [TwoFactorController::class, 'verifyChallenge']);

Route::post('email-contacto/enviar-codigo', [\App\Http\Controllers\EmailVerificationController::class, 'enviarCodigo']);
Route::post('email-contacto/verificar-codigo', [\App\Http\Controllers\EmailVerificationController::class, 'verificarCodigo']);
Route::post('email-contacto/verificar-estado', [\App\Http\Controllers\EmailVerificationController::class, 'verificarEstado']);

Route::middleware(['jwt.auth', 'throttle:30,1'])->get('catalogos/generos', [\App\Http\Controllers\GeneroController::class, 'catalog']);

Route::middleware(['jwt.auth', 'jwt.refresh', 'auto.permiso'])->group(function () {
    Route::post('2fa/setup/start', [TwoFactorController::class, 'startSetup']);
    Route::post('2fa/setup/confirm', [TwoFactorController::class, 'confirmSetup']);
    Route::post('2fa/disable', [TwoFactorController::class, 'disable']);
    Route::get('me', [ProfileController::class, 'me']);
    Route::post('perfil/persona', [ProfileController::class, 'savePersona']);
    Route::post('perfil/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('perfil/avatar', [ProfileController::class, 'deleteAvatar']);
    Route::post('perfil/password', [ProfileController::class, 'changePassword']);
    Route::apiResource('usuarios', UsuarioController::class);
    Route::put('usuarios/{id}/roles', [UsuarioController::class, 'syncRoles']);
    Route::get('usuarios/{id}/roles', [UsuarioController::class, 'getRoles']);
    Route::apiResource('roles', RolController::class);
    Route::put('permisos/roles/{idRol}/objetos/{idObjeto}', [PermisoController::class, 'upsertForRoleObject']);
    Route::apiResource('permisos', PermisoController::class);
    Route::apiResource('bitacoras', BitacoraController::class);
    Route::apiResource('parametros', ParametroController::class);
    Route::apiResource('objetos', ObjetoController::class);
    Route::apiResource('tipos-objeto', TipoObjetoController::class);

    Route::middleware('block.client')->apiResource('generos', \App\Http\Controllers\GeneroController::class);
    Route::apiResource('personas', \App\Http\Controllers\PersonaController::class);
    Route::apiResource('productos', \App\Http\Controllers\ProductoController::class);
    Route::apiResource('agencias', AgenciasController::class);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('nombres-empresa', NombresEmpresaController::class);
    Route::apiResource('oficinas-empresa', OficinasEmpresaController::class);
    Route::apiResource('empresas-cliente', EmpresasClienteController::class);
    Route::apiResource('paises', PaisesController::class);
    Route::apiResource('departamentos', DepartamentosController::class);
    Route::apiResource('ciudades', CiudadesController::class);
    Route::apiResource('direcciones', DireccionesController::class);
    Route::apiResource('origenes', \App\Http\Controllers\OrigenController::class);
    Route::apiResource('cotizaciones', \App\Http\Controllers\CotizacionController::class);
    Route::apiResource('acciones-realizadas', \App\Http\Controllers\AccionRealizadaController::class);
    Route::apiResource('items-cotizacion', \App\Http\Controllers\ItemCotizacionController::class);
    Route::apiResource('tipos-movimiento', \App\Http\Controllers\TipoMovimientoController::class);
    Route::apiResource('kardex', \App\Http\Controllers\KardexController::class);
    Route::apiResource('servicios-realizados', \App\Http\Controllers\ServicioRealizadoController::class);
    Route::apiResource('calificacion_servicio', CalificacionServicioController::class);
    Route::apiResource('tipos-visita', \App\Http\Controllers\TipoVisitaController::class);
    Route::apiResource('tipos-producto', \App\Http\Controllers\TipoProductoController::class);
    Route::apiResource('reportes-visita', \App\Http\Controllers\ReporteVisitaController::class);

    Route::apiResource('contactos', ContactoController::class);
    Route::apiResource('estados-solicitud', EstadoSolicitudController::class)
        ->parameters(['estados-solicitud' => 'estadoSolicitud']);
    Route::apiResource('estados-proyecto', EstadoProyectoController::class)
        ->parameters(['estados-proyecto' => 'estadoProyecto']);
    Route::apiResource('proyectos', ProyectoController::class);
    Route::apiResource('gastos', GastosController::class);
    Route::apiResource('ingresos', IngresosController::class);
    Route::apiResource('solicitudes', SolicitudController::class);
    Route::apiResource('ordenes-servicio', OrdenServicioController::class);
    Route::apiResource('historial-contrasenas', HistorialContrasenasController::class);
    Route::apiResource('tipos-mantenimiento', TipoMantenimientoController::class);
    Route::apiResource('estados-ticket', EstadoTicketController::class);
    Route::apiResource('estados-calendario', EstadoCalendarioController::class);
    Route::apiResource('tickets', TicketController::class);
    Route::apiResource('calendario', CalendarioController::class);
    Route::apiResource('estados-factura', EstadoFacturaController::class);
    Route::apiResource('estados-cai', EstadoCaiController::class);
    Route::apiResource('cai', CaiController::class);
    Route::apiResource('facturas', FacturaController::class);
    Route::get('facturas-clientes', [FacturaController::class, 'getClientes']);
    Route::apiResource('detalles-factura', DetalleFacturaController::class);
    Route::apiResource('servicios', ServicioController::class);
    Route::apiResource('detalles-orden-producto', DetalleOrdenProductoController::class);

    Route::get('estados-cotizacion', [EstadoCotizacionController::class, 'index'])
        ->withoutMiddleware('auto.permiso');

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);

    Route::post('db/backup', [\App\Http\Controllers\GestionDbController::class, 'backup']);
    Route::get('db/backup/download', [\App\Http\Controllers\GestionDbController::class, 'download'])->name('db.backup.download');

    Route::get('clientes', [\App\Http\Controllers\ClienteCatalogController::class, 'index'])
        ->withoutMiddleware('auto.permiso');

    Route::get('estados-orden-servicio', [OrdenServicioController::class, 'estadosCatalog']);

    Route::get('usuarios/{id}/rol', [\App\Http\Controllers\UsuarioController::class, 'rol']);
    Route::put('usuarios/{id}/rol', [\App\Http\Controllers\UsuarioController::class, 'setRol']);

    Route::get('roles/{id}/usuarios', [\App\Http\Controllers\RolController::class, 'usuarios']);

    Route::get('tecnicos', [UsuarioController::class, 'tecnicosCatalog'])->withoutMiddleware('auto.permiso');

    Route::get('dashboard/indicadores', [DashboardController::class, 'indicators']);
    Route::get('dashboard/ordenes-estado', [DashboardController::class, 'ordenesPorEstado']);
    Route::get('dashboard/cotizaciones-mes', [DashboardController::class, 'cotizacionesPorMes']);
    Route::get('dashboard/proyectos-estado', [DashboardController::class, 'proyectosPorEstado']);
    Route::get('dashboard/actividades-recientes', [DashboardController::class, 'actividadesRecientes']);
});
