<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoObjeto extends Model
{
    use HasFactory;

    protected $table = 'tbl_tipo_objetos';
    protected $primaryKey = 'id_tipo_objeto_pk';
    public $incrementing = true;
    protected $keyType = 'int';
    
    // Usar timestamps personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';
    
    protected $fillable = [
        'nombre_tipo_objeto',
        'descripcion_tipo_objeto',
        'creado_por',
        'modificado_por',
    ];

    protected $dates = [
        'fecha_creacion',
        'fecha_modificacion',
    ];

    // Evento para establecer automáticamente creado_por y modificado_por
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = auth()->user();
            if ($user) {
                $model->creado_por = $user->usuario ?? 'system';
            } else {
                $model->creado_por = 'system';
            }
        });

        static::updating(function ($model) {
            $user = auth()->user();
            if ($user) {
                $model->modificado_por = $user->usuario ?? 'system';
            } else {
                $model->modificado_por = 'system';
            }
        });
    }
}
