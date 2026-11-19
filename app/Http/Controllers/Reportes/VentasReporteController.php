<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentasReporteRequest;
use App\Services\Reportes\VentasReporteService;
use Illuminate\Http\JsonResponse;

class VentasReporteController extends Controller
{
    public function __construct(private VentasReporteService $reporteService)
    {
    }

    public function sales(VentasReporteRequest $request): JsonResponse
    {
        $data = $request->validated();

        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->reporteService->consultar($data['desde'] ?? null, $data['hasta'] ?? null),
        ]);
    }

    public function comparison(): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->reporteService->comparaciones(),
        ]);
    }
}