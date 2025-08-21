<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'tbl_contacto';
    protected $primaryKey = 'id_contacto_pk';
    
    protected $fillable = [
        'tipo_contacto',
        'valor_contacto',
        'id_persona_fk',
    ];

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona_fk', 'id_persona_pk');
    }
}
