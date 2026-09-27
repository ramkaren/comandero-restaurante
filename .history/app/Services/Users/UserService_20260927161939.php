<?php

namespace App\Services\Users;

use App\Repositories\Users\UserRepository;
use Exception;

class UserService
{

    private $userRepository;

    public function __construct(UserRepository $user)
    {
        $this->userRepository = $user;
    }

    public function OnGetUsersAll(): array
    {
        try {

            $data = "";

            return [
                "message" => "ok",
                "status" => 200,
                "data" => $data
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "status" => 500
            ];
        }
    }
}
