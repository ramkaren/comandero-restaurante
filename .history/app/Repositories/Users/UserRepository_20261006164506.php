<?php

namespace App\Repositories\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{

    public function GetAllUser(): Collection
    {
        return User::get();
    }

    public function GetAllUserPaginated(int $perPage=10): LengthAwarePaginator 
    {
        return User::paginate($perPage);
    }

    public function FindById(int $id): User
    {
        return User::findOrFail($id);
    }
     
    public function UpdateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;   
    }

    public function DeleteUser(int $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete(); 
    }
    
}
