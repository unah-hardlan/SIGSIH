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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\EstadoOrdenServicio;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);
Route::post('email/resend', [AuthController::class, 'resendVerification']);
Route::get('verify-email', [AuthController::class, 'verifyEmail']);
// 2FA verify (public, tied to challenge cookie)
Route::post('2fa/verify', [TwoFactorController::class, 'verifyChallenge']);

// Get de genero para cliente
Route::middleware(['jwt.auth', 'throttle:30,1'])->get('catalogos/generos', function () {
    $items = \App\Models\Genero::select('id_genero_pk as id', 'genero')->orderBy('genero')->get();
    return response()->json([
        'data' => $items,
        'meta' => ['count' => $items->count()]
    ]);
});


// Protegidas con JWT + Auto Permission (Authorization: Bearer <token>)
Route::middleware(['jwt.auth', 'auto.permiso'])->group(function () {
    // 2FA setup (authenticated)
    Route::post('2fa/setup/start', [TwoFactorController::class, 'startSetup']);
    Route::post('2fa/setup/confirm', [TwoFactorController::class, 'confirmSetup']);
    Route::post('2fa/disable', [TwoFactorController::class, 'disable']);
    // Perfil del usuario autenticado
    Route::get('me', [ProfileController::class, 'me']);
    Route::post('perfil/persona', [ProfileController::class, 'savePersona']);
    Route::post('perfil/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('perfil/avatar', [ProfileController::class, 'deleteAvatar']);
    Route::post('perfil/password', [ProfileController::class, 'changePassword']);
    Route::apiResource('usuarios', UsuarioController::class);
    // Multi-rol: sincronización de roles
    Route::put('usuarios/{id}/roles', [UsuarioController::class, 'syncRoles']);
    Route::get('usuarios/{id}/roles', [UsuarioController::class, 'getRoles']);
    Route::apiResource('roles', RolController::class);
    // Upsert permisos por combinación rol-objeto (debe ir antes del apiResource para evitar colisiones)
    Route::put('permisos/roles/{idRol}/objetos/{idObjeto}', [PermisoController::class, 'upsertForRoleObject']);
    Route::apiResource('permisos', PermisoController::class);
    Route::apiResource('bitacoras', BitacoraController::class);
    Route::apiResource('parametros', ParametroController::class);
    Route::apiResource('objetos', ObjetoController::class);
    Route::apiResource('tipos-objeto', TipoObjetoController::class)->only(['index']);

    // MODULO DE PERSONAS (sin tipos-persona ni perfiles)
    // CRUD de géneros solo para admin (cliente usa únicamente GET /api/catalogos/generos)
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
    Route::apiResource('estados-solicitud', EstadoSolicitudController::class);
    Route::apiResource('estados-proyecto', EstadoProyectoController::class);
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
    Route::apiResource('detalles-factura', DetalleFacturaController::class);
    Route::apiResource('servicios', ServicioController::class);
    Route::apiResource('detalles-orden-producto', DetalleOrdenProductoController::class);

    // Catálogo: Estados de Orden de Servicio
    Route::get('estados-orden-servicio', function () {
        $items = EstadoOrdenServicio::select(
            'id_estado_orden_servicio_pk as id',
            'nombre',
            'codigo'
        )
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
        return response()->json([
            'data' => $items,
            'meta' => ['count' => $items->count()],
        ]);
    });

    // Rol único del usuario (FK directa)
    Route::get('usuarios/{id}/rol', [\App\Http\Controllers\UsuarioController::class, 'rol']);
    Route::put('usuarios/{id}/rol', [\App\Http\Controllers\UsuarioController::class, 'setRol']);

    // Usuarios por rol
    Route::get('roles/{id}/usuarios', [\App\Http\Controllers\RolController::class, 'usuarios']);

    // Dashboard datasets
    Route::get('dashboard/indicadores', [DashboardController::class, 'indicators']);
    Route::get('dashboard/ordenes-estado', [DashboardController::class, 'ordenesPorEstado']);
    Route::get('dashboard/cotizaciones-mes', [DashboardController::class, 'cotizacionesPorMes']);
    Route::get('dashboard/proyectos-estado', [DashboardController::class, 'proyectosPorEstado']);
    // KPIs específicos de proyectos (opcional, por si el front los llama por separado)
    // Mantener sólo si el front los requiere; de lo contrario se usan los de 'indicadores'
});