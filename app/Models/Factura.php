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
        'total',
        'total_letras',
        'id_estado_factura_fk',
        'id_cai_fk',
        'id_cliente_fk'
    ];

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
        return $this->belongsTo(Persona::class, 'id_cliente_fk', 'id_persona_pk');
    }
}
