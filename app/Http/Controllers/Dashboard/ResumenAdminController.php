<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\ResumenAdminService;
use Illuminate\Http\JsonResponse;

class ResumenAdminController extends Controller
{
    public function __construct(private ResumenAdminService $resumenService)
    {
    }

    public function summary(): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->resumenService->resumen(),
        ]);
    }
}