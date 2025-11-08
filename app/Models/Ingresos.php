<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingresos extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_ingresos';
    protected $primaryKey = 'id_ingresos_pk';

    protected $fillable = [
        'nombre_ingreso',
        'fecha_ingreso',
        'monto_ingreso',
        'descripcion_ingreso',
        'id_proyecto_fk',
        'id_categoria_fk'
    ];

    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto_fk', 'id_proyecto_pk');
    }

    
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_fk', 'id_categoria_pk');
    }
}
