<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        // Deshabilitado: ya no forzamos el cambio de contraseña basado en
        // `primer_ingreso`. Mantener este middleware como passthrough para
        // evitar redirecciones inesperadas.
        return $next($request);
    }

    private function isAllowedWhileFirstLogin(Request $request): bool
    {
        $path = trim($request->path(), '/');

        if ($request->is('login') || $request->is('logout') || $request->is('session/token')) {
            return true;
        }

        if ($request->is('password/reset') || $request->is('password/reset/*')) {
            return true;
        }

        if ($request->is('load-view')) {
            return false;
        }

        if ($request->is('api/me') || $request->is('api/perfil/password') || $request->is('api/logout')) {
            return true;
        }

        if ($request->is('api/catalogos/generos')) {
            return true;
        }

        if (preg_match('#^api/parametros(?:/.*)?$#i', $path) === 1 && in_array(strtoupper($request->method()), ['GET', 'HEAD'], true)) {
            return true;
        }

        return false;
    }
}
