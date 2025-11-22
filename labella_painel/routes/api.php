<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::prefix('v1')->group(function () {
    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/number/{orderNumber}', [OrderController::class, 'findByOrderNumber']);

    // Upload (protected - should be admin only in production)
    Route::post('/upload/image', [\App\Http\Controllers\Api\UploadController::class, 'uploadImage']);
    Route::post('/upload/images', [\App\Http\Controllers\Api\UploadController::class, 'uploadImages']);
    Route::delete('/upload/image', [\App\Http\Controllers\Api\UploadController::class, 'deleteImage']);
});

