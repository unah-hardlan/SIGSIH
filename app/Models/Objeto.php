<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_objetos';
    protected $primaryKey = 'id_objetos_pk';
    protected $fillable = [
        'nombre_objeto',
        'descripcion_objeto',
        'id_tipo_objetos_fk',
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

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'id_objeto_fk');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_objetos_fk');
    }
}
