@extends('layouts.app')

@section('title', 'Login Seguro | Administración')

@section('content')
    <div style="margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
        <h1 id="welcomeMessage" style="margin: 0 0 5px 0; color: #0f172a; font-size: 24px;">Panel de Administración</h1>
        <p style="margin: 0; color: #64748b; font-size: 14px;">Auditoría de seguridad y logs de accesos del sistema.</p>
    </div>


    <!-- PANEL FORENSE (PISTAS DE AUDITORÍA) -->
    <div style="background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <h3 style="margin-top: 0; color: #1e293b; font-size: 16px;">Historial de Inicios de Sesión</h3>
        
        <div style="overflow-x: auto; margin-top: 15px; border: 1px solid #e2e8f0; border-radius: 6px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 12px 15px; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Fecha / Hora</th>
                        <th style="padding: 12px 15px; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Usuario</th>
                        <th style="padding: 12px 15px; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Dirección IP</th>
                        <th style="padding: 12px 15px; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Estado</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody">
                    <tr><td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Cargando registros...</td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; font-size: 13px;">
            <div id="paginationInfo" style="color: #64748b;">Mostrando 0 registros</div>
            <div style="display: flex; gap: 8px;">
                <button id="btnPrevPage" class="btn" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 6px 12px;" disabled>Anterior</button>
                <button id="btnNextPage" class="btn" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 6px 12px;" disabled>Siguiente</button>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
<script src="{{ asset('js/dashboard-admin.js') }}"></script>
@endsection
