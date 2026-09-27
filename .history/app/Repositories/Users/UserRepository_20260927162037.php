<?php

namespace App\Repositories\Users;

use App\Models\User;

class UserRepository
{
    
    public function GetAllUser(){
        return User::get();
    }
}
