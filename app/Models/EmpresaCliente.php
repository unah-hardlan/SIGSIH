<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaCliente extends Model
{
    use HasFactory;

    protected $table = 'tbl_empresa_cliente';
    protected $primaryKey = 'id_empresa_cliente_pk';
    public $timestamps = false;

    protected $fillable = [
        'fecha_registro',
        'id_nombre_empresa_fk',
        'id_direccion_fk',
        'id_oficina_fk',
        'estado_empresa'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime'
    ];

    // Relación con Nombre Empresa
    public function nombreEmpresa()
    {
        return $this->belongsTo(NombreEmpresa::class, 'id_nombre_empresa_fk', 'id_nombre_empresa_pk');
    }

    // Relación con Dirección
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion_fk', 'id_direccion_pk');
    }

    // Relación con Oficina Empresa
    public function oficina()
    {
        return $this->belongsTo(OficinaEmpresa::class, 'id_oficina_fk', 'id_oficina_empresa_pk');
    }
}
