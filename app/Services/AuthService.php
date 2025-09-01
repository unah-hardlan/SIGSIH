<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Parametro;
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
        // Normalizar entrada: trim y mayúsculas para usuario
        $usuario = strtoupper(trim($usuario));
        // Rechazar espacios en usuario/contraseña
        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $contrasena)) {
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }

        $secret = config('jwt.secret');
        if (!$secret) {
            return ['error' => 'JWT_SECRET no está configurado', 'code' => 500];
        }

        // Limitar intentos: leer de parámetros globales y bloquear al superar límite
        $maxIntentos = (int) (\App\Models\Parametro::where('parametro','ADMIN_INTENTOS_INICIO SESION')->value('valor') ?? 3);

        $user = Usuario::where('usuario', $usuario)->first();
        if (!$user) {
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }

        // Campo estado_usuario se usa para bloqueo
        if (strtoupper((string)$user->estado_usuario) === 'BLOQUEADO') {
            return ['error' => 'Usuario bloqueado por intentos inválidos', 'code' => 423];
        }

        // Llevar conteo de intentos temporales en cache por usuario
        $cacheKey = 'login_attempts:' . $user->getKey();
        $attempts = cache()->get($cacheKey, 0);

        $valid = Hash::check($contrasena, $user->contrasena);
        if (!$valid) {
            $attempts++;
            // guardar por 15 minutos
            cache()->put($cacheKey, $attempts, now()->addMinutes(15));
            if ($attempts >= $maxIntentos) {
                $user->estado_usuario = 'BLOQUEADO';
                $user->save();
                return ['error' => 'Usuario bloqueado por múltiples intentos inválidos', 'code' => 423];
            }
            return ['error' => 'Usuario/contraseña inválidos', 'code' => 401];
        }

        // reset intentos al éxito
        cache()->forget($cacheKey);

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
