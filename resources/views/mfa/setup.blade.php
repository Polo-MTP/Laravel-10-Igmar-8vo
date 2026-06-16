<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Google Authenticator</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/mfa-setup.css') }}">
</head>
<body>
    <div class="card">
        <h2>Seguridad Nivel Senior 🛡️</h2>
        <p>Usuario: <strong>{{ $email }}</strong></p>
        <p>Abre <b>Google Authenticator</b> en tu celular y escanea este código QR:</p>
        
        <div class="qr-box">
            {!! $qrImage !!}
        </div>

        <p>Si estás desde tu celular, ingresa esta llave secreta manualmente:</p>
        <div class="secret">{{ $secretKey }}</div>

        <div style="margin-top: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <p style="margin-top: 0; font-weight: bold; color: #334155;">Paso 2: Verifica tu dispositivo</p>
            <p style="font-size: 14px; margin-bottom: 10px;">Ingresa el código de 6 dígitos para continuar:</p>
            
            <input type="text" id="verify_code" maxlength="6" placeholder="123456" 
                   style="text-align: center; font-size: 24px; letter-spacing: 8px; font-family: monospace; padding: 10px; width: 150px; border: 2px solid #cbd5e1; border-radius: 6px; outline: none;">
            
            <div id="status_msg" style="margin-top: 10px; font-size: 14px; font-weight: bold; height: 20px;"></div>
        </div>
    </div>

    <script>
        const mfaMethodId = @json($mfaMethodId);
    </script>
    <script src="{{ asset('js/mfa-setup.js') }}"></script>
</body>
</html>
