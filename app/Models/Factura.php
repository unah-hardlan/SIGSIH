<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'tbl_factura';
    protected $primaryKey = 'id_factura_pk';
    public $timestamps = false;

    protected $fillable = [
        'numero',
        'fecha',
        'oc',
        'subtotal',
        'impuesto',
        'total',
        'total_letras',
        'id_estado_factura_fk',
        'id_cai_fk',
        'id_cliente_fk',
        'id_cotizacion_fk'
    ];

    
    public function getRouteKeyName()
    {
        return 'id_factura_pk';
    }

    public function estadoFactura()
    {
        return $this->belongsTo(EstadoFactura::class, 'id_estado_factura_fk', 'id_estado_factura_pk');
    }

    public function cai()
    {
        return $this->belongsTo(Cai::class, 'id_cai_fk', 'id_cai_pk');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion_fk', 'id_cotizacion_pk');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class, 'id_factura_fk', 'id_factura_pk');
    }
}
