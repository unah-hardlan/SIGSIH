<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'tbl_detalle_factura';
    protected $primaryKey = 'id_detalle_pk';
    public $timestamps = false;

    protected $fillable = [
        'id_factura_fk',
        'id_servicio_fk',
        'descripcion',
        'precio_unitario',
        'cantidad',
        'impuesto',
        'total_linea',
        'fecha_servicio',
        'horas',
        'descuento'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'cantidad' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total_linea' => 'decimal:2',
        'fecha_servicio' => 'datetime',
        'horas' => 'decimal:2',
        'descuento' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Auto-calcular total_linea cuando cambian los componentes
        static::saving(function($model){
            $dirty = array_keys($model->getDirty());
            $componentes = ['precio_unitario','cantidad','impuesto','descuento'];
            $cambiaronComponentes = count(array_intersect($componentes, $dirty)) > 0;
            $totalManual = in_array('total_linea', $dirty);
            
            if ($cambiaronComponentes || !$totalManual) {
                $precio = (float) ($model->precio_unitario ?? 0);
                $cant = (float) ($model->cantidad ?? 1);
                $imp = (float) ($model->impuesto ?? 0);
                $desc = (float) ($model->descuento ?? 0);
                
                $subtotal = $precio * $cant;
                $model->total_linea = $subtotal + $imp - $desc;
            }
        });
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura_fk', 'id_factura_pk');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio_fk', 'id_servicio_pk');
    }
}
