<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Categorias\ActualizarCategoriaController;
use App\Http\Controllers\Categorias\CategoriasController;
use App\Http\Controllers\Categorias\CrearCategoriaController;
use App\Http\Controllers\Categorias\EliminarCategoriaController;
use App\Http\Controllers\Categorias\ListarCategoriasController;
use App\Http\Controllers\Categorias\ObtenerCategoriaController;
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
        Route::post('/', [ProductoController::class, 'store'])->middleware('permission:products.create');
        Route::get('grouped', [ProductoController::class, 'groupedByCategory']);
    });

    Route::middleware('auth:api')->prefix('categories')->group(function () {
        Route::get('all', [CategoriasController::class, 'getAll']);
        Route::post('store', [CategoriasController::class, 'store']);
        Route::post('getByID', [CategoriasController::class, 'getByID']);
        Route::put('update/{categoria}', [CategoriasController::class, 'update']);
        Route::delete('delete/{categoria}', [CategoriasController::class, 'destroy']);
    });

    Route::middleware(['auth:api'])->prefix('users')->group(function () {
        Route::get('all', [UserController::class, 'GetUser']);
        Route::get('paginate', [UserController::class, 'GetUserPaginated']);
        Route::get('getByID/{id}', [UserController::class, 'GetUserById']);
        Route::put('update/{id}', [UserController::class, 'UpdateUser']);
        Route::delete('delete/{id}', [UserController::class, 'DeleteUser']);
    });
});
