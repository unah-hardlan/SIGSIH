<?php

namespace App\Console\Commands;

use App\Jobs\AvisoOrdenServicioPorIniciar;
use App\Jobs\EnviarRecordatorioVisita;
use App\Models\Calendario;
use Illuminate\Console\Command;

class ProgramarRecordatoriosCalendario extends Command
{
    protected $signature = 'calendario:programar-recordatorios';
    protected $description = 'Programa recordatorios 24–48h antes de visitas y avisos de OS por iniciar';

    public function handle(): int
    {
        // Recordatorios de visitas 24-48 horas antes
        $now = now();
        $en48h = $now->copy()->addHours(48);
        $en24h = $now->copy()->addHours(24);

        // Selecciona eventos entre 24 y 48h
        Calendario::whereBetween('fecha', [$en24h, $en48h])
            ->whereNotNull('id_usuario_fk')
            ->get()
            ->each(function ($evento) use ($now) {
                // Programa job a ejecutarse 24h antes si falta >24h, o inmediato si está en la ventana
                $delay = $evento->fecha->copy()->subHours(24)->diffInSeconds($now, false);
                if ($delay > 0) {
                    // Ya estamos dentro de la ventana, ejecutar ahora
                    EnviarRecordatorioVisita::dispatch($evento->id_calendario_pk);
                } else {
                    EnviarRecordatorioVisita::dispatch($evento->id_calendario_pk)->delay($evento->fecha->copy()->subHours(24));
                }
            });

        // Aviso OS por iniciar: si la OS asociada comienza en <=24h (usamos fecha_asignada o fecha_creada como proxy)
        Calendario::whereNotNull('id_orden_servicio_fk')
            ->where('fecha', '<=', $en24h)
            ->where('fecha', '>=', $now)
            ->get()
            ->each(function ($evento) use ($now) {
                AvisoOrdenServicioPorIniciar::dispatch($evento->id_orden_servicio_fk);
            });

        $this->info('Recordatorios programados.');
        return self::SUCCESS;
    }
}
