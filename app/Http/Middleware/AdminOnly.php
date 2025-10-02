<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }
        $rol = strtolower($user->rol->rol ?? '');
        // Lista de roles considerados administrador (ajustable)
        $adminRoles = ['admin','administrador','superadmin','soporte','manager'];
        if (!in_array($rol, $adminRoles, true)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        return $next($request);
    }
}
