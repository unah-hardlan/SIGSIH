<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    
    protected array $payload;

    
    public function __construct(array $payload)
    {
        $defaults = [
            'title' => 'Notificación',
            'body' => '',
            'url' => null,
            'icon' => 'fa-bell',
            'severity' => 'info',
            'module' => null,
            'meta' => [],
        ];
        $this->payload = array_replace($defaults, $payload);
    }

    public function via($notifiable): array
    {
        
        return ['database', 'broadcast'];
    }

    
    public function toArray($notifiable): array
    {
        return $this->payload;
    }

    
    public function toBroadcast($notifiable): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'data' => $this->toArray($notifiable),
            'created_at' => now()->toISOString(),
        ];
    }
}
