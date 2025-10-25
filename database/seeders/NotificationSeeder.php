<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        if (!class_exists(\App\Models\Usuario::class)) return;
        $user = \App\Models\Usuario::first();
        if (!$user) return;
        $user->notify(new \App\Notifications\SystemNotification([
            'title' => 'Bienvenido a SIGSIH',
            'body' => 'Este es un ejemplo de notificación in-app.',
            'url' => '/admin/dashboard',
            'icon' => 'fa-bell',
            'severity' => 'info',
            'module' => 'dashboard',
        ]));
    }
}
