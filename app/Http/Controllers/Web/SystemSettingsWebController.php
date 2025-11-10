<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class SystemSettingsWebController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();
        $perm = app(PermissionService::class);
        if (!$perm->can($user, ['Mantenimiento del Sistema', 'Mantenimiento del sistema', 'Mantenimiento'], 'consultar')) {
            return response()->json(['error' => 'Permiso denegado'], 403);
        }
        return app(\App\Http\Controllers\SystemSettingsController::class)->show($request);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $perm = app(PermissionService::class);
        if (!$perm->can($user, ['Mantenimiento del Sistema', 'Mantenimiento del sistema', 'Mantenimiento'], 'actualizacion')) {
            return response()->json(['error' => 'Permiso denegado'], 403);
        }
        return app(\App\Http\Controllers\SystemSettingsController::class)->update($request);
    }
}
