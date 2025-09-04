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

        return response()->json([
            'appName' => $appName,
            'logoUrl' => $logoUrl,
            'logoHeight' => $logoHeight,
            'timezone' => $timezone,
            'dateFormat' => $dateFormat,
            'sessionsLimit' => $sessionsLimit,
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
        ]);

        $user = Auth::user();

        // Update name
        if (array_key_exists('app_name', $validated)) {
            $name = $validated['app_name'] ?? null;
            if ($name !== null) {
                $param = Parametro::updateOrCreate(
                    ['parametro' => 'app.name'],
                    [
                        'valor' => $name,
                        'id_usuario_fk' => $user?->id_usuario_pk,
                        'modificado_por' => $user?->usuario ?? 'system',
                        'fecha_modificacion' => now(),
                    ]
                );
                if ($param->wasRecentlyCreated || empty($param->creado_por)) {
                    $param->creado_por = $user?->usuario ?? 'system';
                    $param->fecha_creacion = now();
                    $param->save();
                }
                Cache::forget('appName');
            }
        }

        // Update logo
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            // Delete old logo if exists and stored in public disk
            $old = optional(Parametro::where('parametro', 'app.logo_path')->first())->valor;
            if ($old && !preg_match('#^(https?://|/)#i', $old)) {
                try { Storage::disk('public')->delete($old); } catch (\Throwable $e) {}
            }

            // Store new logo under public/system
            $path = $file->store('system', 'public'); // e.g., system/abc.png

            $param = Parametro::updateOrCreate(
                ['parametro' => 'app.logo_path'],
                [
                    'valor' => $path,
                    'id_usuario_fk' => $user?->id_usuario_pk,
                    'modificado_por' => $user?->usuario ?? 'system',
                    'fecha_modificacion' => now(),
                ]
            );
            if ($param->wasRecentlyCreated || empty($param->creado_por)) {
                $param->creado_por = $user?->usuario ?? 'system';
                $param->fecha_creacion = now();
                $param->save();
            }
            Cache::forget('appLogoUrl');
        }

        // Update logo height
        if (array_key_exists('logo_height', $validated)) {
            $height = (int) $validated['logo_height'];
            if ($height > 0) {
                $param = Parametro::updateOrCreate(
                    ['parametro' => 'app.logo_height'],
                    [
                        'valor' => $height,
                        'id_usuario_fk' => $user?->id_usuario_pk,
                        'modificado_por' => $user?->usuario ?? 'system',
                        'fecha_modificacion' => now(),
                    ]
                );
                if ($param->wasRecentlyCreated || empty($param->creado_por)) {
                    $param->creado_por = $user?->usuario ?? 'system';
                    $param->fecha_creacion = now();
                    $param->save();
                }
                Cache::forget('appLogoHeight');
            }
        }

        // Timezone
        if (array_key_exists('timezone', $validated)) {
            $tz = $validated['timezone'];
            if (!empty($tz)) {
                $param = Parametro::updateOrCreate(
                    ['parametro' => 'app.timezone'],
                    [
                        'valor' => $tz,
                        'id_usuario_fk' => $user?->id_usuario_pk,
                        'modificado_por' => $user?->usuario ?? 'system',
                        'fecha_modificacion' => now(),
                    ]
                );
                if ($param->wasRecentlyCreated || empty($param->creado_por)) {
                    $param->creado_por = $user?->usuario ?? 'system';
                    $param->fecha_creacion = now();
                    $param->save();
                }
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
                $param = Parametro::updateOrCreate(
                    ['parametro' => 'app.date_format'],
                    [
                        'valor' => $df,
                        'id_usuario_fk' => $user?->id_usuario_pk,
                        'modificado_por' => $user?->usuario ?? 'system',
                        'fecha_modificacion' => now(),
                    ]
                );
                if ($param->wasRecentlyCreated || empty($param->creado_por)) {
                    $param->creado_por = $user?->usuario ?? 'system';
                    $param->fecha_creacion = now();
                    $param->save();
                }
                Cache::forget('appDateFormat');
            }
        }

        // Sessions limit
        if (array_key_exists('sessions_limit', $validated)) {
            $sl = (int) $validated['sessions_limit'];
            if ($sl > 0) {
                $param = Parametro::updateOrCreate(
                    ['parametro' => 'auth.sessions_limit'],
                    [
                        'valor' => $sl,
                        'id_usuario_fk' => $user?->id_usuario_pk,
                        'modificado_por' => $user?->usuario ?? 'system',
                        'fecha_modificacion' => now(),
                    ]
                );
                if ($param->wasRecentlyCreated || empty($param->creado_por)) {
                    $param->creado_por = $user?->usuario ?? 'system';
                    $param->fecha_creacion = now();
                    $param->save();
                }
                Cache::forget('authSessionsLimit');
            }
        }

        // Return updated values
        return $this->show($request);
    }
}
