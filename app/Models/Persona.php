<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_persona';
    protected $primaryKey = 'id_persona_pk';
    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'dni',
    'avatar_path',
        'id_genero_fk',
        'id_usuario_fk',
    ];

    public function genero(){ return $this->belongsTo(Genero::class,'id_genero_fk','id_genero_pk'); }
    public function usuario(){ return $this->belongsTo(Usuario::class,'id_usuario_fk','id_usuario_pk'); }

    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'id_persona_fk', 'id_persona_pk');
    }
}
