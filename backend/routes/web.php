<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Auth\AdminTokenController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');
Route::get('/user', [LoginController::class, 'user'])->middleware('auth');

// Token de API admin (Sanctum) — throttle; não cria sessão web
Route::post('/api/admin/token', [AdminTokenController::class, 'store'])
    ->middleware('throttle:api-admin-login');

// Admin API routes (JSON) - moved to /api/admin to avoid conflict with Filament
Route::prefix('api/admin')->middleware(['auth.admin.api', 'admin'])->group(function () {
    Route::delete('/token', [AdminTokenController::class, 'destroy']);

    // Products
    Route::apiResource('products', AdminProductController::class);

    // Categories
    Route::apiResource('categories', AdminCategoryController::class);

    // Orders (sem store: pedidos públicos são POST /api/v1/orders; Filament gere criação manual se necessário)
    Route::apiResource('orders', AdminOrderController::class)->except(['store']);

    // Uploads
    Route::post('/upload/image', [UploadController::class, 'uploadImage']);
    Route::post('/upload/images', [UploadController::class, 'uploadImages']);
    Route::delete('/upload/image', [UploadController::class, 'deleteImage']);
});
