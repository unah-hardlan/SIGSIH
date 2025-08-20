<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_ms_rol';
    protected $primaryKey = 'id_rol_pk';
    protected $fillable = [
        'rol',
        'descripcion_rol',
        'creado_por',
        'fecha_creacion',
        'modificado_por',
        'fecha_modificacion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            $now = now();
            if(!$model->fecha_creacion) $model->fecha_creacion = $now;
            if(!$model->creado_por) $model->creado_por = auth()->user()->usuario ?? 'system';
        });
        static::updating(function($model){
            $model->fecha_modificacion = now();
            $model->modificado_por = auth()->user()->usuario ?? 'system';
        });
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'tbl_usuario_rol', 'id_rol_fk', 'id_usuario_fk');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'id_rol_fk');
    }
}
