<?php

namespace App\Repositories\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{

    public function GetAllUser(): Collection
    {
        return User::get();
    }
}
