<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoProyecto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_estado_proyecto';
    protected $primaryKey = 'id_estado_proyecto_pk';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'es_final', 'orden'
    ];

  
    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'id_estado_proyecto_fk', 'id_estado_proyecto_pk');
    }
}