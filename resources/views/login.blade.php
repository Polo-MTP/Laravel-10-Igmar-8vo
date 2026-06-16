<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión | Login Seguro</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY', '6LfHwwctAAAAALHKhTXYSHFwLqpSQ2_1yUp8tOkq') }}"></script>
</head>
<body>

    <div class="login-container" id="app">
        <div class="logo">Login Seguro</div>

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
            <div id="alertBox" class="alert hidden"></div>
        @elseif ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
            <div id="alertBox" class="alert hidden"></div>
        @else
            <div id="alertBox" class="alert hidden"></div>
        @endif

        <!-- PASO 1: Formulario de Login -->
        <form id="loginForm" class="fade-in" method="POST" action="/api/login" novalidate>
            @csrf
            <div class="form-group">
                <div id="emailError" class="error-message">Correo requerido</div>
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" placeholder="tu@correo.com">
            </div>
            <div class="form-group">
                <div id="passwordError" class="error-message">Contraseña requerida</div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn" id="loginBtn">Iniciar Sesión</button>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/register" style="color: #2563eb; text-decoration: none; font-size: 14px;">¿No tienes cuenta? Regístrate aquí</a>
            </div>
        </form>

        <!-- PASO 2: Formulario de Google Authenticator (Oculto) -->
        <form id="mfaForm" class="hidden" method="POST" action="/api/mfa/verify" novalidate>
            @csrf
            <p style="text-align: center; color: #64748b; font-size: 14px; margin-bottom: 20px;">
                Protección activada. Ingresa el código de 6 dígitos de Google Authenticator.
            </p>
            <div class="form-group">
                <div id="mfaError" class="error-message">Código inválido</div>
                <label for="mfa_code">Código de Seguridad</label>
                <input type="text" id="mfa_code" placeholder="123456" style="text-align: center; font-size: 24px; letter-spacing: 8px; font-family: monospace;">
            </div>
            <button type="submit" class="btn" id="mfaBtn">Verificar Identidad</button>
        </form>

        <!-- PASO 3: Formulario Tercer Factor por Correo (Oculto) -->
        <form id="emailOtpForm" class="hidden" method="POST" action="/api/mfa/email/verify" novalidate>
            @csrf
            <p style="text-align: center; color: #64748b; font-size: 14px; margin-bottom: 20px;">
                Paso 3: Máxima Seguridad. Te hemos enviado un código de 6 dígitos a tu correo.
            </p>
            <div class="form-group">
                <div id="emailOtpError" class="error-message">Código inválido</div>
                <label for="email_code">Código del Correo</label>
                <input type="text" id="email_code" placeholder="123456" style="text-align: center; font-size: 24px; letter-spacing: 8px; font-family: monospace;">
            </div>
            <button type="submit" class="btn" id="emailOtpBtn">Finalizar Inicio de Sesión</button>
        </form>
    </div>

    <script>
        const recaptchaSiteKey = "{{ env('RECAPTCHA_SITE_KEY', '6LfHwwctAAAAALHKhTXYSHFwLqpSQ2_1yUp8tOkq') }}";
    </script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
