<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'tbl_pais';
    protected $primaryKey = 'id_pais_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_pais'
    ];

    // Relación con Departamentos
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'id_pais_pk', 'id_pais_pk');
    }
}
