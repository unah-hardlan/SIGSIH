<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class GenerateAutomaticDbBackup extends Command
{
    protected $signature = 'db:backup-automatico';
    protected $description = 'Genera un respaldo automatico de la base de datos y aplica retencion de 10 archivos.';

    public function handle(DatabaseBackupService $service): int
    {
        try {
            $result = $service->createBackup('automatico', null, 'system');
            $this->info('Respaldo automatico generado: ' . ($result['filename'] ?? 'N/A'));
            if (($result['rotation']['deleted_count'] ?? 0) > 0) {
                $this->info('Rotacion aplicada. Respaldos eliminados: ' . $result['rotation']['deleted_count']);
            }
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error al generar respaldo automatico: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
