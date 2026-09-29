<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'me'])->middleware(['auth:api']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    //Rutas
    Route::middleware(['auth:api'])->prefix('users')->group(function(){
        // http://localhost:8000/api/v1/user/{la ruta a consultar}
        Route::get('all',[UserController::class,'GetUser']);
        Route::get('paginate', [UserController::class, 'GetUserPaginate']);
        Route::put('{id}', [UserController::class, 'UpdateUser']);
        Route::delete('{id}', [UserController::class, 'DeleteUser']);
    });

    //Les dejo un ejemplo de las rutas que etngas que protejer
    Route::middleware(['auth:api'])->prefix('ejemplo')->group(function () {});
    
    });
