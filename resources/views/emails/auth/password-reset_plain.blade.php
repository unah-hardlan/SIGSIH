Hola{{ $greetingName ? ' ' . $greetingName : '' }},

Recibimos una solicitud para restablecer tu contraseña en {{ $appName }}.

Abre el siguiente enlace para continuar (expira en {{ $expireMinutes }} minuto{{ $expireMinutes === 1 ? '' : 's' }}):
{{ $resetUrl }}

Si no solicitaste este cambio, puedes ignorar este mensaje y tu contraseña seguirá siendo la misma.

@if ($supportEmail)
Si necesitas ayuda adicional, escríbenos a {{ $supportEmail }}.

@endif
Gracias,
El equipo de {{ $appName }}

© {{ $year }} {{ $appName }}
@if ($appUrl)
{{ $appUrl }}
@endif
