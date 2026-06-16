<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http; // Importante para hacer la petición a Google

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //quitar el withoutVerifying() si  dejas de usar localhost, osea si usas un dominio real
        $response = Http::asForm()->withoutVerifying()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $value, // El código que nos mandó el Frontend
        ]);

        // 2. Google nos devuelve un JSON. Verificamos si el campo 'success' es true.
        $body = $response->json();
        
        if (!isset($body['success']) || !$body['success']) {
            // Si es falso el token expiró o es inválido
            $fail('La verificación anti-robots (reCAPTCHA) ha fallado. Intenta de nuevo.');
            return;
        }

        // 3. Verificamos el score de v3 (0.0 a 1.0). 
        // Normalmente >= 0.5 se considera humano.
        if (isset($body['score']) && $body['score'] < 0.5) {
            $fail('Se detectó comportamiento inusual. No has pasado la prueba de reCAPTCHA.');
        }
    }
}
