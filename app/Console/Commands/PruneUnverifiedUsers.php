<?php

namespace App\Console\Commands;

use App\Models\Parametro;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PruneUnverifiedUsers extends Command
{
    protected $signature = 'users:prune-unverified {--dry-run : Lista los usuarios que serían eliminados}';
    protected $description = 'Elimina usuarios que no han verificado su correo después de cierto tiempo (configurable)';

    public function handle(): int
    {
        $days = $this->getIntParam([
            'AUTH.VERIFY_EMAIL.DELETE_AFTER_DAYS',
            'auth.verify_email.delete_after_days',
        ], 30);

        if ($days <= 0) {
            $this->info('Prune deshabilitado (DELETE_AFTER_DAYS <= 0).');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days);
        $query = Usuario::query()
            ->whereNull('email_verified_at')
            ->where(function ($q) use ($cutoff) {
                $q->whereNotNull('email_verification_sent_at')
                  ->where('email_verification_sent_at', '<', $cutoff);
            });

        $dry = (bool) $this->option('dry-run');
        $count = (clone $query)->count();

        if ($dry) {
            $this->info("[Dry-run] Se eliminarían {$count} usuarios no verificados (enviados antes de {$cutoff}).");
            $list = (clone $query)->limit(50)->get(['id_usuario_pk', 'usuario', 'correo_electronico', 'email_verification_sent_at']);
            foreach ($list as $u) {
                $this->line(sprintf('#%d %s <%s> enviado:%s', $u->id_usuario_pk, $u->usuario, $u->correo_electronico, (string)$u->email_verification_sent_at));
            }
            return self::SUCCESS;
        }

        $deleted = 0;
        Usuario::unguard();
        $query->chunkById(500, function ($chunk) use (&$deleted) {
            foreach ($chunk as $user) {
                try {
                    $user->delete();
                    $deleted++;
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }, 'id_usuario_pk');

        $this->info("Eliminados {$deleted} usuarios no verificados.");
        return self::SUCCESS;
    }

    private function getIntParam(array $keys, int $default): int
    {
        foreach ($keys as $key) {
            $value = Parametro::where('parametro', $key)->value('valor');
            if ($value === null || $value === '') continue;
            if (is_numeric($value)) return (int) $value;
            if (is_string($value)) {
                $filtered = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                if ($filtered !== '' && is_numeric($filtered)) return (int) $filtered;
            }
        }
        return $default;
    }
}
