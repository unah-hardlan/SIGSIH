<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCotizacion extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_item_cotizacion';
    protected $primaryKey = 'id_item_cotizacion_pk';

    protected $fillable = [
        'descripcion',
        'precio_unitario',
        'cantidad',
        'impuesto',
        'total',
        'id_cotizacion_fk',
        'id_producto_fk',
    ];

    protected $casts = [
        'precio_unitario' => 'float',
        'cantidad' => 'float',
        'impuesto' => 'float',
        'total' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            
            $dirty = array_keys($model->getDirty());
            $componentes = ['precio_unitario', 'cantidad', 'impuesto'];
            $cambiaronComponentes = count(array_intersect($componentes, $dirty)) > 0;
            $totalManual = in_array('total', $dirty);
            if ($cambiaronComponentes || !$totalManual) {
                $precio = (float) $model->precio_unitario;
                $cant = (float) $model->cantidad;
                $imp = (float) $model->impuesto;
                $model->total = $precio * $cant + $imp; 
            }
        });
    }

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion_fk', 'id_cotizacion_pk');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto_fk', 'id_producto_pk');
    }
}
