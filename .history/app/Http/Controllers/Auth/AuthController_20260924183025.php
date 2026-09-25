<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'area_id' => $request->area_id,
            'last_login' => Carbon::now()
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 401);
        }

        if ($user->deleted_at !== null) {
            return response()->json(['error' => 'El usuario ya no esta registrado'], 403);
        }

        if (!$user->is_active) {
            return response()->json(['error' => 'El usuario esta dado de baja'], 403);
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $user->update([
            'last_login' => Carbon::now()
        ]);

        return $this->respondWithToken($token, $user);
    }

    public function me(): JsonResponse
    {
        $user = JWTAuth::user();
        $user->load('roles.permissions');

        return response()->json([
            "user" => $this->buildUserWithRoles($user),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function refresh(): JsonResponse
    {
        try {

            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            $user = JWTAuth::setToken($newToken)->toUser();

            return $this->respondWithToken($newToken, $user);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'No se pudo renovar la sesión'
            ], 401);
        }
    }

    protected function respondWithToken($token, $user): JsonResponse
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');

        $user->load('roles.permissions');

        return response()->json([
            "token" => $token,
            "expires_in" => $guard->factory()->getTTL() * 60,
            "user" => $this->buildUserWithRoles($user),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ], 200);
    }

    protected function buildUserWithRoles($user): array
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($user->area_id);

        $user->load(['roles', 'area']);

        return [
            ...$user->toArray(),
            "roles" => $user->getRoleNames(),
        ];
    }
}
