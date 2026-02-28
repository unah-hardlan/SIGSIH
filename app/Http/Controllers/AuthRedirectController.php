<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthRedirectController extends Controller
{

    public function home(Request $request)
    {
        return $this->redirectForUser();
    }


    public function postAuth(Request $request)
    {
        return $this->redirectForUser();
    }

    private function redirectForUser()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $rolNombre = strtolower($user->rol->rol ?? '');
        if (in_array($rolNombre, ['cliente', 'client', 'usuario', 'user'])) {
            $persona = \App\Models\Persona::where('id_usuario_fk', $user->id_usuario_pk)->first();
            if (!$persona || empty($persona->primer_nombre) || empty($persona->primer_apellido) || empty($persona->dni) || empty($persona->id_genero_fk)) {
                return redirect()->route('cliente.configurar-perfil');
            }
            return redirect()->route('cliente.perfil');
        }
        return redirect()->route('admin.dashboard');
    }
}
