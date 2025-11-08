<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientOnly
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            
            return redirect()->guest('/login');
        }

        
        $rolNombre = $user->rol->rol ?? null; 
        if (!$rolNombre || !in_array(strtolower($rolNombre), ['cliente','client','usuario','user'])) {
            
            abort(403, 'Acceso no autorizado para clientes.');
        }

        return $next($request);
    }
}
