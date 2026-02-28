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
            
            $uid = \Illuminate\Support\Facades\Auth::id();
            if (!$uid && is_object($user)) {
                
                $uid = $user->id_usuario_pk ?? $user->id ?? null;
                
                if (!$uid && $user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                    $uid = $user->getAuthIdentifier();
                }
            }

            
            $needsProfile = $uid ? !Persona::query()->where('id_usuario_fk', $uid)->exists() : false;

            if ($needsProfile) {
                
                if ($request->is('admin/*') && !$request->is('admin/perfil')) {
                    \session()->flash('must_complete_profile', true);
                    return \redirect()->route('admin.perfil');
                }

                
                if ($request->is('cliente/*') && !$request->is('cliente/perfil')) {
                    \session()->flash('must_complete_profile', true);
                    return \redirect()->route('cliente.perfil');
                }

                
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
