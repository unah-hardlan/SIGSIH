@php
$appName = $appName ?? config('app.name', 'SIGSIH');
@endphp
@if (!empty($greetingName))
Hola {{ $greetingName }},
@else
Hola,
@endif

Para completar tu registro en {{ $appName }}, verifica tu correo con este enlace:
{{ $verifyUrl }}

Si necesitas ayuda, escribe a: {{ $supportEmail }}

Gracias,
El equipo de {{ $appName }}

© {{ $year }} {{ $appName }}