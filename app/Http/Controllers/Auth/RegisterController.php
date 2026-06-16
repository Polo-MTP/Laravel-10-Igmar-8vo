<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controlador encargado de registrar nuevos usuarios en el sistema.
 *
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /**
     * Inicializa una nueva instancia de la clase.
     *
     * @param RegisterService $registerService Servicio para el registro de usuarios.
     */
    public function __construct(
        private RegisterService $registerService
    ) {}

    /**
     * Registra un nuevo usuario en la base de datos con el rol de Invitado.
     *
     * @param RegisterRequest $request Solicitud de validación de datos de registro.
     * @return JsonResponse Respuesta JSON con los datos del usuario registrado.
     */
    public function store(RegisterRequest $request): JsonResponse{
        $data = $request->validated();

        Log::debug('RegisterController: Iniciando registro de usuario', [
            'email' => $data['email']
        ]);

        $result = $this->registerService->register($data);

        Log::debug('RegisterController: Registro de usuario terminado exitosamente', [
            'email' => $data['email'],
            'result' => $result
        ]);

        return response()->json($result, 201);
    }
}

