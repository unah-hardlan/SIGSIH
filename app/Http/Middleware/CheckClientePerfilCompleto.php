<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Usuario;

class CheckClientePerfilCompleto
{
    
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        
        if (!$user) {
            return redirect()->route('login');
        }

        
        $rolNombre = strtolower($user->rol->rol ?? '');
        if (!in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {
            return $next($request);
        }

        
        $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        
        
        if (!$persona || 
            empty($persona->primer_nombre) || 
            empty($persona->primer_apellido) || 
            empty($persona->dni) || 
            empty($persona->id_genero_fk)) {
            
            
            if (!$request->routeIs('cliente.configurar-perfil') && 
                !$request->routeIs('cliente.configurar-perfil.store')) {
                return redirect()->route('cliente.configurar-perfil');
            }
        }

        return $next($request);
    }
}