<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_solicitud';
    protected $primaryKey = 'id_solicitud_pk';

    protected $fillable = [
        'id_cliente_fk',
        'nombre_solicitud',
        'numero_solicitud_acf',
        'numero_solicitud_cliente',
        'descripcion_problema',
        'id_estado_solicitud_fk',
        'id_contacto_fk'
    ];

    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    
    public function estadoSolicitud()
    {
        return $this->belongsTo(EstadoSolicitud::class, 'id_estado_solicitud_fk', 'id_estado_solicitud_pk');
    }

    
    public function contacto()
    {
        return $this->belongsTo(Contacto::class, 'id_contacto_fk', 'id_contacto_pk');
    }
}
