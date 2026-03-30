<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable implements \Illuminate\Contracts\Auth\CanResetPassword
{
    use HasFactory, Notifiable, \Illuminate\Auth\Passwords\CanResetPassword;
    public $timestamps = true;
    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_modificacion';

    protected $table = 'tbl_ms_usuario';
    protected $primaryKey = 'id_usuario_pk';
    protected $fillable = [
        'usuario',
        'estado_usuario',
        'contrasena',
        'correo_electronico',
        'email_verified_at',
        'email_verification_token',
        'email_verification_sent_at',
        'id_rol_fk',
        'primer_ingreso',
        'fecha_ultima_conexion',
        'fecha_vencimiento',
        'creado_por',
        'fecha_creacion',
        'modificado_por',
        'fecha_modificacion',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_enabled',
    ];
    protected $hidden = [
        'contrasena',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [

        'fecha_ultima_conexion' => 'datetime',
        'fecha_vencimiento' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
        'email_verified_at' => 'datetime',
        'email_verification_sent_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $now = now();
            if (!$model->fecha_creacion) {
                $model->fecha_creacion = $now;
            }
            if (!$model->creado_por) {
                $model->creado_por = auth()->user()->usuario ?? 'system';
            }

            $rawPrimerIngreso = method_exists($model, 'getRawOriginal')
                ? $model->getRawOriginal('primer_ingreso')
                : ($model->attributes['primer_ingreso'] ?? null);
            if ($rawPrimerIngreso === null) {
                $model->primer_ingreso = 1;
            }

            if (empty($model->estado_usuario)) {
                $model->estado_usuario = 'ACTIVO';
            }
        });

        static::updating(function ($model) {
            $model->fecha_modificacion = now();
            $model->modificado_por = auth()->user()->usuario ?? 'system';
        });
    }


    public function getAuthPassword()
    {
        return $this->contrasena;
    }


    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol_fk', 'id_rol_pk');
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'tbl_usuario_rol', 'id_usuario_fk', 'id_rol_fk');
    }


    protected function setContrasenaAttribute($value)
    {
        if (!isset($value) || $value === '') {
            $this->attributes['contrasena'] = $value;
            return;
        }

        $str = (string)$value;
        $isHashed = preg_match('/^\$2y\$|^\$argon2id\$|^\$argon2i\$/', $str) === 1;
        $this->attributes['contrasena'] = $isHashed ? $str : \Illuminate\Support\Facades\Hash::make($str);
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario_fk');
    }

    public function parametros()
    {
        return $this->hasMany(Parametro::class, 'id_usuario_fk');
    }

    public function persona()
    {
        return $this->hasOne(Persona::class, 'id_usuario_fk', 'id_usuario_pk');
    }

    public function getNombreAttribute(): string
    {
        $persona = $this->relationLoaded('persona')
            ? $this->persona
            : $this->persona()->first();

        if ($persona) {
            $nombre = trim(implode(' ', array_filter([
                $persona->primer_nombre,
                $persona->segundo_nombre,
                $persona->primer_apellido,
                $persona->segundo_apellido,
            ])));

            if ($nombre !== '') {
                return $nombre;
            }
        }

        return (string) $this->usuario;
    }

    public function scopeSearchByIdentity(Builder $query, string $term): Builder
    {
        $term = trim($term);

        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('usuario', 'like', "%{$term}%")
                ->orWhere('correo_electronico', 'like', "%{$term}%")
                ->orWhereHas('persona', function (Builder $personaQuery) use ($term) {
                    $personaQuery->where('primer_nombre', 'like', "%{$term}%")
                        ->orWhere('segundo_nombre', 'like', "%{$term}%")
                        ->orWhere('primer_apellido', 'like', "%{$term}%")
                        ->orWhere('segundo_apellido', 'like', "%{$term}%");
                });
        });
    }

    public function scopeOrderByPersonaName(Builder $query, string $direction = 'asc'): Builder
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $query
            ->leftJoin('tbl_persona as persona_usuario', 'persona_usuario.id_usuario_fk', '=', 'tbl_ms_usuario.id_usuario_pk')
            ->select('tbl_ms_usuario.*')
            ->orderByRaw("CASE WHEN COALESCE(TRIM(CONCAT_WS(' ', persona_usuario.primer_nombre, persona_usuario.segundo_nombre, persona_usuario.primer_apellido, persona_usuario.segundo_apellido)), '') = '' THEN 1 ELSE 0 END asc")
            ->orderBy('persona_usuario.primer_nombre', $direction)
            ->orderBy('persona_usuario.primer_apellido', $direction)
            ->orderBy('tbl_ms_usuario.usuario', $direction);
    }


    public function getPrimerIngresoAttribute($value)
    {
        return in_array($value, [1, '1', true, 'S', 's', 'Y', 'y'], true);
    }


    public function setPrimerIngresoAttribute($value)
    {
        $this->attributes['primer_ingreso'] = in_array($value, [1, '1', true, 'S', 's', 'Y', 'y'], true) ? 1 : 0;
    }


    public function setUsuarioAttribute($value)
    {
        $this->attributes['usuario'] = strtoupper(trim((string)$value));
    }


    public function getEmailForPasswordReset()
    {
        return (string) $this->correo_electronico;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->correo_electronico;
    }


    public function receivesBroadcastNotificationsOn(): string
    {
        return 'App.Models.Usuario.' . $this->getKey();
    }


    public function notifications()
    {
        return $this->morphMany(DbNotification::class, 'notifiable', 'tipo_notificable', 'id_notificable')
            ->orderBy('fecha_creacion', 'desc');
    }


    public function routeNotificationForDatabase($notification)
    {
        return $this->notifications();
    }

    public function readNotifications()
    {
        return $this->morphMany(DbNotification::class, 'notifiable', 'tipo_notificable', 'id_notificable')
            ->whereNotNull('fecha_lectura')
            ->orderBy('fecha_creacion', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->morphMany(DbNotification::class, 'notifiable', 'tipo_notificable', 'id_notificable')
            ->whereNull('fecha_lectura')
            ->orderBy('fecha_creacion', 'desc');
    }


    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): void
    {
        $this->email_verified_at = now();
        $this->email_verification_token = null;
        $this->email_verification_sent_at = null;
        $this->save();
    }
}
