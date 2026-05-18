<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeSuperAdmin extends Command
{
    protected $signature = 'labella:make-super-admin
                            {--name= : Nome do usuário}
                            {--email= : E-mail do usuário}
                            {--password= : Senha (mín. 8 caracteres)}';

    protected $description = 'Cria um super usuário para o painel admin';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Nome');
        $email = $this->option('email') ?? $this->ask('E-mail');
        $password = $this->option('password') ?? $this->secret('Senha (mín. 8 caracteres)');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
            ],
            [
                'name.required' => 'O nome é obrigatório.',
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Informe um e-mail válido.',
                'email.unique' => 'Este e-mail já está em uso.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info("Super usuário criado com sucesso!");
        $this->line("E-mail: {$email}");
        $this->line("Acesse o painel em: " . config('app.url') . '/admin');

        return self::SUCCESS;
    }
}
