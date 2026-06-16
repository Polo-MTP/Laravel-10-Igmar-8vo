<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyMfaRequest;
use App\Http\Services\Auth\MfaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controlador encargado de gestionar la verificación del segundo y tercer factor de autenticación de los usuarios.
 *
 * @package App\Http\Controllers\Auth
 */
class MfaController extends Controller
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
     * Verifica el código ingresado para el segundo factor de autenticación (TOTP).
     *
     * @param VerifyMfaRequest $request Solicitud con el código MFA y el identificador del método.
     * @return JsonResponse Respuesta JSON con el resultado de la verificación del segundo factor.
     */
    public function verify(VerifyMfaRequest $request): JsonResponse
    {
        $data = $request->validated();

        Log::debug('MfaController: Iniciando verificación de código MFA', [
            'mfa_method_id' => $data['mfa_method_id']
        ]);
        
        $result = $this->mfaService->verify($data);

        Log::debug('MfaController: Verificación de código MFA terminada', [
            'mfa_method_id' => $data['mfa_method_id'],
            'result' => $result
        ]);

        return response()->json($result, $result['code']);
    }

    /**
     * Verifica el código OTP enviado al correo electrónico para el tercer factor de autenticación.
     *
     * @param \Illuminate\Http\Request $request Solicitud HTTP con el código y el identificador de usuario.
     * @param \App\Http\Services\Auth\EmailOtpService $emailOtpService Servicio para la lógica de OTP de correo.
     * @return JsonResponse Respuesta JSON con el resultado de la verificación del tercer factor.
     */
    public function verifyEmailOtp(\Illuminate\Http\Request $request, \App\Http\Services\Auth\EmailOtpService $emailOtpService): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'code' => 'required|string|size:6'
        ]);

        Log::debug('MfaController: Iniciando verificación de código OTP por correo', [
            'user_id' => $data['user_id']
        ]);

        $result = $emailOtpService->verify($data);

        Log::debug('MfaController: Verificación de código OTP por correo terminada', [
            'user_id' => $data['user_id'],
            'result' => $result
        ]);

        return response()->json($result, $result['code']);
    }
}
