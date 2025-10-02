<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - Verificación de correo</title>
    <style>
        :root { color-scheme: light; }
        body { margin:0; padding:0; background:#f3f6fb; font-family:'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color:#1f2937; }
        .email-wrapper { width:100%; background:#f3f6fb; padding:24px 12px; }
        .card { max-width:560px; margin:0 auto; background:#ffffff; border-radius:18px; box-shadow:0 14px 48px rgba(15,23,42,.1); overflow:hidden; }
        .card-header { background:linear-gradient(135deg,#1d4ed8,#2563eb); padding:28px 32px; text-align:center; }
        .brand { display:inline-flex; align-items:center; gap:12px; color:#fff; font-size:20px; font-weight:600; letter-spacing:.3px; }
        .brand img { max-height:54px; display:block; }
        .brand-placeholder { font-size:24px; font-weight:700; }
        .card-body { padding:32px; }
        h1 { margin:0 0 16px; font-size:24px; color:#0f172a; }
        p { margin:0 0 16px; line-height:1.6; }
        .button { display:inline-block; margin:24px 0; padding:16px 40px; background:linear-gradient(135deg,#1d4ed8,#2563eb); color:#fff!important; text-decoration:none; font-weight:600; border-radius:999px; letter-spacing:.5px; box-shadow:0 10px 30px rgba(37,99,235,.35); }
        .button:hover { background:linear-gradient(135deg,#1f3fd6,#1e4ed8); }
        .info-box { background:#f1f5ff; border-radius:12px; padding:18px 20px; font-size:14px; color:#1e3a8a; margin-bottom:24px; }
        .footer { padding:0 32px 28px; font-size:12px; color:#6b7280; text-align:center; }
        @media (max-width:600px){ .card-body{padding:28px 22px;} .button{width:100%; text-align:center;} }
    </style>
    <!-- Safe preview text -->
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body>
<div class="email-wrapper">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
        <tr><td>
            <div class="card">
                <div class="card-header">
                    <div class="brand">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo {{ $appName }}">
                        @endif
                        <span class="brand-placeholder">{{ $appName }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <h1>
                        @if(!empty($greetingName))
                            Hola {{ $greetingName }}
                        @else
                            Hola
                        @endif
                    </h1>
                    <p>
                        Para completar tu registro en <strong>{{ $appName }}</strong>, por favor verifica tu correo electrónico.
                    </p>
                    <p style="text-align:center;">
                        <a href="{{ $verifyUrl }}" class="button">Verificar correo</a>
                    </p>
                    <p>
                        ¿No puedes hacer clic en el botón? Copia y pega este enlace en tu navegador:
                    </p>
                    <p style="word-break: break-all; font-size: 14px; color: #1d4ed8;">
                        <a href="{{ $verifyUrl }}" style="color:#1d4ed8;">{{ $verifyUrl }}</a>
                    </p>
                    @if ($supportEmail)
                        <p style="margin-top:28px;">
                            ¿Necesitas ayuda? Escríbenos a
                            <a href="mailto:{{ $supportEmail }}" style="color:#2563eb; font-weight:600;">{{ $supportEmail }}</a>.
                        </p>
                    @endif
                    <p style="margin-top:32px;">Gracias,<br>El equipo de {{ $appName }}</p>
                </div>
                <div class="footer">
                    © {{ $year }} {{ $appName }}. Todos los derechos reservados.
                    @if ($appUrl)
                        <br>
                        <a href="{{ $appUrl }}" style="color:#6b7280; text-decoration:none;">{{ $appUrl }}</a>
                    @endif
                </div>
            </div>
        </td></tr>
    </table>
    </div>
</body>
</html>
