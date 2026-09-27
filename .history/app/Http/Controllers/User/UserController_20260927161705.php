<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private $userService;

    public function __construct(UserService $user)
    {
        $this->userService = $user;
    }

    public function GetUser()
    {
        $response = $this->userService->OnGetUsersAll();
        return response()->json($response);
    }
}
