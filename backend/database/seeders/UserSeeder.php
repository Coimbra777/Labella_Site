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
     *
     * Em produção este seeder não cria usuários padrão (evita admin@… / password em banco real).
     * Crie administradores com `php artisan labella:make-super-admin` ou fluxo auditado.
     */
    public function run(): void
    {
        if (config('app.env') === 'production') {
            $this->command?->warn(
                'UserSeeder: ignorado em APP_ENV=production. Crie o admin manualmente (veja backend/README.md).',
            );

            return;
        }

        // Admin user (Filament exige is_admin) — apenas dev/staging/testing
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
