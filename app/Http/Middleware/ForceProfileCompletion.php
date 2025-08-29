<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Persona;

class ForceProfileCompletion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user) {
            // Necesita completar perfil si aún no existe Persona asociada
            $needsProfile = !Persona::query()->where('id_usuario_fk', $user->getKey())->exists();

            if ($needsProfile) {
                // 1) Bloquear cualquier ruta admin/* que no sea el perfil con redirección dura
                if ($request->is('admin/*') && !$request->is('admin/perfil')) {
                    \session()->flash('must_complete_profile', true);
                    return \redirect()->route('admin.perfil');
                }

                // 2) Bloquear carga de vistas parciales del SPA (load-view) que no sean 'perfil'
                if ($request->is('load-view')) {
                    $view = (string) $request->query('view', '');
                    if ($view !== 'perfil') {
                        return \response('Debe completar su perfil antes de continuar.', 403);
                    }
                }
            }
        }
        return $next($request);
    }
}
