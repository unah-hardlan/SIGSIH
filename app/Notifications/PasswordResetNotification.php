<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $token,
        protected ?string $email = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $email = $this->email ?? $notifiable->getEmailForPasswordReset();
        $url = $this->resetUrl($email);
        $expire = (int) config('auth.passwords.users.expire', 60);
        $name = trim((string) ($notifiable->nombre_usuario ?? $notifiable->usuario ?? ''));

        return (new MailMessage)
            ->subject(config('app.name', 'SIGSIH') . ' - Restablecimiento de contraseña')
            ->greeting($name !== '' ? 'Hola ' . $name : 'Hola')
            ->line('Recibiste este correo porque se solicitó restablecer tu contraseña en SIGSIH.')
            ->line('El enlace estará disponible durante ' . $expire . ' minutos.')
            ->action('Restablecer contraseña', $url)
            ->line('Si no solicitaste el cambio, ignora este mensaje.')
            ->salutation('Saludos, ' . config('app.name', 'SIGSIH'));
    }

    protected function resetUrl(string $email): string
    {
        $frontendUrl = config('app.frontend_url');
        if ($frontendUrl) {
            $query = http_build_query([
                'token' => $this->token,
                'email' => $email,
            ]);

            return rtrim($frontendUrl, '/') . '/reset-password?' . $query;
        }

        return url(route('password.reset.form', [
            'token' => $this->token,
            'email' => $email,
        ], false));
    }
}
