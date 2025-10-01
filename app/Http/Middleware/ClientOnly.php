<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            // Si no hay usuario autenticado -> redirigir a login cliente (o general)
            return redirect()->guest('/login');
        }

        // Ajusta según tu modelo/relación de rol
        $rolNombre = $user->rol->rol ?? null; // asumiendo $user->rol->rol es el nombre del rol
        if (!$rolNombre || !in_array(strtolower($rolNombre), ['cliente','client','usuario','user'])) {
            // Evitar fuga de información: 403
            abort(403, 'Acceso no autorizado para clientes.');
        }

        return $next($request);
    }
}
