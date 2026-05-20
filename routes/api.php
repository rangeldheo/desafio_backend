<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SaleController;

use Illuminate\Support\Facades\Route;

Route::prefix('produtos')->group(function () {
    Route::get('/', [ProductController::class, 'index']);

    Route::post('/', [ProductController::class, 'store']);
});

Route::prefix('compras')->group(function () {

    Route::get('/', [
        PurchaseController::class,
        'index',
    ]);

    Route::post('/', [
        PurchaseController::class,
        'store',
    ]);
});

Route::prefix('vendas')->group(function () {

    Route::get('/', [
        SaleController::class,
        'index',
    ]);

    Route::post('/', [
        SaleController::class,
        'store',
    ]);

    Route::post('/{id}/cancelar', [
        SaleController::class,
        'cancel',
    ]);
});
