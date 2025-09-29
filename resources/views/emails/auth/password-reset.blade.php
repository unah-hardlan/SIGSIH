@component('mail::message')
@if ($logoUrl)
<center>
<img src="{{ $logoUrl }}" alt="Logo {{ $appName }}" style="max-height: 72px; margin-bottom: 16px;">
</center>
@endif

@if ($greetingName)
# Hola {{ $greetingName }}
@else
# Hola
@endif

Recibimos una solicitud para restablecer tu contraseña en **{{ $appName }}**.

@component('mail::button', ['url' => $resetUrl])
Restablecer contraseña
@endcomponent

El enlace vence en {{ $expireMinutes }} minuto{{ $expireMinutes === 1 ? '' : 's' }}. Si no solicitaste este cambio, puedes ignorar este mensaje.

@if ($supportEmail)
Si necesitas ayuda adicional, escríbenos a [{{ $supportEmail }}](mailto:{{ $supportEmail }}).
@endif

Gracias,<br>
El equipo de {{ $appName }}
@endcomponent
