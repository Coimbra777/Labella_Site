<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::prefix('v1')->group(function () {
    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Settings (configurações do site)
    Route::get('/settings', [SettingsController::class, 'index']);

    // Orders
    Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:public-orders');
});
