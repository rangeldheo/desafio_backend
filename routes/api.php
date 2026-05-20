<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('produtos')->group(function () {
    Route::get('/', [ProductController::class, 'index']);

    Route::post('/', [ProductController::class, 'store']);
});

Route::prefix('compras')->group(function () {
    Route::post('/', [
        PurchaseController::class,
        'store',
    ]);
});
