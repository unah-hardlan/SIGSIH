<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTicket extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_ticket';
    protected $primaryKey = 'id_estado_ticket_pk';
    public $timestamps = false;

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'es_final', 'orden'
    ];
}
