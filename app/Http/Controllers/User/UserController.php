<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $user)
    {
        $this->userService = $user;
    }

    public function GetUser(): JsonResponse
    {
        $response = $this->userService->OnGetUsersAll();
        return response()->json($response);
    }

    public function GetUserPaginate(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $response = $this->userService->OnGetUsersAllPaginate($perPage);
        return response()->json($response);
    }

    public function UpdateUser(Request $request, int $id): JsonResponse
    {
        $response = $this->userService->OnUpdateUser($id, $request->all());
        return response()->json($response);
    }

    public function DeleteUser(int $id): JsonResponse
    {
        $response = $this->userService->OnDeleteUser($id);
        return response()->json($response);
    }
}