<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockClientFromAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user) {
            $rolNombre = strtolower($user->rol->rol ?? '');
            // Si es cliente y viene a /admin/* lo redirigimos a su portal
            if (in_array($rolNombre, ['cliente','client','usuario','user'])) {
                return redirect()->route('cliente.perfil');
            }
        }
        return $next($request);
    }
}
