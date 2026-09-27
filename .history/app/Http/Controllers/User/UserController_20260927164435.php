<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private $userService;

    /*
    -- Crear un end-point para la actualizacion de usuarios
    -- Crear un end-point para la eliminacion de usuario
    -- Crear un end-point para traer a todos los usuarios pero paginado, con la paginacion nativa de laravel
    */

    public function __construct(UserService $user)
    {
        $this->userService = $user;
    }

    public function GetUser(): JsonResponse
    {
        $response = $this->userService->OnGetUsersAll();
        return response()->json($response);
    }
}
