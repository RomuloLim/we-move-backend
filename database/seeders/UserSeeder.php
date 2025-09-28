<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creates test user
        User::firstOrCreate(
            ['email' => 'teste@exemplo.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('senha123'),
                'email_verified_at' => now(),
            ]
        );

        // Creates admin user
        User::firstOrCreate(
            ['email' => 'admin@exemplo.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
