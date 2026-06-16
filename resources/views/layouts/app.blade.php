<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login Seguro | Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/layout-app.css') }}">
    @yield('extra-css')
</head>
<body>

    <div id="app" style="width: 100%; display: flex; flex-direction: column; align-items: center;">
        <div class="navbar">
            <h2>Login Seguro</h2>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="/perfil" class="btn btn-primary">Mi Perfil</a>
                <button id="logoutBtn" class="btn btn-logout">Cerrar Sesión</button>
            </div>
        </div>

        <div class="container">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('js/layout-app.js') }}"></script>
    @yield('extra-js')

</body>
</html>
