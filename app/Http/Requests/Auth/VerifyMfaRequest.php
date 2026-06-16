<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMfaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Exigimos que el id sea exactamente un formato UUID (súper seguro)
            'mfa_method_id' => 'required|uuid',
            // Exigimos que el código sean exactamente 6 números
            'code' => 'required|numeric|digits:6',
        ];
    }

    public function messages(): array
    {
        return [
            'mfa_method_id.required' => 'El ID del método es requerido.',
            'mfa_method_id.uuid' => 'El ID del método ha sido manipulado.',
            'code.required' => 'El código es obligatorio.',
            'code.numeric' => 'El código solo debe contener números.',
            'code.digits' => 'El código debe tener exactamente 6 dígitos.',
        ];
    }
}
