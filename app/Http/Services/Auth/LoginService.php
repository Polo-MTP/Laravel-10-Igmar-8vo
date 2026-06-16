<?php

namespace App\Http\Services\Auth;

use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Servicio encargado de gestionar la lógica de inicio y cierre de sesión de usuarios.
 *
 * @package App\Http\Services\Auth
 */
class LoginService
{
    /**
     * Realiza el proceso de inicio de sesión validando credenciales y evaluando requerimientos de MFA.
     *
     * @param array $data Credenciales ingresadas por el usuario (email, password).
     * @param string $ipAddress Dirección IP desde donde se realiza la solicitud.
     * @param string|null $userAgent Agente de usuario que realiza la solicitud.
     * @return array Resultado de la operación con estado, mensaje y código HTTP.
     */
    public function login(array $data, string $ipAddress, ?string $userAgent): array
    {
        Log::debug('LoginService: Iniciando autenticación de usuario', [
            'email' => $data['email'],
            'ip' => $ipAddress
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            Log::debug('LoginService: Usuario no encontrado en la base de datos', [
                'email' => $data['email']
            ]);

            $this->guardarEnHistorial(null, $data['email'], $ipAddress, $userAgent, 'failed_user_not_found');
            return ['success' => false, 'message' => 'Credenciales incorrectas.', 'code' => 401];
        }

        if ($this->laCuentaEstaBloqueada($user)) {
            Log::debug('LoginService: Intento de acceso a cuenta bloqueada temporalmente', [
                'email' => $user->email,
                'locked_until' => $user->locked_until
            ]);

            $this->guardarEnHistorial($user->id, $user->email, $ipAddress, $userAgent, 'account_locked');
            return $this->generarMensajeDeBloqueo($user);
        }

        if (!Hash::check($data['password'], $user->password)) {
            $this->procesarContrasenaIncorrecta($user, $ipAddress, $userAgent);

            Log::debug('LoginService: Contraseña incorrecta ingresada', [
                'email' => $user->email,
                'failed_attempts' => $user->failed_attempts
            ]);

            return ['success' => false, 'message' => 'Credenciales incorrectas.', 'code' => 401];
        }

        return $this->procesarLoginExitoso($user, $ipAddress, $userAgent);
    }

    /**
     * Determina si la cuenta de un usuario se encuentra temporalmente bloqueada.
     *
     * @param User $user Instancia del modelo de usuario.
     * @return bool Verdadero si está bloqueada, falso en caso contrario.
     */
    private function laCuentaEstaBloqueada(User $user): bool
    {
        return $user->is_locked && $user->locked_until && $user->locked_until > now();
    }

    /**
     * Genera la estructura de respuesta cuando una cuenta está bloqueada.
     *
     * @param User $user Instancia del modelo de usuario.
     * @return array Estructura de respuesta de bloqueo de cuenta.
     */
    private function generarMensajeDeBloqueo(User $user): array
    {
        $minutosRestantes = now()->diffInMinutes($user->locked_until);
        return [
            'success' => false, 
            'message' => "Tu cuenta está bloqueada. Intenta de nuevo en {$minutosRestantes} minutos.", 
            'code' => 403
        ];
    }

    /**
     * Incrementa los intentos fallidos de contraseña y bloquea la cuenta si supera el límite de 5 intentos.
     *
     * @param User $user Instancia del modelo de usuario.
     * @param string $ipAddress Dirección IP de origen.
     * @param string|null $userAgent Identificador del agente de usuario.
     * @return void
     */
    private function procesarContrasenaIncorrecta(User $user, string $ipAddress, ?string $userAgent): void
    {
        $user->failed_attempts += 1;
        
        if ($user->failed_attempts >= 5) {
            $user->is_locked = true;
            $user->locked_until = now()->addMinutes(15);
            Log::debug('LoginService: La cuenta ha sido bloqueada por límite de intentos fallidos alcanzado', [
                'email' => $user->email,
                'failed_attempts' => $user->failed_attempts
            ]);
        }
        
        $user->save();
        $this->guardarEnHistorial($user->id, $user->email, $ipAddress, $userAgent, 'failed_password');
    }

    /**
     * Reinicia intentos fallidos y finaliza el inicio de sesión o inicia el flujo MFA según el rol.
     *
     * @param User $user Instancia del modelo de usuario.
     * @param string $ipAddress Dirección IP de origen.
     * @param string|null $userAgent Identificador del agente de usuario.
     * @return array Estructura de respuesta de inicio de sesión exitoso.
     */
    private function procesarLoginExitoso(User $user, string $ipAddress, ?string $userAgent): array
    {
        Log::debug('LoginService: Procesando login exitoso del primer factor', [
            'email' => $user->email
        ]);

        $user->failed_attempts = 0;
        $user->is_locked = false;
        $user->locked_until = null;
        $user->save();

        $this->guardarEnHistorial($user->id, $user->email, $ipAddress, $userAgent, 'success_factor_1');

        if ($user->role->factor_count > 1) {
            Log::debug('LoginService: Usuario requiere autenticación multifactor (MFA)', [
                'email' => $user->email,
                'factor_count' => $user->role->factor_count
            ]);

            return $this->generarRetoMfa($user);
        }

        Log::debug('LoginService: Login de factor único exitoso y completado', [
            'email' => $user->email
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return [
            'success' => true,
            'message' => 'Login exitoso',
            'user' => $user->load('role'),
            'code' => 200
        ];
    }

    /**
     * Genera el reto para el segundo factor de autenticación (TOTP).
     *
     * @param User $user Instancia del modelo de usuario.
     * @return array Respuesta de requerimiento de MFA con detalles o URL firmada para configuración.
     */
    private function generarRetoMfa(User $user): array
    {
        Log::debug('LoginService: Generando reto MFA para el usuario', [
            'email' => $user->email
        ]);

        $mfaMethod = $user->mfaMethods()
            ->whereHas('type', function($q) {
                $q->where('type', 'totp');
            })
            ->where('factor_step', 2)
            ->where('is_active', true)
            ->first();


        if (!$mfaMethod) {
            Log::debug('LoginService: MFA no configurado para el usuario. Generando URL de configuración firmada', [
                'email' => $user->email
            ]);

            $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'mfa.setup', now()->addMinutes(10), ['email' => $user->email]
            );

            return [
                'success' => true,
                'requires_setup' => true,
                'setup_url' => $signedUrl,
                'message' => 'MFA no configurado.',
                'code' => 200
            ];
        }

        Log::debug('LoginService: Reto MFA TOTP generado', [
            'email' => $user->email,
            'mfa_method_id' => $mfaMethod->id
        ]);

        return [
            'success' => true,
            'requires_mfa' => true,
            'step' => 2,
            'mfa_method_id' => $mfaMethod->id,
            'message' => 'Contraseña correcta. Ingresa el código de tu app de autenticación.',
            'code' => 200
        ];
    }

    /**
     * Registra los intentos de inicio de sesión en el historial de auditoría.
     *
     * @param int|null $userId Identificador del usuario o nulo si no se encontró.
     * @param string $email Correo de la cuenta de acceso.
     * @param string $ipAddress Dirección IP de origen.
     * @param string|null $userAgent Identificador del agente de usuario.
     * @param string $status Estado del intento de inicio de sesión.
     * @return void
     */
    private function guardarEnHistorial(?int $userId, string $email, string $ipAddress, ?string $userAgent, string $status): void
    {
        LoginAttempt::record($userId, $email, $status, 1);
    }

    /**
     * Realiza el cierre de sesión, borrando sesiones y destruyendo tokens.
     *
     * @param User|null $user Instancia del usuario actual.
     * @return array Respuesta con el estado y mensaje de éxito del cierre de sesión.
     */
    public function logout(?User $user): array
    {
        Log::debug('LoginService: Iniciando proceso de cierre de sesión', [
            'user_id' => $user?->id,
            'email' => $user?->email
        ]);

        if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        \Illuminate\Support\Facades\Auth::logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        Log::debug('LoginService: Sesión cerrada exitosamente', [
            'user_id' => $user?->id
        ]);

        return [
            'success' => true,
            'message' => 'Sesión cerrada exitosamente.',
            'code' => 200
        ];
    }
}
