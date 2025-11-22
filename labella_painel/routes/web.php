<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
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
Route::prefix('api/admin')->middleware('auth')->group(function () {
    // Products
    Route::apiResource('products', AdminProductController::class);
    
    // Categories
    Route::apiResource('categories', AdminCategoryController::class);
    
    // Orders
    Route::apiResource('orders', AdminOrderController::class);
});
