<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoSolicitud extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'tbl_estado_solicitud';
    protected $primaryKey = 'id_estado_solicitud_pk';
    
    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'es_final', 'orden'
    ];

  
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_estado_solicitud_fk', 'id_estado_solicitud_pk');
    }
}
