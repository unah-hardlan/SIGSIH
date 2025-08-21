<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CalificacionServicioController;
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
    Route::apiResource('calificacion_servicio', CalificacionServicioController::class);
});
