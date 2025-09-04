<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_ms_bitacora';
    protected $primaryKey = 'id_bitacora_pk';
    protected $fillable = [
        'fecha_evento',
        'id_usuario_fk',
        'id_objetos_fk',
        'accion',
        'descripcion',
        'creado_por',
        'fecha_creacion',
    ];

    protected $casts = [
        'fecha_evento' => 'datetime',
        'fecha_creacion' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $now = now();
            if (!$model->fecha_evento) $model->fecha_evento = $now;
            if (!$model->fecha_creacion) $model->fecha_creacion = $now;
            if (!$model->creado_por) $model->creado_por = auth()->user()->usuario ?? 'system';
            if (!$model->id_usuario_fk) $model->id_usuario_fk = auth()->user()->id_usuario_pk ?? null;
        });
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_fk');
    }

    public function objeto()
    {
        return $this->belongsTo(Objeto::class, 'id_objetos_fk');
    }
}
