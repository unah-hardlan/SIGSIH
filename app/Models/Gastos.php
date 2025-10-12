<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gastos extends Model
{
    use HasFactory;

    protected $table = 'tbl_gasto';
    protected $primaryKey = 'id_gasto_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_gasto',
        'fecha_gasto',
        'monto_gasto',
        'descripcion_gasto',
        'id_proyecto_fk',
        'id_categoria_fk'
    ];

    protected $casts = [
        'fecha_gasto' => 'date',
        'monto_gasto' => 'decimal:2'
    ];

    // Relación con Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto_fk', 'id_proyecto_pk');
    }

    // Relación con Categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_fk', 'id_categoria_pk');
    }
}
