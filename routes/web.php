<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\CocinaController;
use App\Http\Controllers\MesaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:super_usuario|administrador|mesero|cocinero|cajero')
        ->name('dashboard');

    Route::middleware('permission:tables.view')->group(function () {
        Route::get('/mesero/mesas', [MesaController::class, 'index'])->name('mesero.mesas');
    });

    Route::middleware('permission:tables.select')->group(function () {
        Route::post('/mesero/mesas/{mesa}/seleccionar', [MesaController::class, 'seleccionar'])->name('mesero.mesas.select');
    });

    Route::middleware('permission:tables.release')->group(function () {
        Route::post('/mesero/mesas/{mesa}/liberar', [MesaController::class, 'liberar'])->name('mesero.mesas.release');
    });

    Route::middleware('permission:orders.view')->group(function () {
        Route::get('/mesero/comandas', [ComandaController::class, 'index'])->name('mesero.comandas');
    });

    Route::middleware('permission:orders.create')->group(function () {
        Route::get('/mesero/comandas/{comanda}/editar', [ComandaController::class, 'edit'])->name('mesero.comandas.edit');
        Route::post('/mesero/comandas/{comanda}/productos', [ComandaController::class, 'agregar'])->name('mesero.comandas.add');
        Route::patch('/mesero/comandas/{comanda}/detalles/{detalle}/cantidad', [ComandaController::class, 'ajustar'])->name('mesero.comandas.adjust');
        Route::delete('/mesero/comandas/{comanda}/detalles/{detalle}', [ComandaController::class, 'eliminar'])->middleware('permission:orders.delete-product')->name('mesero.comandas.delete');
        Route::post('/mesero/comandas/{comanda}/guardar', [ComandaController::class, 'guardar'])->name('mesero.comandas.save');
    });

    Route::middleware(['role:cocinero', 'permission:kitchen.view'])->prefix('cocina')->name('cocina.')->group(function () {
        Route::get('/', [CocinaController::class, 'index'])->name('index');
        Route::get('/comandas/{comanda}', [CocinaController::class, 'show'])->name('show');
        Route::post('/comandas/{comanda}/iniciar', [CocinaController::class, 'start'])
            ->middleware('permission:kitchen.update-status')->name('start');
        Route::post('/comandas/{comanda}/lista', [CocinaController::class, 'finish'])
            ->middleware('permission:kitchen.update-status')->name('finish');
    });
});
