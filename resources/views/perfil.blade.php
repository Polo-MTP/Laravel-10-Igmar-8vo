@extends('layouts.app')

@section('title', 'SINE | Mi Perfil')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
        <h1 style="margin: 0; color: #1e293b;">Mi Perfil</h1>
        <button onclick="window.history.back()" class="btn" style="width: auto; padding: 8px 15px; background: #64748b; font-size: 14px;">Volver</button>
    </div>
    
    <p style="color: #475569;">Esta es una vista puramente informativa sobre los datos de tu cuenta.</p>
    
    <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #cbd5e1; margin-top: 20px;">
        <h3 style="margin-top: 0; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Información Personal</h3>
        
        <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
            <div>
                <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Nombre Completo</strong>
                <div id="profileName" style="font-size: 18px; font-weight: bold; color: #0f172a;">Cargando...</div>
            </div>
            
            <div>
                <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Correo Electrónico</strong>
                <div id="profileEmail" style="font-size: 16px; color: #334155;">Cargando...</div>
            </div>
            
            <div>
                <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Estado de la Cuenta</strong>
                <div id="profileStatus" style="font-size: 16px; color: #334155;">Cargando...</div>
            </div>
            
            <div>
                <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Fecha de Creación</strong>
                <div id="profileCreated" style="font-size: 16px; color: #334155;">Cargando...</div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
<script src="{{ asset('js/perfil.js') }}"></script>
@endsection
