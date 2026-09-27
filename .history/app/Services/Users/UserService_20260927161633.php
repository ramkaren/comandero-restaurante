<?php

namespace App\Services\Users;

use Exception;

class UserService
{

    public function __construct() {}

    public function OnGetUsersAll()
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
