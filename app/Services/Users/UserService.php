<?php

namespace App\Services\Users;

use App\Repositories\Users\UserRepository;
use Exception;

class UserService
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $user)
    {
        $this->userRepository = $user;
    }

    public function OnGetUsersAll(): array
    {
        try {

            $data = $this->userRepository->GetAllUser();

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

public function OnGetUsersAllPaginated(int $perPage = 10): array
    {
        try {

            $data = $this->userRepository->GetAllUserPaginated($perPage);

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

 public function OnGetUserById(int $id): array
    {
        try {

            $user = $this->userRepository->FindById($id);

            return [
                "message" => "ok",
                "status" => 200,
                "data" => $user
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "status" => 500
            ];
        }
    }

    public function OnUpdateUser(int $id, array $data): array
    {
        try {

            $user = $this->userRepository->UpdateUser($id, $data);

            return [
                "message" => "Usuario actualizado correctamente.",
                "status" => 200,
                "data" => $user
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "status" => 500
            ];
        }
    }

    public function OnDeleteUser(int $id): array
    {
        try {

            $this->userRepository->DeleteUser($id);

            return [
                "message" => "Usuario eliminado correctamente.",
                "status" => 200
            ];
        } catch (Exception $e) {
   
         return [
                "message" => $e->getMessage(),
                "status" => 500
            ];
        }
    }
}