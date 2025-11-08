<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    
    public function show(Request $request)
    {
        $appName = optional(Parametro::where('parametro', 'app.name')->first())->valor
            ?? config('app.name', 'SIGSIH');

        $logoParam = optional(Parametro::where('parametro', 'app.logo_path')->first())->valor;
        if ($logoParam) {
            
            if (preg_match('#^(https?://|/)#i', $logoParam)) {
                $logoUrl = $logoParam;
            } else {
                $logoUrl = asset('storage/' . ltrim($logoParam, '/'));
            }
        } else {
            $logoUrl = asset('images/logo.png');
        }

        $logoHeight = (int) (optional(Parametro::where('parametro', 'app.logo_height')->first())->valor ?? 96);

        
        $timezone = optional(Parametro::where('parametro', 'app.timezone')->first())->valor
            ?? config('app.timezone', 'UTC');
        $dateFormat = optional(Parametro::where('parametro', 'app.date_format')->first())->valor
            ?? 'Y-m-d';
        
        $requireVerify = (bool) (
            Parametro::where('parametro', 'AUTH.REQUIERE_VERIFICACION_CORREO')->value('valor')
            ?? Parametro::where('parametro', 'auth.require_email_verification')->value('valor')
            ?? false
        );
        
        $pwdCooldown = (int) (
            Parametro::where('parametro', 'AUTH.PASSWORD_RESET.COOLDOWN_MINUTES')->value('valor')
            ?? Parametro::where('parametro', 'auth.password_reset.cooldown_minutes')->value('valor')
            ?? 5
        );
        $pwdExpire = (int) (
            Parametro::where('parametro', 'AUTH.PASSWORD_RESET.EXPIRE_MINUTES')->value('valor')
            ?? Parametro::where('parametro', 'auth.password_reset.expire_minutes')->value('valor')
            ?? 60
        );
        $pwdMaxPerDay = (int) (
            Parametro::where('parametro', 'AUTH.PASSWORD_RESET.MAX_PER_DAY')->value('valor')
            ?? Parametro::where('parametro', 'auth.password_reset.max_per_day')->value('valor')
            ?? 5
        );
        
        $dniFormat = Parametro::where('parametro', 'FORMATO DNI')->value('valor') ?? '0000-0000-00000';
        
        
        $slLegacy = Parametro::where('parametro', 'AUTH.LIMITE_SESIONES')->value('valor');
        $slDotted = Parametro::where('parametro', 'auth.sessions_limit')->value('valor');
        if (is_numeric($slLegacy)) {
            $sessionsLimit = max(1, (int) $slLegacy);
        } elseif (is_numeric($slDotted)) {
            $sessionsLimit = max(1, (int) $slDotted);
        } else {
            $sessionsLimit = 1;
        }

        
        $adminIntentos = (int) (
            optional(Parametro::where('parametro', 'ADMIN.INTENTOS_INICIO_SESION')->first())->valor
            ?? optional(Parametro::where('parametro', 'ADMIN_INTENTOS_INICIO SESION')->first())->valor
            ?? 3
        );
        $adminCorreo = optional(Parametro::where('parametro', 'ADMIN.CORREO')->first())->valor
            ?? optional(Parametro::where('parametro', 'ADMIN_CORREO')->first())->valor
            ?? '';
        $adminUsuario = optional(Parametro::where('parametro', 'ADMIN.USUARIO')->first())->valor
            ?? optional(Parametro::where('parametro', 'ADMIN_CUSER')->first())->valor
            ?? '';
        $adminPassword = optional(Parametro::where('parametro', 'ADMIN.PASSWORD')->first())->valor
            ?? optional(Parametro::where('parametro', 'ADMIN_CPASS')->first())->valor
            ?? '';

        return response()->json([
            'appName' => $appName,
            'logoUrl' => $logoUrl,
            'logoHeight' => $logoHeight,
            'timezone' => $timezone,
            'dateFormat' => $dateFormat,
            'requireEmailVerification' => $requireVerify,
            'passwordResetCooldown' => $pwdCooldown,
            'passwordResetExpire' => $pwdExpire,
            'passwordResetMaxPerDay' => $pwdMaxPerDay,
            'dniFormat' => $dniFormat,
            'sessionsLimit' => $sessionsLimit,
            'adminIntentos' => $adminIntentos,
            'adminCorreo' => $adminCorreo,
            'adminUsuario' => $adminUsuario,
            'adminPassword' => $adminPassword,
        ]);
    }

    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'max:2048'], 
            'logo_height' => ['nullable', 'integer', 'min:24', 'max:256'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'date_format' => ['nullable', 'string', 'in:d/m/Y,m/d/Y,Y-m-d'],
            'sessions_limit' => ['nullable', 'integer', 'min:1', 'max:10'],
            'require_email_verification' => ['nullable', 'boolean'],
            'password_reset_cooldown' => ['nullable', 'integer', 'min:0', 'max:120'],
            'password_reset_expire' => ['nullable', 'integer', 'min:5', 'max:1440'],
            'password_reset_max_per_day' => ['nullable', 'integer', 'min:1', 'max:20'],
            'dni_format' => ['nullable', 'string', 'max:40'],
            'admin_intentos' => ['nullable', 'integer', 'min:1', 'max:10'],
            'admin_correo' => ['nullable', 'email', 'max:150'],
            'admin_usuario' => ['nullable', 'string', 'max:60'],
            'admin_password' => ['nullable', 'string', 'max:120'],
        ]);

        $user = Auth::user();

        
        if (array_key_exists('app_name', $validated)) {
            $name = $validated['app_name'] ?? null;
            if ($name !== null) {
                $this->persistParametro('app.name', $name, $user);
                Cache::forget('appName');
            }
        }

        
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $oldNew = optional(Parametro::where('parametro', 'app.logo_path')->first())->valor;
            $oldLegacy = optional(Parametro::where('parametro', 'APP.LOGO_RUTA')->first())->valor;
            $old = $oldNew ?: $oldLegacy;
            if ($old && !preg_match('#^(https?://|/)#i', $old)) {
                try {
                    Storage::disk('public')->delete($old);
                } catch (\Throwable $e) {
                }
            }
            $path = $file->store('system', 'public');
            $this->persistParametro('app.logo_path', $path, $user);
            $this->persistParametro('APP.LOGO_RUTA', $path, $user); 
            Cache::forget('appLogoUrl');
            Cache::forget('appName'); 
        }

        
        if (array_key_exists('logo_height', $validated)) {
            $height = (int) $validated['logo_height'];
            if ($height > 0) {
                $this->persistParametro('app.logo_height', $height, $user);
                $this->persistParametro('APP.LOGO_ALTO', $height, $user);
                Cache::forget('appLogoHeight');
            }
        }

        
        if (array_key_exists('timezone', $validated)) {
            $tz = $validated['timezone'];
            if (!empty($tz)) {
                $this->persistParametro('app.timezone', $tz, $user);
                Cache::forget('appTimezone');
                try {
                    config(['app.timezone' => $tz]);
                    date_default_timezone_set($tz);
                } catch (\Throwable $e) {
                    
                }
            }
        }

        
        if (array_key_exists('date_format', $validated)) {
            $df = $validated['date_format'];
            if (!empty($df)) {
                $this->persistParametro('app.date_format', $df, $user);
                Cache::forget('appDateFormat');
            }
        }

        
        if (array_key_exists('require_email_verification', $validated)) {
            $val = (bool) $validated['require_email_verification'];
            $this->persistParametro('AUTH.REQUIERE_VERIFICACION_CORREO', $val ? 1 : 0, $user);
            $this->persistParametro('auth.require_email_verification', $val ? 1 : 0, $user);
        }

        
        if (array_key_exists('password_reset_cooldown', $validated)) {
            $val = (int) $validated['password_reset_cooldown'];
            if ($val >= 0) {
                $this->persistParametro('AUTH.PASSWORD_RESET.COOLDOWN_MINUTES', $val, $user);
                $this->persistParametro('auth.password_reset.cooldown_minutes', $val, $user);
            }
        }
        if (array_key_exists('password_reset_expire', $validated)) {
            $val = (int) $validated['password_reset_expire'];
            if ($val > 0) {
                $this->persistParametro('AUTH.PASSWORD_RESET.EXPIRE_MINUTES', $val, $user);
                $this->persistParametro('auth.password_reset.expire_minutes', $val, $user);
            }
        }
        if (array_key_exists('password_reset_max_per_day', $validated)) {
            $val = (int) $validated['password_reset_max_per_day'];
            if ($val > 0) {
                $this->persistParametro('AUTH.PASSWORD_RESET.MAX_PER_DAY', $val, $user);
                $this->persistParametro('auth.password_reset.max_per_day', $val, $user);
            }
        }

        
        if (array_key_exists('dni_format', $validated)) {
            $val = $validated['dni_format'];
            if ($val !== null) {
                $this->persistParametro('FORMATO DNI', $val, $user);
            }
        }

        
        if (array_key_exists('sessions_limit', $validated)) {
            $sl = (int) $validated['sessions_limit'];
            if ($sl > 0) {
                $this->persistParametro('auth.sessions_limit', $sl, $user);
                
                $this->persistParametro('AUTH.LIMITE_SESIONES', $sl, $user);
                $this->persistParametro('AUTH.LIMITE_SESIONES.ADMIN', $sl, $user);
                $this->persistParametro('AUTH.LIMITE_SESIONES.CLIENTE', $sl, $user);
                Cache::forget('authSessionsLimit');
            }
        }

        
        if (array_key_exists('admin_intentos', $validated)) {
            $val = (int) $validated['admin_intentos'];
            if ($val > 0) {
                $this->upsertParametro('ADMIN.INTENTOS_INICIO_SESION', $val, $user);
            }
        }
        
        if (array_key_exists('admin_correo', $validated)) {
            $val = $validated['admin_correo'];
            if ($val !== null) {
                $this->upsertParametro('ADMIN.CORREO', $val, $user);
            }
        }
        
        if (array_key_exists('admin_usuario', $validated)) {
            $val = $validated['admin_usuario'];
            if ($val !== null) {
                $this->upsertParametro('ADMIN.USUARIO', $val, $user);
            }
        }
        
        if (array_key_exists('admin_password', $validated)) {
            $val = $validated['admin_password'];
            if ($val !== null) {
                $this->upsertParametro('ADMIN.PASSWORD', $val, $user);
            }
        }

        
        return $this->show($request);
    }

    private function upsertParametro(string $clave, $valor, $user): void
    {
        $this->persistParametro($clave, $valor, $user);
    }

    
    private function persistParametro(string $clave, $valor, $user): void
    {
        $param = Parametro::where('parametro', $clave)->first();
        $now = now();
        if (!$param) {
            $param = new Parametro();
            $param->parametro = $clave;
            $param->creado_por = $user?->usuario ?? 'system';
            $param->fecha_creacion = $now;
            $param->id_usuario_fk = $user?->id_usuario_pk; 
        }
        $param->valor = $valor;
        $param->modificado_por = $user?->usuario ?? 'system';
        $param->fecha_modificacion = $now;
        
        if (!$param->id_usuario_fk) {
            $param->id_usuario_fk = $user?->id_usuario_pk;
        }
        $param->save();
    }
}