<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_kardex';
    protected $primaryKey = 'id_kardex_pk';

    protected $fillable = [
        'id_producto_fk',
        'id_tipo_movimiento_fk',
        'cantidad',
        'fecha_movimiento',
        'motivo',
        'id_tecnico_fk',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'cantidad' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if(!$model->fecha_movimiento){
                $model->fecha_movimiento = now();
            }
        });
    }

    public function producto(){ return $this->belongsTo(Producto::class,'id_producto_fk','id_producto_pk'); }
    public function tipoMovimiento(){ return $this->belongsTo(TipoMovimiento::class,'id_tipo_movimiento_fk','id_tipo_movimiento_pk'); }
    public function tecnico(){ return $this->belongsTo(Persona::class,'id_tecnico_fk','id_persona_pk'); }
}
