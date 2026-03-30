<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {

        $schedule->command('calendario:programar-recordatorios')->hourly();

        $schedule->command('users:prune-unverified')->dailyAt('03:00');

        $schedule->command('bitacora:prune --keep=100')->daily();

        // Respaldo automatico semanal de base de datos.
        $schedule->command('db:backup-automatico')->weeklyOn(1, '02:00');
    }


    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
