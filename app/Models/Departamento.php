<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'tbl_departamento';
    protected $primaryKey = 'id_departamento_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_departamento',
        'id_pais_pk'
    ];

    // Relación con País
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais_pk', 'id_pais_pk');
    }
}
