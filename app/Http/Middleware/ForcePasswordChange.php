<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        if (!(bool) ($user->primer_ingreso ?? false)) {
            return $next($request);
        }

        if ($this->isAllowedWhileFirstLogin($request)) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Debes cambiar tu contraseña antes de continuar.',
                'force_password_change' => true,
                'status' => 'password_reset_required',
                'reset_url' => route('password.force.redirect'),
            ], 403);
        }

        return redirect()->route('password.force.redirect');
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
