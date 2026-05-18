<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');
Route::get('/user', [LoginController::class, 'user'])->middleware('auth');

// Admin API routes (JSON) - moved to /api/admin to avoid conflict with Filament
Route::prefix('api/admin')->middleware(['auth', 'admin'])->group(function () {
    // Products
    Route::apiResource('products', AdminProductController::class);
    
    // Categories
    Route::apiResource('categories', AdminCategoryController::class);
    
    // Orders
    Route::apiResource('orders', AdminOrderController::class);

    // Uploads
    Route::post('/upload/image', [UploadController::class, 'uploadImage']);
    Route::post('/upload/images', [UploadController::class, 'uploadImages']);
    Route::delete('/upload/image', [UploadController::class, 'deleteImage']);
});
