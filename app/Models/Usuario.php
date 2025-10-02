<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'nombre_usuario',
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
        // 'primer_ingreso' se normaliza con accessor para soportar 'S'/'N' en BD
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
            // primer ingreso por defecto (si no se envía)
            $rawPrimerIngreso = method_exists($model, 'getRawOriginal')
                ? $model->getRawOriginal('primer_ingreso')
                : ($model->attributes['primer_ingreso'] ?? null);
            if ($rawPrimerIngreso === null) {
                $model->primer_ingreso = 1;
            }
            // Valor por defecto para estado_usuario si la BD lo requiere (NOT NULL)
            if (empty($model->estado_usuario)) {
                $model->estado_usuario = 'ACTIVO';
            }
        });

        static::updating(function ($model) {
            $model->fecha_modificacion = now();
            $model->modificado_por = auth()->user()->usuario ?? 'system';
        });
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Relación directa (FK) al rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol_fk', 'id_rol_pk');
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'tbl_usuario_rol', 'id_usuario_fk', 'id_rol_fk');
    }

    // Hash de contraseña automático con verificación de rehash
    protected function setContrasenaAttribute($value)
    {
        if (!isset($value) || $value === '') {
            $this->attributes['contrasena'] = $value;
            return;
        }
        // Si ya parece ser un hash bcrypt/argon (60+ chars con prefijo), no rehash
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

    // Normaliza primer_ingreso: true para 1/'1'/true/'S'/'Y'; false para 0/'0'/false/'N' o null
    public function getPrimerIngresoAttribute($value)
    {
        return in_array($value, [1, '1', true, 'S', 's', 'Y', 'y'], true);
    }

    // Al asignar, almacenamos 1 o 0 (entero) para compatibilidad con la columna en BD
    public function setPrimerIngresoAttribute($value)
    {
        $this->attributes['primer_ingreso'] = in_array($value, [1, '1', true, 'S', 's', 'Y', 'y'], true) ? 1 : 0;
    }

    // Mutator: garantizar que el nombre de usuario se almacene en MAYÚSCULAS sin espacios extremos
    public function setUsuarioAttribute($value)
    {
        $this->attributes['usuario'] = strtoupper(trim((string)$value));
    }

    /**
     * Obtiene el correo a utilizar para el flujo de restablecimiento de contraseña.
     */
    public function getEmailForPasswordReset()
    {
        return (string) $this->correo_electronico;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->correo_electronico;
    }

    // Email verification helpers
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