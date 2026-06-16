<?php

namespace App\Http\Services\Auth;

use App\Models\LoginAttempt;

/**
 * Servicio encargado de la consulta e historial forense de auditoría de seguridad y accesos.
 *
 * @package App\Http\Services\Auth
 */
class AuditService
{
    /**
     * Obtiene los datos paginados de auditoría de intentos de inicio de sesión.
     *
     * @param int $perPage Cantidad de registros por página.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Registros paginados con datos de usuario.
     */
    public function getHistoricalLoginData($perPage = 10)
    {
        return LoginAttempt::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
