<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Cuenta | Login Seguro</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY', '6LfHwwctAAAAALHKhTXYSHFwLqpSQ2_1yUp8tOkq') }}"></script>
</head>
<body>

    <div class="register-container fade-in">
        <div class="logo">Login Seguro<br><span>Crear cuenta nueva</span></div>

        <div id="alertBox" class="alert hidden"></div>

        <form id="registerForm" method="POST" action="/api/register" novalidate>
            @csrf
            <div class="form-group">
                <div id="nameError" class="error-message">Falta el nombre</div>
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" placeholder="Juan Pérez">
            </div>

            <div class="form-group">
                <div id="emailError" class="error-message">Correo inválido</div>
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" placeholder="tu@correo.com">
            </div>

            <div class="form-group">
                <div id="passwordError" class="error-message">Contraseña inválida</div>
                <label for="password">Contraseña Segura</label>
                <input type="password" id="password" placeholder="••••••••">
                <span class="helper-text">Debe incluir Mayúscula, Minúscula, Número y Carácter Especial.</span>
            </div>

            <div class="form-group">
                <div id="passwordConfirmationError" class="error-message">Las contrasenas no coinciden</div>
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn" id="registerBtn">Crear Cuenta</button>

            <a href="/login" class="auth-link">¿Ya tienes cuenta? Inicia Sesión</a>
        </form>
    </div>

    <script>
        const recaptchaSiteKey = "{{ env('RECAPTCHA_SITE_KEY', '6LfHwwctAAAAALHKhTXYSHFwLqpSQ2_1yUp8tOkq') }}";
    </script>
    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>
