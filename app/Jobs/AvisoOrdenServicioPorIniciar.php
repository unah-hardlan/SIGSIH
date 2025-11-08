<?php

namespace App\Jobs;

use App\Models\OrdenServicio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AvisoOrdenServicioPorIniciar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $ordenServicioId;

    public function __construct(int $ordenServicioId)
    {
        $this->ordenServicioId = $ordenServicioId;
    }

    public function handle(): void
    {
        $os = OrdenServicio::find($this->ordenServicioId);
        if (!$os) return;
        
        
    }
}
