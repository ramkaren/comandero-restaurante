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
        Route::post('store', [ProductoController::class, 'store']);
        Route::post('productoByID', [ProductoController::class, 'getByID']);
        Route::put('update/{producto}', [ProductoController::class, 'update']);
        Route::delete('delete/{producto}', [ProductoController::class, 'destroy']);
    });

    Route::middleware('auth:api')->prefix('categories')->group(function () {
        Route::get('/', [CategoriasController::class, 'getAll']);
        Route::post('categoriaByID', [CategoriasController::class, 'show']);
        Route::post('/', [CategoriasController::class, 'store']);
        Route::put('/{categoria}', [CategoriasController::class, 'update']);
        Route::patch('/{categoria}', [CategoriasController::class, 'update']);
        Route::delete('/{categoria}', [CategoriasController::class, 'destroy']);
    });

    Route::middleware(['auth:api'])->prefix('users')->group(function () {
        Route::get('all', [UserController::class, 'GetUser']);
        Route::get('paginate', [UserController::class, 'GetUserPaginated']);
        Route::get('userByID/{id}', [UserController::class, 'GetUserById']);
        Route::put('update/{id}', [UserController::class, 'UpdateUser']);
        Route::delete('delete/{id}', [UserController::class, 'DeleteUser']);
    });
});
