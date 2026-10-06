<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Categorias\ActualizarCategoriaController;
use App\Http\Controllers\Categorias\CrearCategoriaController;
use App\Http\Controllers\Categorias\EliminarCategoriaController;
use App\Http\Controllers\Categorias\ListarCategoriasController;
use App\Http\Controllers\Categorias\ObtenerCategoriaController;
use App\Http\Controllers\Productos\ActualizarProductoController;
use App\Http\Controllers\Productos\CrearProductoController;
use App\Http\Controllers\Productos\EliminarProductoController;
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
        Route::get('/', [ProductoController::class, 'getAll']);
        Route::post('productoByID',[ProductoController::class,'getByID']);
        Route::post('/', [CrearProductoController::class, 'store'])->middleware('permission:products.create');
        Route::put('/{producto}', [ActualizarProductoController::class, 'update'])->middleware('permission:products.update');
        Route::patch('/{producto}', [ActualizarProductoController::class, 'update'])->middleware('permission:products.update');
        Route::delete('/{producto}', [EliminarProductoController::class, 'destroy'])->middleware('permission:products.delete');
    });

    Route::middleware('auth:api')->prefix('categories')->group(function () {
        Route::get('/', [ListarCategoriasController::class, 'index'])->middleware('permission:categories.view');
        Route::post('categoriaByID', [ObtenerCategoriaController::class, 'show'])->middleware('permission:categories.view');
        Route::post('/', [CrearCategoriaController::class, 'store'])->middleware('permission:categories.create');
        Route::put('/{categoria}', [ActualizarCategoriaController::class, 'update'])->middleware('permission:categories.update');
        Route::patch('/{categoria}', [ActualizarCategoriaController::class, 'update'])->middleware('permission:categories.update');
        Route::delete('/{categoria}', [EliminarCategoriaController::class, 'destroy'])->middleware('permission:categories.delete');
    });

});
