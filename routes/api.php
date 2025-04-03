
<?php

use App\Http\Controllers\ProductController;
use Illuminate\Routing\Route;

Route::apiResource('/products', ProductController::class);
