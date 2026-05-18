<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Senha em texto plano: o cast `hashed` em {@see User} aplica Hash::make ao persistir.
     * Não use Hash::make aqui (senão o valor é hasheado duas vezes e o login falha).
     */
    public function run(): void
    {
        // Admin user (Filament exige is_admin)
        User::create([
            'name' => 'Admin',
            'email' => 'admin@labella.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        // Usuário comum (sem acesso ao painel)
        User::create([
            'name' => 'Test User',
            'email' => 'test@labella.com',
            'password' => 'password',
            'is_admin' => false,
        ]);
    }
}
