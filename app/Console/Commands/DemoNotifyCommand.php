<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Notifications\SystemNotification;

class DemoNotifyCommand extends Command
{
    protected $signature = 'demo:notify {userId} {--n=1 : Number of notifications to create}';
    protected $description = 'Create demo notifications for a given user id';

    public function handle(): int
    {
        $userId = (int)$this->argument('userId');
        $count = (int)$this->option('n');

        $user = Usuario::find($userId);
        if (!$user) {
            $this->error("Usuario {$userId} no encontrado");
            return self::FAILURE;
        }

        for ($i = 1; $i <= max(1, $count); $i++) {
            $title = "Demo Noti #{$i}";
            $user->notify(new SystemNotification([
                'title' => $title,
                'body' => 'Notificación de prueba generada por demo:notify',
                'url' => '/',
                'icon' => 'fa-bell',
                'severity' => 'info',
                'module' => 'demo',
                'meta' => ['i' => $i],
            ]));
        }

        $this->info("Se generaron {$count} notificaciones para el usuario {$userId}.");
        return self::SUCCESS;
    }
}
