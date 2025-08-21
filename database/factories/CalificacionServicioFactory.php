<?php

namespace Database\Factories;

use App\Models\CalificacionServicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalificacionServicioFactory extends Factory
{
    protected $model = CalificacionServicio::class;

    public function definition(): array
    {
        return [
            'nombre_calificacion' => $this->faker->randomElement([
                'Excelente',
                'Muy Bueno',
                'Bueno',
                'Regular',
                'Malo',
                'Muy Malo',
                'Sobresaliente',
                'Satisfactorio',
                'Insatisfactorio',
                'Deficiente'
            ]),
            'descripcion_calificacion' => $this->faker->sentence(8),
        ];
    }
}
