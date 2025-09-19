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
use App\Services\BitacoraService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;


class AuthController extends Controller
{
    public function __construct(private AuthService $authService, private BitacoraService $bitacora) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        // Normalizar: mayúsculas y trim, rechazar espacios
        $usuario = strtoupper(trim($data['usuario']));
        $password = $data['contrasena'];
        if (preg_match('/\s/', $usuario) || preg_match('/\s/', $password)) {
            return response()->json(['error' => 'Usuario/contraseña inválidos'], 401);
        }
        // Primero solo verifica credenciales; decide 2FA sin emitir token aún
        $cred = $this->authService->verifyCredentialsOnly($usuario, $password);
        if (isset($cred['error'])) {
            return response()->json(['error' => $cred['error']], $cred['code']);
        }
        /** @var \App\Models\Usuario $user */
        $user = $cred['user'];

        // Si 2FA está habilitado, emite challenge y no setea auth_token todavía
    if ($user->two_factor_enabled) {
            $challengeId = (string) \Illuminate\Support\Str::uuid();
            Cache::put('2fa:challenge:' . $challengeId, $user->getKey(), now()->addMinutes(5));
            try { $this->bitacora->logFor('Login', '2FA Challenge', 'Inicio de 2FA', $user->getKey()); } catch (\Throwable $e) {}
            $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
            $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
            return response()->json(['status' => '2fa_required'])
                ->cookie('2fa_challenge', $challengeId, 5, '/', null, $secure, true, false, $sameSite);
        }

        // Si no hay 2FA, emitir token final como antes
        $result = $this->authService->attempt($usuario, $password);
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['code']);
        }
        // Registrar en bitácora
        try {
            $this->bitacora->logFor('Login', 'Login', 'Inicio de sesión', $result['user']['id'] ?? null);
        } catch (\Throwable $e) {
        }
        // Mantener token sólo para cookie, no exponerlo al frontend
    $token = $result['token'] ?? null;
        $payload = $result;
        unset($payload['token']);
        $response = response()->json($payload, 200);
        if ($token) {
            $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
            $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
            $response->cookie('auth_token', $token, 60, '/', null, $secure, true, false, $sameSite);
        }
        return $response;
    }

    public function logout(): JsonResponse
    {
        // Intentar identificar usuario desde Authorization Bearer para loguear correctamente y liberar slot de sesión.
        $userId = null;
        $tokenId = null;
        try {
            $auth = request()->header('Authorization');
            if ($auth && str_starts_with($auth, 'Bearer ')) {
                $token = substr($auth, 7);
                $payload = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));
                $userId = (int) ($payload->sub ?? null);
                $tokenId = substr(hash('sha256', $token), 0, 32);
            }
        } catch (\Throwable $e) {
        }
        // Remover token actual del registro de sesiones concurrentes
        try {
            if ($userId && $tokenId) {
                $sessionsKey = 'user_sessions:' . $userId;
                $sessions = cache()->get($sessionsKey, []);
                if (is_array($sessions) && isset($sessions[$tokenId])) {
                    unset($sessions[$tokenId]);
                    cache()->put($sessionsKey, $sessions, now()->addHours(1));
                }
            }
        } catch (\Throwable $e) {
        }
        try {
            $this->bitacora->logFor('Login', 'Logout', 'Cierre de sesión', $userId);
        } catch (\Throwable $e) {
        }
        $req = request();
        $secure = ($req && $req->isSecure()) || str_starts_with((string) config('app.url'), 'https://');
        $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
        return response()->json(['ok' => true])->cookie('auth_token', null, -1, '/', null, $secure, true, false, $sameSite);
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

        // Asignar SIEMPRE rol Cliente (ignorando lo enviado) para registro público
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
        $token = $tokenResult['token'] ?? null;
        $payload = $tokenResult;
        unset($payload['token']);
        $response = response()->json($payload, 201);
        if ($token) {
            $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
            $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
            $response->cookie('auth_token', $token, 60, '/', null, $secure, true, false, $sameSite);
        }
        return $response;
    }

    public function showPasswordRecoverForm()
    {
        return view('auth.password-recover');
    }

    public function sendPasswordResetEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        // Buscar usuario por email
        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            return response()->json([
                'message' => 'Si existe una cuenta con ese correo, se han enviado las instrucciones de recuperación.'
            ], 200);
        }

        // Aquí puedes implementar el envío del email
        // Por ahora, solo simulamos el éxito
        try {
            // TODO: Implementar envío de email con token de recuperación
            // Mail::send(...);

            // Registrar en bitácora
            $this->bitacora->logFor('Password Reset', 'Solicitud', 'Solicitud de recuperación de contraseña', $usuario->id);

            return response()->json([
                'message' => 'Se han enviado las instrucciones de recuperación a tu correo electrónico.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al enviar las instrucciones. Inténtalo de nuevo más tarde.'
            ], 500);
        }
    }
}
