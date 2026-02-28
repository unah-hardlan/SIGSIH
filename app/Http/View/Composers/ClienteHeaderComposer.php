<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Persona;

class ClienteHeaderComposer
{
    
    public function compose(View $view)
    {
        $authUser = Auth::user();
        $clienteUsuario = $authUser->usuario ?? 'Usuario';
        $clienteIniciales = strtoupper(substr($clienteUsuario, 0, 2));
        
        
        $clienteAvatar = null;
        if ($authUser) {
            $persona = Persona::where('id_usuario_fk', $authUser->id_usuario_pk)->first();
            $clienteAvatar = $persona->avatar_path ?? null;
        }

        $view->with([
            'authUser' => $authUser,
            'clienteUsuario' => $clienteUsuario,
            'clienteIniciales' => $clienteIniciales,
            'clienteAvatar' => $clienteAvatar,
        ]);
    }
}
