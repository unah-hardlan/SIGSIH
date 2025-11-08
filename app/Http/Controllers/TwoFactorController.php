<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Parametro;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(private AuthService $auth) {}

    
    public function startSetup(Request $request): JsonResponse
    {
        $request->validate(['current_password' => 'required|string']);
        
        $user = $request->user();
        $hash = $user?->contrasena;
        if (!$hash || !Hash::check($request->string('current_password'), $hash)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 403);
        }
        $g2fa = new Google2FA();
        $secret = $g2fa->generateSecretKey();
        $user->two_factor_secret = encrypt($secret);
        $user->save();

        $label = 'SIGSIH:' . ($user->correo_electronico ?: $user->usuario);
        $otpauth = $g2fa->getQRCodeUrl('SIGSIH', $label, $secret);

        return response()->json([
            'otpauth_url' => $otpauth,
        ]);
    }

    
    public function confirmSetup(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'current_password' => 'required|string',
        ]);
        
        $user = $request->user();
        $hash = $user?->contrasena;
        if (!$hash || !Hash::check($request->string('current_password'), $hash)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 403);
        }
        if (!$user->two_factor_secret) {
            return response()->json(['message' => 'No hay secreto 2FA que confirmar'], 400);
        }

        $g2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $g2fa->verifyKey($secret, $request->string('code'), 1);
        if (!$valid) {
            return response()->json(['message' => 'Código inválido'], 422);
        }

        $codes = collect(range(1, 8))->map(fn() => Str::random(10))->implode(',');
        $user->two_factor_recovery_codes = encrypt($codes);
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();

        return response()->json([
            'message' => '2FA activado',
            'recovery_codes' => explode(',', $codes),
        ]);
    }

    
    public function disable(Request $request): JsonResponse
    {
        $request->validate(['current_password' => 'required|string']);
        
        $user = $request->user();
        $hash = $user?->contrasena;
        if (!$hash || !Hash::check($request->string('current_password'), $hash)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 403);
        }
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_enabled = false;
        $user->two_factor_confirmed_at = null;
        $user->save();
        return response()->json(['message' => '2FA desactivado']);
    }

    
    public function verifyChallenge(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        $challengeId = $request->cookie('2fa_challenge');
        if (!$challengeId) {
            return response()->json(['message' => 'Challenge no encontrado'], 401);
        }
    $challengeKey = $this->challengeKey($challengeId);
    $attemptKey = self::attemptKey($challengeId);
    $userId = Cache::get($challengeKey);
        if (!$userId) {
            return response()->json(['message' => 'Challenge expirado o inválido'], 401);
        }
        
        $user = Usuario::find($userId);
        if (!$user || !$user->two_factor_secret) {
            return response()->json(['message' => 'Usuario/2FA inválidos'], 401);
        }
        $maxAttempts = $this->maxTotpAttempts();
        $attempts = Cache::get($attemptKey, 0);

        $code = trim($request->string('code'));
        $g2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);

        $allowTotp = ($maxAttempts <= 0) || ($attempts < $maxAttempts);
        $valid = $allowTotp ? $g2fa->verifyKey($secret, $code, 1) : false;

        $codes = $user->two_factor_recovery_codes ? explode(',', decrypt($user->two_factor_recovery_codes)) : [];
        $isRecovery = in_array($code, $codes, true);

        if (!$valid && !$isRecovery) {
            if ($allowTotp && $maxAttempts > 0) {
                $attempts++;
                Cache::put($attemptKey, $attempts, now()->addMinutes(5));
            }
            $needsRecovery = $maxAttempts > 0 && ($attempts >= $maxAttempts);
            $payload = [
                'message' => $needsRecovery
                    ? 'Demasiados intentos. Usa un código de recuperación para continuar.'
                    : 'Código inválido',
            ];
            if ($needsRecovery) {
                $payload['needs_recovery'] = true;
                $payload['attempts'] = $attempts;
                $payload['max_attempts'] = $maxAttempts;
            }
            return response()->json($payload, 422);
        }

        if ($isRecovery) {
            $codes = array_values(array_diff($codes, [$code]));
            $user->two_factor_recovery_codes = encrypt(implode(',', $codes));
            $user->save();
        }

        
        $tokenResult = $this->auth->tokenForUser($user);
        if (isset($tokenResult['error'])) {
            return response()->json(['error' => $tokenResult['error']], $tokenResult['code']);
        }
        Cache::forget($challengeKey);
        Cache::forget($attemptKey);
        $res = response()->json(['ok' => true]);
        
    $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
    $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
        $res->headers->setCookie(Cookie::forget('2fa_challenge'));
        
        $token = $tokenResult['token'];
        $res->cookie('auth_token', $token, 60, '/', null, $secure, true, false, $sameSite);
        return $res;
    }

    public static function issueChallengeForUser(Usuario $user, Request $request): JsonResponse
    {
        $challengeId = (string) Str::uuid();
    $ttl = now()->addMinutes(5);
    Cache::put(self::challengeKey($challengeId), $user->getKey(), $ttl);
    Cache::put(self::attemptKey($challengeId), 0, $ttl);
    $secure = $request->isSecure() || str_starts_with((string) config('app.url'), 'https://');
    $sameSite = app()->environment('production') ? 'Strict' : 'Lax';
        return response()->json(['status' => '2fa_required'])
            ->cookie('2fa_challenge', $challengeId, 5, '/', null, $secure, true, false, $sameSite);
    }

    private static function challengeKey(string $id): string
    {
        return '2fa:challenge:' . $id;
    }

    private static function attemptKey(string $id): string
    {
        return '2fa:attempts:' . $id;
    }

    private function maxTotpAttempts(): int
    {
        $candidates = [
            'AUTH.2FA.MAX_TOTP_ATTEMPTS',
            'AUTH.2FA.MAX_ATTEMPTS',
            'auth.2fa.max_totp_attempts',
        ];
        foreach ($candidates as $param) {
            $val = Parametro::where('parametro', $param)->value('valor');
            if ($val !== null && $val !== '') {
                if (is_numeric($val)) {
                    $intVal = (int) $val;
                    if ($intVal >= 0) {
                        return $intVal;
                    }
                }
                $filtered = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
                if ($filtered !== '' && is_numeric($filtered)) {
                    $intVal = (int) $filtered;
                    if ($intVal >= 0) {
                        return $intVal;
                    }
                }
            }
        }

        return 5;
    }
}
