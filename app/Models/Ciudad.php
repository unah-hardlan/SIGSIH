<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    use HasFactory;

    protected $table = 'tbl_ciudad';
    protected $primaryKey = 'id_ciudad_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_ciudad',
        'id_departamento_fk'
    ];

    
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento_fk', 'id_departamento_pk');
    }

    
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'id_ciudad_fk', 'id_ciudad_pk');
    }
}
