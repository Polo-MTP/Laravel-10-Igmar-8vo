<?php

namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio encargado de la lógica de creación y registro de nuevos usuarios en el sistema.
 *
 * @package App\Http\Services\Auth
 */
class RegisterService
{
    /**
     * Registra un nuevo usuario en el sistema asociándole por defecto el rol de Invitado.
     *
     * @param array $data Datos de registro del usuario (name, email, password).
     * @return array Resultado de la operación con estado y datos del usuario registrado.
     */
    public function register(array $data): array
    {
        Log::debug('RegisterService: Iniciando registro de nuevo usuario', [
            'email' => $data['email']
        ]);

        $user = DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $invitadoRole = Role::where('name', 'Invitado')->firstOrFail();
            
            if (!$invitadoRole) {
                Log::debug('RegisterService: Error, el rol de Invitado no existe en la BD', [
                    'email' => $data['email']
                ]);
                abort(500, "El rol de Invitado no existe en la base de datos.");
            }

            Log::debug('RegisterService: Asignando rol de Invitado', [
                'email' => $data['email'],
                'role_id' => $invitadoRole->id
            ]);

            $data['role_id'] = $invitadoRole->id;

            return User::create($data);
        });

        Log::debug('RegisterService: Registro de nuevo usuario completado exitosamente', [
            'email' => $user->email,
            'user_id' => $user->id
        ]);

        return [
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
        ];
    }
}
