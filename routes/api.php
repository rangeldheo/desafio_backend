<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('produtos')->group(function () {
    Route::get('/', [ProductController::class, 'index']);

    Route::post('/', [ProductController::class, 'store']);
});
