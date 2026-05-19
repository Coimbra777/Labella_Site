<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\RepositoryServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;

return [
    /*
     * Garante registro do guard driver `sanctum` quando bootstrap/cache/packages.php
     * estiver desatualizado (ex.: volume Docker com permissões diferentes do host).
     */
    SanctumServiceProvider::class,
    AppServiceProvider::class,
    AdminPanelProvider::class,
    RepositoryServiceProvider::class,
];
