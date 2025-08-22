<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tbl_tickets';
    protected $primaryKey = 'id_ticket_pk';
    public $timestamps = false;

    protected $fillable = [
        'fecha_creacion',
        'descripcion_ticket',
        'id_estado_ticket_fk',
        'id_tecnico_fk',
        'id_cliente_fk'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime'
    ];

    /**
     * Relación con el estado del ticket
     */
    public function estado()
    {
        return $this->belongsTo(EstadoTicket::class, 'id_estado_ticket_fk', 'id_estado_ticket_pk');
    }

    /**
     * Relación con el técnico asignado
     */
    public function tecnico()
    {
        return $this->belongsTo(Persona::class, 'id_tecnico_fk', 'id_persona_pk');
    }

    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Persona::class, 'id_cliente_fk', 'id_persona_pk');
    }
}
