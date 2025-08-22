<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_categorias';
    protected $primaryKey = 'id_categoria_pk';

    protected $fillable = [
        'tipo_categoria',
        'nombre_categoria'
    ];

    /**
     * Relación con ingresos
     */
    public function ingresos()
    {
        return $this->hasMany(Ingresos::class, 'id_categoria_fk', 'id_categoria_pk');
    }
}
