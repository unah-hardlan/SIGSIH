<?php

namespace App\Notifications;

use App\Models\Parametro;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $token
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);
        $name = trim((string) ($notifiable->nombre_usuario ?? $notifiable->usuario ?? ''));

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
            'verifyUrl' => $url,
            'supportEmail' => $supportEmail,
            'year' => now()->year,
            'appUrl' => config('app.url'),
        ];

        return (new MailMessage)
            ->subject($appName . ' - Verificación de correo')
            ->view('emails.auth.verify-email', $viewData)
            ->text('emails.auth.verify-email_plain', $viewData);
    }

    protected function verificationUrl($notifiable): string
    {
        $frontendUrl = config('app.frontend_url');
        if ($frontendUrl) {
            $query = http_build_query([
                'token' => $this->token,
                'email' => $notifiable->correo_electronico,
            ]);
            return rtrim($frontendUrl, '/').'/verify-email?'.$query;
        }
        
        return url('/verificar-correo?token='.$this->token.'&email='.urlencode($notifiable->correo_electronico));
    }

    protected function getParametro(string $key): ?string
    {
        return Parametro::where('parametro', $key)->value('valor');
    }
}
