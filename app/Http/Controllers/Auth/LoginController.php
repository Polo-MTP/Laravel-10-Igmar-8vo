<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Services\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controlador encargado de gestionar las peticiones de inicio y cierre de sesión de usuarios.
 *
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /**
     * Inicializa una nueva instancia de la clase.
     *
     * @param LoginService $loginService Servicio para la lógica de inicio de sesión.
     */
    public function __construct(
        private LoginService $loginService
    ) {}

    /**
     * Procesa la solicitud de inicio de sesión de un usuario.
     *
     * @param LoginRequest $request Solicitud de validación de credenciales.
     * @return JsonResponse Respuesta JSON con el resultado del inicio de sesión.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        Log::debug('LoginController: Iniciando proceso de login', [
            'email' => $data['email'],
            'ip' => $request->ip()
        ]);

        $result = $this->loginService->login(
            $data, 
            $request->ip(), 
            $request->userAgent()
        );

        Log::debug('LoginController: Proceso de login terminado', [
            'email' => $data['email'],
            'result' => $result
        ]);

        return response()->json($result, $result['code']);
    }

    /**
     * Procesa la solicitud de cierre de sesión de un usuario.
     *
     * @param Request $request Solicitud HTTP actual.
     * @return JsonResponse Respuesta JSON indicando el éxito del cierre de sesión.
     */
    public function logout(Request $request): JsonResponse
    {
        Log::debug('LoginController: Iniciando proceso de logout', [
            'user_id' => $request->user()?->id
        ]);

        $result = $this->loginService->logout($request->user());

        Log::debug('LoginController: Proceso de logout terminado', [
            'user_id' => $request->user()?->id,
            'result' => $result
        ]);

        return response()->json($result, $result['code']);
    }
}
 