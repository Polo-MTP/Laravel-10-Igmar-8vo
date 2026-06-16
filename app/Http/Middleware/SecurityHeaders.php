<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Previene ataques de Clickjacking (que otra web meta tu sistema en un iFrame)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Previene ataques de MIME Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Habilita el filtro XSS nativo del navegador
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controla cuánta información se envía a otros sitios cuando das clic en un link
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // CSP (Content Security Policy): La defensa definitiva contra XSS.
        // Bloquea cualquier script, imagen o recurso que intente cargar desde un dominio no autorizado.
        $response->headers->set('Content-Security-Policy', "default-src 'self'; connect-src 'self' https://www.google.com/recaptcha/; script-src 'self' 'unsafe-inline' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/; style-src 'self' 'unsafe-inline'; frame-src https://www.google.com/recaptcha/ https://www.google.com/recaptcha/api2/");

        // Fuerza a que el navegador siempre use HTTPS (Solo en producción)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
