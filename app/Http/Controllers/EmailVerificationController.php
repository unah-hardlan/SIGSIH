<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    /**
     * Enviar código de verificación al email
     */
    public function enviarCodigo(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $email = strtolower(trim($request->input('email')));
        
        // Generar código de 6 dígitos
        $codigo = rand(100000, 999999);
        
        // Crear clave única para el cache
        $cacheKey = 'email_verification:' . sha1($email);
        
        // Verificar si ya existe un código reciente (cooldown de 2 minutos)
        if (Cache::has($cacheKey . ':sent_at')) {
            $sentAt = Cache::get($cacheKey . ':sent_at');
            $nextAllowed = $sentAt->addMinutes(2);
            
            if (now()->lt($nextAllowed)) {
                $secondsRemaining = now()->diffInSeconds($nextAllowed);
                return response()->json([
                    'success' => false,
                    'message' => "Debes esperar {$secondsRemaining} segundos antes de solicitar otro código.",
                    'retry_after_seconds' => $secondsRemaining
                ], 429);
            }
        }
        
        // Guardar código en cache por 5 minutos
        Cache::put($cacheKey, $codigo, now()->addMinutes(5));
        Cache::put($cacheKey . ':sent_at', now(), now()->addMinutes(5));
        Cache::put($cacheKey . ':attempts', 0, now()->addMinutes(5));
        
        try {
            // Enviar email con el código
            Mail::send('emails.codigo-verificacion', [
                'codigo' => $codigo,
                'email' => $email
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Código de Verificación - SIGSIH');
            });
            
            Log::info("Código de verificación enviado", [
                'email' => $email,
                'codigo' => $codigo // En producción, considera no loggear el código
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Código enviado correctamente. Revisa tu correo electrónico.',
                'expires_in' => 300 // 5 minutos en segundos
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error al enviar código de verificación", [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el código. Por favor, intenta nuevamente.'
            ], 500);
        }
    }

    /**
     * Verificar el código ingresado por el usuario
     */
    public function verificarCodigo(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'codigo' => 'required|digits:6'
        ]);

        $email = strtolower(trim($request->input('email')));
        $codigoIngresado = $request->input('codigo');
        
        $cacheKey = 'email_verification:' . sha1($email);
        
        // Verificar si existe el código en cache
        if (!Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'El código ha expirado o no existe. Solicita uno nuevo.'
            ], 400);
        }
        
        // Obtener número de intentos
        $attempts = Cache::get($cacheKey . ':attempts', 0);
        
        // Máximo 3 intentos
        if ($attempts >= 3) {
            // Limpiar cache
            Cache::forget($cacheKey);
            Cache::forget($cacheKey . ':sent_at');
            Cache::forget($cacheKey . ':attempts');
            
            return response()->json([
                'success' => false,
                'message' => 'Has excedido el número máximo de intentos. Solicita un nuevo código.'
            ], 429);
        }
        
        $codigoAlmacenado = Cache::get($cacheKey);
        
        // Verificar código
        if ($codigoIngresado == $codigoAlmacenado) {
            // Código correcto - limpiar cache y marcar como verificado
            Cache::forget($cacheKey);
            Cache::forget($cacheKey . ':sent_at');
            Cache::forget($cacheKey . ':attempts');
            
            // Marcar email como verificado (válido por 1 hora para el proceso de registro)
            Cache::put('email_verified:' . sha1($email), true, now()->addHour());
            
            Log::info("Email verificado correctamente", ['email' => $email]);
            
            return response()->json([
                'success' => true,
                'message' => 'Email verificado correctamente.',
                'verified' => true
            ]);
        } else {
            // Código incorrecto - incrementar intentos
            $attempts++;
            Cache::put($cacheKey . ':attempts', $attempts, now()->addMinutes(5));
            
            $intentosRestantes = 3 - $attempts;
            
            return response()->json([
                'success' => false,
                'message' => "Código incorrecto. Te quedan {$intentosRestantes} intento(s).",
                'attempts_remaining' => $intentosRestantes
            ], 400);
        }
    }

    /**
     * Verificar si un email ya fue verificado (útil para validación en el backend)
     */
    public function verificarEstado(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $email = strtolower(trim($request->input('email')));
        $cacheKey = 'email_verified:' . sha1($email);
        
        $isVerified = Cache::has($cacheKey);
        
        return response()->json([
            'success' => true,
            'verified' => $isVerified
        ]);
    }
}
