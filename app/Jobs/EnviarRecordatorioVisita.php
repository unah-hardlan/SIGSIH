<?php

namespace App\Jobs;

use App\Models\Calendario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarRecordatorioVisita implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $calendarioId;

    public function __construct(int $calendarioId)
    {
        $this->calendarioId = $calendarioId;
    }

    public function handle(): void
    {
        $evento = Calendario::find($this->calendarioId);
        if (!$evento) return;
        // TODO: implementar envío (notificación/email) a técnico y cliente
        // Notificación placeholder
        // Notification::send([$evento->tecnico, $evento->cliente], new VisitaRecordatorio($evento));
    }
}
