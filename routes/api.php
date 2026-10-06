<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Productos\ProductoController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'me'])->middleware(['auth:api']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::middleware('auth:api')->prefix('products')->group(function () {
        Route::get('/', [ProductoController::class, 'getAll']);
        Route::post('productoByID',[ProductoController::class,'getByID']);
        Route::put('/{producto}', [ProductoController::class, 'update'])->middleware('permission:products.update');
        Route::patch('/{producto}', [ProductoController::class, 'update'])->middleware('permission:products.update');
        Route::delete('/{producto}', [ProductoController::class, 'destroy'])->middleware('permission:products.delete');
    });

    Route::middleware(['auth:api'])->prefix('users')->group(function(){
        Route::get('all', [UserController::class, 'GetUser']);
        Route::get('paginate', [UserController::class, 'GetUserPaginated']);
        Route::get('{id}', [UserController::class, 'GetUserById']);
        Route::put('{id}', [UserController::class, 'UpdateUser']);
        Route::delete('{id}', [UserController::class, 'DeleteUser']);
    });

});
