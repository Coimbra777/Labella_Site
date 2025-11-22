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
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@labella.com',
            'password' => Hash::make('password'),
        ]);

        // Test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@labella.com',
            'password' => Hash::make('password'),
        ]);
    }
}
