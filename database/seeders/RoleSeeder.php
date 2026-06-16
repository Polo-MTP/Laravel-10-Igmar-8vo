<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Administrador',
            'factor_count' => 3
        ]);
        Role::create([
            'name' => 'Usuario',
            'factor_count' => 2
        ]);
        Role::create([
            'name' => 'Invitado',
            'factor_count' => 1
        ]);
    }
}
