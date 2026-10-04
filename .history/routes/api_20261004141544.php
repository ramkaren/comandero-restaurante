<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Productos\ProductoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'me'])->middleware(['auth:api']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::middleware('auth:api')->prefix('products')->group(function () {
        Route::get('/', [ProductoController::class, 'index']);
        Route::put('/{producto}', [ProductoController::class, 'update'])->middleware('permission:products.update');
        Route::patch('/{producto}', [ProductoController::class, 'update'])->middleware('permission:products.update');
        Route::delete('/{producto}', [ProductoController::class, 'destroy'])->middleware('permission:products.delete');
    });

});
