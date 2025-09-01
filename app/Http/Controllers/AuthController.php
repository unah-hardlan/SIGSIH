<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUsuarioRequest;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use function response;


class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        // Normalizar: mayúsculas y trim, rechazar espacios
        $usuario = strtoupper(trim($data['usuario']));
        $password = $data['contrasena'];
        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $password)) {
            return response()->json(['error' => 'Usuario/contraseña inválidos'], 401);
        }
        $result = $this->authService->attempt($usuario, $password);
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['code']);
        }
        $response = response()->json($result, 200);
        $response->cookie('auth_token', $result['token'], 60, '/', null, false, true, false, 'Lax');
        return $response;
    }

    public function logout(): JsonResponse
    {
        return response()->json(['ok' => true])->cookie('auth_token', null, -1, '/', null, false, true, false, 'Lax');
    }

    // Registro que crea un usuario usando las mismas reglas que StoreUsuarioRequest.
    // Si el usuario ya existe responde 409, si se crea devuelve el mismo formato que login.
    public function register(StoreUsuarioRequest $request): JsonResponse
    {
        $data = $request->validated();

        // verificar existencia por usuario o correo
        $exists = Usuario::where('usuario', $data['usuario'])
            ->orWhere('correo_electronico', $data['correo_electronico'])
            ->first();
        if ($exists) {
            return response()->json(['error' => 'El usuario o correo ya existe'], 409);
        }

        // Asignar rol por defecto si no se envía
        if (empty($data['id_rol_fk'])) {
            $rolPk = Rol::where('rol', 'Cliente')->value('id_rol_pk');
            if (!$rolPk) {
                // fallback al primer rol disponible
                $rolPk = Rol::orderBy('id_rol_pk')->value('id_rol_pk');
            }
            if ($rolPk) {
                $data['id_rol_fk'] = $rolPk;
            } else {
                return response()->json([
                    'error' => 'No hay un rol por defecto disponible. Configure al menos un rol.'
                ], 422);
            }
        }

    $usuario = new Usuario();
        $usuario->fill($data);
    // Forzar usuario en mayúsculas al persistir
    $usuario->usuario = strtoupper(trim($usuario->usuario));
        $usuario->contrasena = Hash::make($data['contrasena']);
    // Forzar primer_ingreso = 1 (nuevo usuario debe completar perfil)
    $usuario->primer_ingreso = 1;
        $usuario->save();

        $tokenResult = $this->authService->tokenForUser($usuario);
        if (isset($tokenResult['error'])) {
            return response()->json(['error' => $tokenResult['error']], $tokenResult['code']);
        }

        $response = response()->json($tokenResult, 201);
        $response->cookie('auth_token', $tokenResult['token'], 60, '/', null, false, true, false, 'Lax');
        return $response;
    }
}
