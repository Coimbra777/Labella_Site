<?php

return [
    /*
     * Garante registro do guard driver `sanctum` quando bootstrap/cache/packages.php
     * estiver desatualizado (ex.: volume Docker com permissões diferentes do host).
     */
    Laravel\Sanctum\SanctumServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\RepositoryServiceProvider::class,
];
