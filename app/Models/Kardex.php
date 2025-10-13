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
        'id_origen_fk',
        'id_producto_fk',
        'id_tipo_movimiento_fk',
        'cantidad',
        'fecha_movimiento',
        'motivo',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'cantidad' => 'decimal:3', 
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if(!$model->fecha_movimiento) $model->fecha_movimiento = now();
        });
    }

    public function producto(){ return $this->belongsTo(Producto::class,'id_producto_fk','id_producto_pk'); }
    public function tipoMovimiento(){ return $this->belongsTo(TipoMovimiento::class,'id_tipo_movimiento_fk','id_tipo_movimiento_pk'); }
    public function origen(){ return $this->belongsTo(Origen::class,'id_origen_fk','id_origen_pk'); }
}