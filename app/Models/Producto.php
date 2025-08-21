<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_producto';
    protected $primaryKey = 'id_producto_pk';
    protected $fillable = [
        'nombre_producto',
        'descripcion_producto',
        'precio_unitario',
        'precio_venta',
        'stock_minimo',
        'fecha_registro',
        'id_tipo_producto_fk',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'precio_unitario' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_minimo' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if(!$model->fecha_registro) $model->fecha_registro = now();
        });
    }

    public function tipoProducto()
    {
        return $this->belongsTo(TipoProducto::class,'id_tipo_producto_fk','id_tipo_producto_pk');
    }
}
