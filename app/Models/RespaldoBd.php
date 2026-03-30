<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespaldoBd extends Model
{
    use HasFactory;

    public $timestamps = true;
    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_modificacion';

    protected $table = 'tbl_ms_respaldo_bd';
    protected $primaryKey = 'id_respaldo_bd_pk';

    protected $fillable = [
        'nombre_archivo',
        'ruta_archivo',
        'tamano_bytes',
        'checksum_sha1',
        'tipo_respaldo',
        'estado_respaldo',
        'id_usuario_fk',
        'observacion',
        'creado_por',
        'fecha_creacion',
        'modificado_por',
        'fecha_modificacion',
    ];

    protected $casts = [
        'tamano_bytes' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
    ];
}
