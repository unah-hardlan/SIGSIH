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
use App\Http\Controllers\ObjetoController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Público: login y logout (logout borra cookie aunque el token haya expirado)
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

// Protegidas con JWT (Authorization: Bearer <token>)
Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('roles', RolController::class);
    Route::apiResource('permisos', PermisoController::class);
    Route::apiResource('bitacoras', BitacoraController::class);
    Route::apiResource('parametros', ParametroController::class);
    Route::apiResource('objetos', ObjetoController::class);
  
    // MODULO DE PERSONAS
    Route::apiResource('tipos-persona', \App\Http\Controllers\TipoPersonaController::class);
    Route::apiResource('perfiles', \App\Http\Controllers\PerfilController::class);
        Route::apiResource('generos', \App\Http\Controllers\GeneroController::class);
    Route::apiResource('personas', \App\Http\Controllers\PersonaController::class);
    Route::apiResource('tipos-producto', \App\Http\Controllers\TipoProductoController::class);
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
    Route::apiResource('items-cotizacion', \App\Http\Controllers\ItemCotizacionController::class);
    Route::apiResource('tipos-movimiento', \App\Http\Controllers\TipoMovimientoController::class);
    Route::apiResource('kardex', \App\Http\Controllers\KardexController::class);
    Route::apiResource('contactos', ContactoController::class);
    Route::apiResource('estados-solicitud', EstadoSolicitudController::class);
    Route::apiResource('estados-proyecto', EstadoProyectoController::class);
    Route::apiResource('proyectos', ProyectoController::class);
    Route::apiResource('gastos', GastosController::class);
    Route::apiResource('ingresos', IngresosController::class);
    Route::apiResource('calificacion_servicio', CalificacionServicioController::class);
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
});

