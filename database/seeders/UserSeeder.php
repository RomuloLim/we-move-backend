<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'teste@exemplo.com',
            'name' => 'Usuário Teste',
            'password' => Hash::make('senha123'),
            'cpf' => '12345678900',
        ]);

        User::factory()->create([
            'email' => 'admin@exemplo.com',
            'name' => 'Administrador',
            'password' => Hash::make('senha123'),
            'cpf' => '12345678909',
            'user_type' => UserType::Admin,
        ]);
    }
}
