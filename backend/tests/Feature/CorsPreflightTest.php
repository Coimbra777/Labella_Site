<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorsPreflightTest extends TestCase
{
    use RefreshDatabase;

    public function test_options_preflight_allows_configured_local_vite_origin_for_api(): void
    {
        $response = $this->call('OPTIONS', '/api/v1/categories', [], [], [], [
            'HTTP_ORIGIN' => 'http://localhost:5173',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
        ]);

        $response->assertSuccessful();
        $this->assertSame(
            'http://localhost:5173',
            $response->headers->get('Access-Control-Allow-Origin'),
        );
    }

    public function test_cors_config_never_lists_wildcard_string_in_non_empty_origins(): void
    {
        $origins = config('cors.allowed_origins');
        $this->assertIsArray($origins);
        $this->assertNotContains('*', $origins);
    }
}
