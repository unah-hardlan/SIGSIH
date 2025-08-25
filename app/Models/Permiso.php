<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_ms_permisos';
    protected $primaryKey = 'id_permiso_pk';
    protected $fillable = [
        'id_rol_fk',
        'id_objeto_fk',
    'permiso_insercion',
    'permiso_eliminacion',
    'permiso_actualizar',
    'permiso_consultar',
        'creado_por',
        'fecha_creacion',
        'modificado_por',
        'fecha_modificacion',
    ];

    protected $casts = [
    'permiso_insercion' => 'boolean',
    'permiso_eliminacion' => 'boolean',
    'permiso_actualizar' => 'boolean',
    'permiso_consultar' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $now = now();
            if (!$model->fecha_creacion) $model->fecha_creacion = $now;
            if (!$model->creado_por) $model->creado_por = auth()->user()->usuario ?? 'system';
        });
        static::updating(function ($model) {
            $model->fecha_modificacion = now();
            $model->modificado_por = auth()->user()->usuario ?? 'system';
        });
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol_fk');
    }

    public function objeto()
    {
        return $this->belongsTo(Objeto::class, 'id_objeto_fk');
    }
}