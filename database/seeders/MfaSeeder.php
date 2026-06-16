<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MfaType;
use App\Models\User;
use App\Models\Role;
use App\Models\MfaMethod;
use Illuminate\Support\Facades\Hash;

class MfaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el tipo de MFA "Código por Correo" en el catálogo global
        MfaType::firstOrCreate(
            ['name' => 'Email OTP'],
            ['type' => 'email', 'is_active' => true]
        );
        
        // 2. Crear el tipo de MFA "Aplicación Autenticadora (TOTP)" en el catálogo global
        MfaType::firstOrCreate(
            ['name' => 'Google Authenticator'],
            ['type' => 'totp', 'is_active' => true]
        );
    }
}
