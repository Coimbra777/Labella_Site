<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class UserSeederEnvironmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_creates_default_users_outside_production(): void
    {
        $this->seed(UserSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@labella.com']);
        $this->assertDatabaseHas('users', ['email' => 'test@labella.com']);
    }

    public function test_user_seeder_does_not_create_users_when_app_env_is_production(): void
    {
        Config::set(['app.env' => 'production']);

        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount('users', 0);
    }
}
