<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_categorias';
    protected $primaryKey = 'id_categoria_pk';

    // Corregido para incluir las columnas correctas
    protected $fillable = [
        'nombre_categoria',
        'descripcion_categoria' 
    ];

    /**
     * Relaciones con Ingresos y Gastos.
     */
    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingresos::class, 'id_categoria_fk', 'id_categoria_pk');
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gastos::class, 'id_categoria_fk', 'id_categoria_pk');
    }
    //     return $this->hasMany(Ingreso::class, 'id_categoria_fk', 'id_categoria_pk');
    // }

    // public function gastos(): HasMany
    // {
    //     return $this->hasMany(Gasto::class, 'id_categoria_fk', 'id_categoria_pk');
    // }
}