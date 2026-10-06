<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Categorias\ActualizarCategoriaController;
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
        Route::post('productoByID', [ProductoController::class, 'getByID']);
        Route::post('store', [ProductoController::class, 'store']);
        Route::put('update/{producto}', [ProductoController::class, 'update']);
        Route::delete('delete/{producto}', [ProductoController::class, 'destroy']);
    });

    Route::middleware('auth:api')->prefix('categories')->group(function () {
        Route::get('/', [ListarCategoriasController::class, 'index'])->middleware('permission:categories.view');
        Route::post('categoriaByID', [ObtenerCategoriaController::class, 'show'])->middleware('permission:categories.view');
        Route::post('/', [CrearCategoriaController::class, 'store'])->middleware('permission:categories.create');
        Route::put('/{categoria}', [ActualizarCategoriaController::class, 'update'])->middleware('permission:categories.update');
        Route::patch('/{categoria}', [ActualizarCategoriaController::class, 'update'])->middleware('permission:categories.update');
        Route::delete('/{categoria}', [EliminarCategoriaController::class, 'destroy'])->middleware('permission:categories.delete');
    });

    Route::middleware(['auth:api'])->prefix('users')->group(function () {
        Route::get('all', [UserController::class, 'GetUser']);
        Route::get('paginate', [UserController::class, 'GetUserPaginated']);
        Route::get('userByID/{id}', [UserController::class, 'GetUserById']);
        Route::put('update/{id}', [UserController::class, 'UpdateUser']);
        Route::delete('delete/{id}', [UserController::class, 'DeleteUser']);
    });
});
