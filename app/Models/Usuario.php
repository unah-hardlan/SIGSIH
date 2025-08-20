<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_ms_usuario';
    protected $primaryKey = 'id_usuario_pk';
    protected $fillable = [
        'usuario',
        'nombre_usuario',
        'estado_usuario',
        'contrasena',
        'correo_electronico',
        'primer_ingreso',
        'fecha_ultima_conexion',
        'fecha_vencimiento',
        'creado_por',
        'fecha_creacion',
        'modificado_por',
        'fecha_modificacion',
    ];
    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'primer_ingreso' => 'boolean',
        'fecha_ultima_conexion' => 'datetime',
        'fecha_vencimiento' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
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
            if (is_null($model->primer_ingreso)) {
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

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'tbl_usuario_rol', 'id_usuario_fk', 'id_rol_fk');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario_fk');
    }

    public function parametros()
    {
        return $this->hasMany(Parametro::class, 'id_usuario_fk');
    }
}