<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Usuario;

class CheckClientePerfilCompleto
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Verificar si es un usuario cliente
        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar si es cliente
        $rolNombre = strtolower($user->rol->rol ?? '');
        if (!in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {
            return $next($request);
        }

        // Buscar si ya tiene una persona asociada
        $persona = Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
        
        // Si no tiene persona o le faltan datos obligatorios, redirigir a configuración
        if (!$persona || 
            empty($persona->primer_nombre) || 
            empty($persona->primer_apellido) || 
            empty($persona->dni) || 
            empty($persona->id_genero_fk)) {
            
            // No redirigir si ya está en la página de configuración
            if (!$request->routeIs('cliente.configurar-perfil') && 
                !$request->routeIs('cliente.configurar-perfil.store')) {
                return redirect()->route('cliente.configurar-perfil');
            }
        }

        return $next($request);
    }
}