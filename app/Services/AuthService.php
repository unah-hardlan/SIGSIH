<?php

namespace App\Services;

use App\Models\Usuario;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;


class AuthService
{
    /**
     * Autentica un usuario y genera un token JWT.
     *
     * @param string $usuario
     * @param string $contrasena
     * @return array{token:string,user:array}|array{error:string,code:int}
     */
    public function attempt(string $usuario, string $contrasena): array
    {
        $secret = config('jwt.secret');
        if (!$secret) {
            return ['error' => 'JWT_SECRET no está configurado', 'code' => 500];
        }

        $user = Usuario::where('usuario', $usuario)->first();
        if (!$user || !Hash::check($contrasena, $user->contrasena)) {
            return ['error' => 'Credenciales inválidas', 'code' => 401];
        }

        $payload = [
            'sub'  => $user->getKey(),
            'name' => $user->nombre_usuario,
            'iat'  => time(),
            'exp'  => time() + 3600,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre_usuario,
                'correo'  => $user->correo_electronico,
            ]
        ];
    }

    /**
     * Genera un token JWT para un usuario existente y devuelve la misma forma que attempt().
     *
     * @param Usuario $user
     * @return array{token:string,user:array}|array{error:string,code:int}
     */
    public function tokenForUser(Usuario $user): array
    {
        $secret = config('jwt.secret');
        if (!$secret) {
            return ['error' => 'JWT_SECRET no está configurado', 'code' => 500];
        }

        $payload = [
            'sub'  => $user->getKey(),
            'name' => $user->nombre_usuario,
            'iat'  => time(),
            'exp'  => time() + 3600,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return [
            'token' => $token,
            'user'  => [
                'id'      => $user->getKey(),
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre_usuario,
                'correo'  => $user->correo_electronico,
            ]
        ];
    }
}
