<?php

namespace App\Http\Requests\Auth;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => ['required', 'string', 'min:8', 'confirmed', new \App\Rules\SecurePassword()],
            'recaptcha' => ['required', 'string', new Recaptcha()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'name'  => $this->name ? ucwords(strtolower(trim($this->name))) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser un correo válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'recaptcha.required' => 'La verificación anti-robots (reCAPTCHA) es obligatoria.',
            'recaptcha.string' => 'La verificación anti-robots (reCAPTCHA) es inválida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}
