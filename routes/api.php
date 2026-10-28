<?php

use App\Http\Controllers\Dashboard\ResumenAdminController;
use App\Http\Controllers\Actividad\ActividadController;
use App\Http\Controllers\Pedidos\PedidoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Productos\ProductoController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Mesas\MesaAdminController;
use App\Http\Controllers\Reportes\VentasReporteController;
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

    Route::middleware(['auth:api'])->prefix('users')->group(function(){
        Route::get('all', [UserController::class, 'GetUser']);
        Route::get('paginate', [UserController::class, 'GetUserPaginated']);
        Route::get('{id}', [UserController::class, 'GetUserById']);
        Route::put('{id}', [UserController::class, 'UpdateUser']);
        Route::delete('{id}', [UserController::class, 'DeleteUser']);
    });

    Route::middleware('auth:api')->prefix('orders')->group(function () {
        Route::get('/', [PedidoController::class, 'index']);
        Route::post('/', [PedidoController::class, 'store']);
        Route::post('{comanda}/items', [PedidoController::class, 'addItems']);
    });

    Route::middleware('auth:api')->prefix('tables')->group(function () {
        Route::get('/', [MesaAdminController::class, 'index']);
        Route::post('/', [MesaAdminController::class, 'store']);
        Route::get('{mesa}', [MesaAdminController::class, 'show']);
        Route::put('{mesa}', [MesaAdminController::class, 'update']);
        Route::patch('{mesa}', [MesaAdminController::class, 'update']);
        Route::delete('{mesa}', [MesaAdminController::class, 'destroy']);
    });

    Route::middleware('auth:api')->prefix('reports')->group(function () {
    Route::get('sales', [VentasReporteController::class, 'sales']);
    Route::get('sales/comparison', [VentasReporteController::class, 'comparison']);
    });

    Route::middleware('auth:api')->prefix('dashboard')->group(function () {
    Route::get('summary', [ResumenAdminController::class, 'summary']);
    });

    Route::middleware('auth:api')->prefix('activity')->group(function () {
    Route::get('/', [ActividadController::class, 'index']);
    });

    });
