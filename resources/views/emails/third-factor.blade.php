<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de Acceso SINE</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; padding: 40px; margin: 0;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        
        <h2 style="color: #0f172a; margin-top: 0;">SINE Escolar 🎓</h2>
        <h3 style="color: #3b82f6;">Verificación de Tercer Factor</h3>
        
        <p style="color: #475569; line-height: 1.6;">
            Hola Administrador,<br><br>
            Has superado correctamente el primer y segundo factor de seguridad. Como medida de protección extra para cuentas con privilegios administrativos, necesitamos confirmar este inicio de sesión.
        </p>

        <p style="color: #475569;">Tu código de un solo uso es:</p>

        <div style="background-color: #f1f5f9; padding: 20px; border-radius: 8px; text-align: center; margin: 30px 0;">
            <span style="font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #1e293b;">
                {{ $code }}
            </span>
        </div>

        <p style="color: #64748b; font-size: 13px; line-height: 1.5;">
            * Este código expirará en 5 minutos.<br>
            * Si tú no solicitaste este código, alguien está intentando acceder a tu cuenta administrativa. Te recomendamos cambiar tu contraseña inmediatamente.
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">

        <p style="color: #94a3b8; font-size: 12px; text-align: center;">
            &copy; {{ date('Y') }} Sistema Integral de Negocios Escolares (SINE).<br>
            Este es un correo automático, por favor no respondas.
        </p>
    </div>
</body>
</html>
