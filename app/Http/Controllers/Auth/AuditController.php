<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Auth\AuditService;

/**
 * Controlador encargado de exponer los datos históricos de auditoría de inicio de sesión.
 *
 * @package App\Http\Controllers\Auth
 */
class AuditController extends Controller
{
    /**
     * @var AuditService Servicio para la gestión de auditoría de inicio de sesión.
     */
    protected $auditService;

    /**
     * Inicializa una nueva instancia de la clase.
     *
     * @param AuditService $auditService Servicio para la gestión de auditoría de inicio de sesión.
     */
    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Obtiene y retorna los registros históricos de auditoría de accesos.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el historial de accesos.
     */
    public function getHistoricalData()
    {
        $logs = $this->auditService->getHistoricalLoginData(10);
        return response()->json([
            'success' => true,
            'data' => $logs
        ], 200);
    }
}
