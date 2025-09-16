<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    /**
     * Return current system settings (name and logo url)
     */
    public function show(Request $request)
    {
        $appName = optional(Parametro::where('parametro', 'app.name')->first())->valor
            ?? config('app.name', 'SIGSIH');

        $logoParam = optional(Parametro::where('parametro', 'app.logo_path')->first())->valor;
        if ($logoParam) {
            // If it's an absolute URL or absolute path, use as-is; otherwise, assume storage path
            if (preg_match('#^(https?://|/)#i', $logoParam)) {
                $logoUrl = $logoParam;
            } else {
                $logoUrl = asset('storage/' . ltrim($logoParam, '/'));
            }
        } else {
            $logoUrl = asset('images/logo.png');
        }

        $logoHeight = (int) (optional(Parametro::where('parametro', 'app.logo_height')->first())->valor ?? 96);

        // General parameters
        $timezone = optional(Parametro::where('parametro', 'app.timezone')->first())->valor
            ?? config('app.timezone', 'UTC');
        $dateFormat = optional(Parametro::where('parametro', 'app.date_format')->first())->valor
            ?? 'Y-m-d';
        $sessionsLimit = (int) (optional(Parametro::where('parametro', 'auth.sessions_limit')->first())->valor ?? 1);

        // Admin parameters (fallback chain: standard dotted -> legacy)
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
            'sessionsLimit' => $sessionsLimit,
            'adminIntentos' => $adminIntentos,
            'adminCorreo' => $adminCorreo,
            'adminUsuario' => $adminUsuario,
            'adminPassword' => $adminPassword,
        ]);
    }

    /**
     * Update system settings. Accepts multipart/form-data with optional fields:
     * - app_name (string)
     * - logo (image file)
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['nullable', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB
            'logo_height' => ['nullable', 'integer', 'min:24', 'max:256'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'date_format' => ['nullable', 'string', 'in:d/m/Y,m/d/Y,Y-m-d'],
            'sessions_limit' => ['nullable', 'integer', 'min:1', 'max:10'],
            'admin_intentos' => ['nullable', 'integer', 'min:1', 'max:10'],
            'admin_correo' => ['nullable', 'email', 'max:150'],
            'admin_usuario' => ['nullable', 'string', 'max:60'],
            'admin_password' => ['nullable', 'string', 'max:120'],
        ]);

        $user = Auth::user();

        // Update name
        if (array_key_exists('app_name', $validated)) {
            $name = $validated['app_name'] ?? null;
            if ($name !== null) {
                $this->persistParametro('app.name', $name, $user);
                Cache::forget('appName');
            }
        }

        // Update logo (sincroniza claves legacy APP.LOGO_RUTA / app.logo_path)
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
            $this->persistParametro('APP.LOGO_RUTA', $path, $user); // mantener legacy
            Cache::forget('appLogoUrl');
            Cache::forget('appName'); // en caso de UI refresque banner completo
        }

        // Update logo height (sincroniza clave legacy APP.LOGO_ALTO)
        if (array_key_exists('logo_height', $validated)) {
            $height = (int) $validated['logo_height'];
            if ($height > 0) {
                $this->persistParametro('app.logo_height', $height, $user);
                $this->persistParametro('APP.LOGO_ALTO', $height, $user);
                Cache::forget('appLogoHeight');
            }
        }

        // Timezone
        if (array_key_exists('timezone', $validated)) {
            $tz = $validated['timezone'];
            if (!empty($tz)) {
                $this->persistParametro('app.timezone', $tz, $user);
                Cache::forget('appTimezone');
                try {
                    config(['app.timezone' => $tz]);
                    date_default_timezone_set($tz);
                } catch (\Throwable $e) {
                    // ignore invalid at runtime; validation should have caught it
                }
            }
        }

        // Date format
        if (array_key_exists('date_format', $validated)) {
            $df = $validated['date_format'];
            if (!empty($df)) {
                $this->persistParametro('app.date_format', $df, $user);
                Cache::forget('appDateFormat');
            }
        }

        // Sessions limit
        if (array_key_exists('sessions_limit', $validated)) {
            $sl = (int) $validated['sessions_limit'];
            if ($sl > 0) {
                $this->persistParametro('auth.sessions_limit', $sl, $user);
                Cache::forget('authSessionsLimit');
            }
        }

        // Admin intentos
        if (array_key_exists('admin_intentos', $validated)) {
            $val = (int) $validated['admin_intentos'];
            if ($val > 0) {
                $this->upsertParametro('ADMIN.INTENTOS_INICIO_SESION', $val, $user);
            }
        }
        // Admin correo
        if (array_key_exists('admin_correo', $validated)) {
            $val = $validated['admin_correo'];
            if ($val !== null) {
                $this->upsertParametro('ADMIN.CORREO', $val, $user);
            }
        }
        // Admin usuario
        if (array_key_exists('admin_usuario', $validated)) {
            $val = $validated['admin_usuario'];
            if ($val !== null) {
                $this->upsertParametro('ADMIN.USUARIO', $val, $user);
            }
        }
        // Admin password (texto plano conforme implementación actual)
        if (array_key_exists('admin_password', $validated)) {
            $val = $validated['admin_password'];
            if ($val !== null) {
                $this->upsertParametro('ADMIN.PASSWORD', $val, $user);
            }
        }

        // Return updated values
        return $this->show($request);
    }

    private function upsertParametro(string $clave, $valor, $user): void
    {
        $this->persistParametro($clave, $valor, $user);
    }

    /**
     * Persist (create or update) a parámetro ensuring creation columns are set on first insert.
     */
    private function persistParametro(string $clave, $valor, $user): void
    {
        $param = Parametro::where('parametro', $clave)->first();
        $now = now();
        if (!$param) {
            $param = new Parametro();
            $param->parametro = $clave;
            $param->creado_por = $user?->usuario ?? 'system';
            $param->fecha_creacion = $now;
            $param->id_usuario_fk = $user?->id_usuario_pk; // track who created
        }
        $param->valor = $valor;
        $param->modificado_por = $user?->usuario ?? 'system';
        $param->fecha_modificacion = $now;
        // ensure id_usuario_fk is kept (if null previously) on updates
        if (!$param->id_usuario_fk) {
            $param->id_usuario_fk = $user?->id_usuario_pk;
        }
        $param->save();
    }
}