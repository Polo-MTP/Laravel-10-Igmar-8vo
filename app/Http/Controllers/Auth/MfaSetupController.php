<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Auth\MfaService;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;

/**
 * Controlador encargado de gestionar la configuración inicial del doble factor de autenticación (MFA) vía TOTP.
 *
 * @package App\Http\Controllers\Auth
 */
class MfaSetupController extends Controller
{
    /**
     * Inicializa una nueva instancia de la clase.
     *
     * @param MfaService $mfaService Servicio para la gestión de multifactor de autenticación.
     */
    public function __construct(
        private MfaService $mfaService
    ) {}

    /**
     * Muestra la vista de configuración con el código QR y la llave secreta para registrar Google Authenticator.
     *
     * @param string $email Correo electrónico del usuario que configurará MFA.
     * @return \Illuminate\View\View Vista Blade de configuración de MFA.
     */
    public function showSetupForm(string $email)
    {
        Log::debug('MfaSetupController: Mostrando formulario de configuración MFA', [
            'email' => $email
        ]);

        $setupData = $this->mfaService->generateSetupData($email);

        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrImage = $writer->writeString($setupData['qrCodeUrl']);

        Log::debug('MfaSetupController: Formulario de configuración MFA generado', [
            'email' => $email,
            'mfa_method_id' => $setupData['mfa_method_id']
        ]);

        return view('mfa.setup', [
            'qrImage' => $qrImage,
            'secretKey' => $setupData['secretKey'],
            'email' => $setupData['email'],
            'mfaMethodId' => $setupData['mfa_method_id']
        ]);
    }

    /**
     * Confirma y valida el primer código ingresado por el usuario para activar la configuración de MFA.
     *
     * @param \Illuminate\Http\Request $request Solicitud HTTP con la clave secreta y el código de confirmación.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la confirmación.
     */
    public function confirmSetup(\Illuminate\Http\Request $request)
    {
        Log::debug('MfaSetupController: Confirmando configuración MFA', [
            'mfa_method_id' => $request->mfa_method_id,
            'code' => $request->code
        ]);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'mfa_method_id' => 'required|string|exists:mfa_methods,id',
            'code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            Log::debug('MfaSetupController: Validación de confirmación fallida', [
                'errors' => $validator->errors()->toArray()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'El formato del código es inválido o la sesión expiró.',
                'errors' => $validator->errors()
            ], 422);
        }

        $method = \App\Models\MfaMethod::findOrFail($request->mfa_method_id);
        
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $isValid = $google2fa->verifyKey($method->secret, $request->code);

        if ($isValid) {
            Log::debug('MfaSetupController: Verificación exitosa de llave MFA', [
                'mfa_method_id' => $request->mfa_method_id
            ]);

            $method->is_verified = true;
            $method->save();
            return response()->json(['success' => true]);
        }

        Log::debug('MfaSetupController: Verificación fallida de llave MFA (código incorrecto)', [
            'mfa_method_id' => $request->mfa_method_id
        ]);

        return response()->json([
            'success' => false, 
            'message' => 'Código incorrecto. Intenta de nuevo.'
        ], 400);
    }
}
