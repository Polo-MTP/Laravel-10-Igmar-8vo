<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrador')->first();
        $userRole = Role::where('name', 'Usuario')->first();

        User::firstOrCreate(
            ['email' => 'trejomisaelperez2304@gmail.com'],
            [
                'name' => 'Misael Trejo',
                'password' => Hash::make('8Yro|U_WZi4.39Nny'),
                'role_id' => $adminRole->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'usuario@correo.com'],
            [
                'name' => 'Usuario Normal',
                'password' => Hash::make('501d[qP*r#e2T[bU'),
                'role_id' => $userRole->id,
            ]
        );
    }
}
