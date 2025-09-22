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
        'sku',
        'nombre_producto',
        'descripcion_producto',
        'precio_unitario',
        'precio_costo',
        'precio_venta',
        'stock_minimo',
        'fecha_registro',
        'id_tipo_producto_fk',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'precio_unitario' => 'decimal:2',
        'precio_costo' => 'decimal:2',
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

    // Relación con kardex para histórico de movimientos
    public function movimientosKardex()
    {
        return $this->hasMany(Kardex::class, 'id_producto_fk', 'id_producto_pk');
    }

    // Método para obtener stock actual calculado
    public function getStockActualAttribute()
    {
        return $this->movimientosKardex()
            ->selectRaw('
                COALESCE(
                    SUM(CASE WHEN tipo_movimiento = "ENTRADA" THEN cantidad ELSE 0 END) -
                    SUM(CASE WHEN tipo_movimiento = "SALIDA" THEN cantidad ELSE 0 END) +
                    SUM(CASE WHEN tipo_movimiento = "AJUSTE" THEN cantidad ELSE 0 END), 
                    0
                ) as stock_actual
            ')
            ->value('stock_actual') ?? 0;
    }

    // Método para verificar si está en stock crítico
    public function isStockCritico()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}
