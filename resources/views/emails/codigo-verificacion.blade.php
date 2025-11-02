<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            color: #333333;
        }
        .content p {
            line-height: 1.6;
            margin: 0 0 15px;
            font-size: 16px;
        }
        .code-box {
            background-color: #f0f9ff;
            border: 2px dashed #3b82f6;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: 700;
            color: #1e40af;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .expiry {
            color: #ef4444;
            font-size: 14px;
            margin-top: 15px;
            font-weight: 600;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Código de Verificación</h1>
        </div>
        
        <div class="content">
            <p>Hola,</p>
            <p>Has solicitado verificar tu dirección de correo electrónico <strong>{{ $email }}</strong>.</p>
            <p>Por favor, utiliza el siguiente código de verificación:</p>
            
            <div class="code-box">
                <div class="code">{{ $codigo }}</div>
                <div class="expiry">⏰ Este código expira en 5 minutos</div>
            </div>
            
            <p>Ingresa este código en el formulario para completar la verificación de tu email.</p>
            
            <div class="warning">
                <p><strong>⚠️ Importante:</strong> Si no solicitaste este código, puedes ignorar este mensaje de forma segura. Tu cuenta permanece protegida.</p>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>SIGSIH - Sistema Integral de Gestión de Servicios Industriales de Hardlan</strong></p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Hardlan. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
