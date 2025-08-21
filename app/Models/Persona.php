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
        'cargo',
        'id_tipo_persona_fk',
        'id_genero_fk',
        'id_perfil_fk',
        'id_usuario_fk',
    ];

    public function tipoPersona(){ return $this->belongsTo(TipoPersona::class,'id_tipo_persona_fk','id_tipo_persona_pk'); }
    public function genero(){ return $this->belongsTo(Genero::class,'id_genero_fk','id_genero_pk'); }
    public function perfil(){ return $this->belongsTo(Perfil::class,'id_perfil_fk','id_perfil_pk'); }
    public function usuario(){ return $this->belongsTo(Usuario::class,'id_usuario_fk','id_usuario_pk'); }
}
