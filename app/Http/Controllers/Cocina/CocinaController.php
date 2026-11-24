<?php

namespace App\Http\Controllers\Cocina;

use App\Http\Controllers\Controller;
use App\Models\Comanda;
use App\Services\Cocina\CocinaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CocinaController extends Controller
{
    public function __construct(private CocinaService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->service->ordenes(),
        ]);
    }

    public function updateStatus(Request $request, Comanda $comanda): JsonResponse
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(['en_preparacion', 'lista'])],
        ]);

        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->service->cambiarEstado($comanda->id, $data['estado']),
        ]);
    }
}
