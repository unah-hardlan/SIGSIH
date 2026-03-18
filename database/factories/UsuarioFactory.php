<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;
    public $timestamps = false;



    public function definition()
    {
        return [
            'usuario' => $this->faker->userName,
            'estado_usuario' => 'A',
            'contrasena' => bcrypt('password'),
            'correo_electronico' => $this->faker->unique()->safeEmail,
            'primer_ingreso' => 'N',
            'fecha_ultima_conexion' => now(),
            'fecha_vencimiento' => now()->addYear(),
            'creado_por' => 'SYSTEM',
            'fecha_creacion' => now(),
            'modificado_por' => 'SYSTEM',
            'fecha_modificacion' => now(),
        ];
    }
}
