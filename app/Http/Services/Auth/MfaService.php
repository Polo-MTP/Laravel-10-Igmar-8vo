<?php

namespace App\Http\Services\Auth;

use App\Models\MfaVerification;
use App\Models\MfaMethod;
use App\Models\User;
use App\Models\MfaType;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Log;

/**
 * Servicio encargado de gestionar los flujos de verificación y configuración de seguridad multifactor (MFA).
 *
 * @package App\Http\Services\Auth
 */
class MfaService
{
    /**
     * Verifica la validez de un código MFA y procesa el paso siguiente (3FA o login final).
     *
     * @param array $data Parámetros de validación (mfa_method_id, code).
     * @return array Resultado de la verificación, indicando el paso a seguir o estado exitoso.
     */
    public function verify(array $data): array
    {
        Log::debug('MfaService: Iniciando validación de código de segundo factor', [
            'mfa_method_id' => $data['mfa_method_id']
        ]);

        $mfaMethod = MfaMethod::find($data['mfa_method_id']);
        
        if (!$mfaMethod) {
            Log::debug('MfaService: Método MFA no encontrado en la base de datos', [
                'mfa_method_id' => $data['mfa_method_id']
            ]);

            return ['success' => false, 'message' => 'Hubo un problema con la configuración de tu seguridad. Por favor, contacta a soporte técnico.', 'code' => 400];
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $isValid = $google2fa->verifyKey($mfaMethod->secret, $data['code']);

        if (!$isValid) {
            Log::debug('MfaService: Código MFA incorrecto ingresado por el usuario', [
                'mfa_method_id' => $data['mfa_method_id'],
                'user_id' => $mfaMethod->user_id
            ]);

            \App\Models\LoginAttempt::record($mfaMethod->user_id, $mfaMethod->user->email, 'failed_mfa', 2, 'Código de Authenticator App incorrecto.');
            return ['success' => false, 'message' => 'El código de la App es incorrecto.', 'code' => 401];
        }

        if (!$mfaMethod->is_verified) {
            Log::debug('MfaService: Marcando método de MFA como verificado', [
                'mfa_method_id' => $mfaMethod->id
            ]);

            $mfaMethod->is_verified = true;
            $mfaMethod->save();
        }

        $user = $mfaMethod->user;

        Log::debug('MfaService: Código MFA validado correctamente. Evaluando necesidad de tercer factor', [
            'user_id' => $user->id,
            'factor_count' => $user->role->factor_count
        ]);

        if ($user->role->factor_count > 2) {
            Log::debug('MfaService: Generando y enviando código OTP para el tercer factor', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            $otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            \Illuminate\Support\Facades\Cache::put('email_otp_' . $user->id, $otpCode, 300);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ThirdFactorMail($otpCode));
            } catch (\Exception $e) {
                Log::debug('MfaService: Error al enviar email de 3er factor', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);

                \Illuminate\Support\Facades\Log::error("Error al enviar email de 3er factor: " . $e->getMessage());
                \App\Models\LoginAttempt::record($user->id, $user->email, 'failed_mfa_mail_error', 2, 'Error enviando correo de tercer factor: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Ocurrió un error en el servidor al intentar enviar el código a su correo electrónico. Por favor, verifique su dirección o intente más tarde.',
                    'code' => 500
                ];
            }

            \App\Models\LoginAttempt::record($user->id, $user->email, 'success_factor_2', 2, 'Segundo factor exitoso. Requiere correo.');

            return [
                'success' => true,
                'requires_email_otp' => true,
                'user_id' => $user->id,
                'message' => 'Por favor revisa tu correo. Hemos enviado un código de 6 dígitos.',
                'code' => 200
            ];
        }

        Log::debug('MfaService: Segundo factor completado exitosamente. Autenticando usuario', [
            'user_id' => $user->id
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        \App\Models\LoginAttempt::record($user->id, $user->email, 'success_factor_2', 2, 'Segundo factor exitoso. Autenticado.');

        return [
            'success' => true,
            'message' => 'Verificación de Segundo Factor exitosa.',
            'user' => $user->load('role'),
            'code' => 200
        ];
    }

    /**
     * Genera los parámetros de configuración iniciales para registrar Google Authenticator.
     *
     * @param string $email Correo electrónico del usuario que configurará MFA.
     * @return array Datos generados (email, secretKey, qrCodeUrl, mfa_method_id).
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Si el usuario ya tiene MFA verificado.
     */
    public function generateSetupData(string $email): array
    {
        Log::debug('MfaService: Iniciando generación de datos para configurar MFA', [
            'email' => $email
        ]);

        $user = User::where('email', $email)->firstOrFail();
        
        $totpMfa = MfaType::firstOrCreate(
            ['name' => 'Authenticator App'],
            ['type' => 'totp', 'is_active' => true]
        );

        $existingMethod = MfaMethod::where('user_id', $user->id)
            ->where('mfa_type_id', $totpMfa->id)
            ->first();

        if ($existingMethod && $existingMethod->is_verified) {
            Log::debug('MfaService: Intento fallido de configurar MFA. El usuario ya cuenta con un dispositivo vinculado', [
                'email' => $email,
                'user_id' => $user->id
            ]);

            abort(403, 'El usuario ya tiene un dispositivo vinculado. Para generar uno nuevo, un administrador debe revocar el dispositivo anterior.');
        }

        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        $method = MfaMethod::updateOrCreate(
            ['user_id' => $user->id, 'mfa_type_id' => $totpMfa->id],
            [
                'secret' => $secretKey,
                'factor_step' => 2,
                'is_verified' => false,
                'is_active' => true,
            ]
        );
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'Sistema SINE',
            $user->email,
            $secretKey
        );

        Log::debug('MfaService: Datos de QR y llave secreta generados correctamente', [
            'email' => $email,
            'mfa_method_id' => $method->id
        ]);

        return [
            'email' => $user->email,
            'secretKey' => $secretKey,
            'qrCodeUrl' => $qrCodeUrl,
            'mfa_method_id' => $method->id
        ];
    }
}
