@extends('layouts.app')

@section('title', 'Login Seguro | Mi Cuenta')

@section('content')
    <div style="margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
        <h1 id="welcomeMessage" style="margin: 0 0 5px 0; color: #0f172a; font-size: 24px;">Centro de Seguridad</h1>
        <p style="margin: 0; color: #64748b; font-size: 14px;">Administra tu información personal y los ajustes de tu cuenta.</p>
    </div>
    
    <div style="display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap;">
        <!-- MOCKUP 1: Estado de cuenta -->
        <div style="flex: 1; min-width: 200px; background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 4px solid #10b981;">
            <h3 style="margin-top: 0; color: #1e293b; font-size: 15px;">Estado de Cuenta</h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 10px;">Tu cuenta está activa y protegida.</p>
            <div style="display: inline-block; padding: 3px 6px; background: #f0fdf4; color: #166534; border-radius: 4px; font-size: 11px; font-weight: 500; border: 1px solid #bbf7d0;">2 Factores Activos</div>
        </div>

        <!-- MOCKUP 2: Dispositivos -->
        <div style="flex: 1; min-width: 200px; background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6;">
            <h3 style="margin-top: 0; color: #1e293b; font-size: 15px;">Sesiones Activas</h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 10px;">Conexión actual desde Windows.</p>
            <a href="#" style="color: #2563eb; font-size: 12px; text-decoration: none;">Administrar sesiones &rarr;</a>
        </div>

        <!-- MOCKUP 3: Historial -->
        <div style="flex: 1; min-width: 200px; background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 4px solid #8b5cf6;">
            <h3 style="margin-top: 0; color: #1e293b; font-size: 15px;">Última Actividad</h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 10px;">MFA validado hace unos momentos.</p>
            <a href="#" style="color: #8b5cf6; font-size: 12px; text-decoration: none;">Ver registro completo &rarr;</a>
        </div>

        <!-- MOCKUP 4: Llaves físicas -->
        <div style="flex: 1; min-width: 200px; background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; border-left: 4px solid #f97316;">
            <h3 style="margin-top: 0; color: #1e293b; font-size: 15px;">Llaves de Seguridad</h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 10px;">Vincula llaves FIDO2 / YubiKey.</p>
            <a href="#" style="color: #f97316; font-size: 12px; text-decoration: none;">Configurar llaves &rarr;</a>
        </div>
    </div>

    <div style="background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <h3 style="margin-top: 0; color: #1e293b; font-size: 16px;">Mi Información de Sesión</h3>
        <div id="apiData" class="data-box">Cargando...</div>
    </div>
@endsection

@section('extra-js')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
