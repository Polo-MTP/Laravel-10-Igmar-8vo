<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user() || ! $request->user()->role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No autorizado. Se requiere iniciar sesión.'
                ], 401);
            }
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role->name, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Acceso Denegado. Tu rol (' . $request->user()->role->name . ') no tiene privilegios para esta acción.'
                ], 403);
            }
            abort(403, 'Acceso Denegado. No tienes permisos para ver esta página.');
        }

        return $next($request);
    }
}
