<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Parametro;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $token,
        protected ?string $email = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $email = $this->email ?? $notifiable->getEmailForPasswordReset();
        $url = $this->resetUrl($email);
        $expire = (int) config('auth.passwords.users.expire', 60);
        $name = trim((string) ($notifiable->nombre ?? $notifiable->usuario ?? ''));

        $appName = $this->getParametro('APP.NOMBRE')
            ?? $this->getParametro('app.name')
            ?? config('app.name', 'SIGSIH');

        $logoParam = $this->getParametro('APP.LOGO_RUTA')
            ?? $this->getParametro('app.logo_path');

        $logoUrl = null;
        if ($logoParam) {
            if (preg_match('#^(https?://|/)#i', $logoParam)) {
                $logoUrl = $logoParam;
            } else {
                $logoUrl = asset('storage/' . ltrim($logoParam, '/'));
            }
        }

        $supportEmail = $this->getParametro('APP.SOPORTE_CORREO')
            ?? config('mail.from.address');

        $viewData = [
            'greetingName' => $name !== '' ? $name : null,
            'appName' => $appName,
            'logoUrl' => $logoUrl,
            'resetUrl' => $url,
            'expireMinutes' => $expire,
            'supportEmail' => $supportEmail,
            'email' => $email,
            'year' => now()->year,
            'appUrl' => config('app.url'),
        ];

        return (new MailMessage)
            ->subject($appName . ' - Restablecimiento de contraseña')
            ->view('emails.auth.password-reset', $viewData)
            ->text('emails.auth.password-reset_plain', $viewData);
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

    protected function getParametro(string $key): ?string
    {
        return Parametro::where('parametro', $key)->value('valor');
    }
}
