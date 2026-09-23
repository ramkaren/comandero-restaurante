<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $roleViews = [
            'super_usuario' => 'dashboard.super_usuario',
            'administrador' => 'dashboard.administrador',
            'mesero' => 'dashboard.mesero',
            'cocinero' => 'dashboard.cocinero',
            'cajero' => 'dashboard.cajero',
        ];

        foreach ($roleViews as $role => $view) {
            if ($user->hasRole($role)) {
                return view($view, ['user' => $user, 'role' => $role]);
            }
        }

        abort(403, 'Tu usuario no tiene un rol habilitado.');
    }
}