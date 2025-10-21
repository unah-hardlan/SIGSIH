<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /** @var array<string,mixed> */
    protected array $payload;

    /**
     * @param array<string,mixed> $payload keys: title, body, url, icon, severity, module, meta
     */
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
        // Database for persistence and broadcast for realtime UI
        return ['database', 'broadcast'];
    }

    /** @return array<string,mixed> */
    public function toArray($notifiable): array
    {
        return $this->payload;
    }

    /** @return array<string,mixed> */
    public function toBroadcast($notifiable): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'data' => $this->toArray($notifiable),
            'created_at' => now()->toISOString(),
        ];
    }
}
