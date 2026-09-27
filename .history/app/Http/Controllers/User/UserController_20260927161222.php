<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private $userService;

    public function __construct()
    {
        
    }

    public function GetUser()
    {
        $response = null;
        return response()->json($response);
    }
}
