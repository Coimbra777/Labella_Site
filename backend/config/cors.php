<?php

/**
 * Origens permitidas para a API pública (browser).
 *
 * - Em produção: defina CORS_ALLOWED_ORIGINS no .env (lista separada por vírgula).
 *   Nunca use "*" em produção.
 * - Em outros ambientes, se CORS_ALLOWED_ORIGINS estiver vazio, usamos URLs padrão do Vite local.
 */
$rawOrigins = env('CORS_ALLOWED_ORIGINS');

$parseOrigins = static function (?string $value): array {
    if ($value === null || trim($value) === '') {
        return [];
    }

    return array_values(array_filter(array_map('trim', explode(',', $value))));
};

$allowedOrigins = $parseOrigins($rawOrigins);

if ($allowedOrigins === [] && env('APP_ENV') !== 'production') {
    $allowedOrigins = [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:8081',
        'http://127.0.0.1:8081',
    ];
}

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Accept', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
