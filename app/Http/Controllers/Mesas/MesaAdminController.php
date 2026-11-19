<?php

namespace App\Http\Controllers\Mesas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMesaRequest;
use App\Http\Requests\UpdateMesaRequest;
use App\Models\Mesa;
use App\Services\Mesas\MesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MesaAdminController extends Controller
{
    public function __construct(private MesaService $mesaService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate(['inactivas' => 'nullable|boolean']);

        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->mesaService->listar($request->boolean('inactivas')),
        ]);
    }

    public function show(Mesa $mesa): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->mesaService->detalle($mesa),
        ]);
    }

    public function store(StoreMesaRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'status'  => 201,
            'data'    => $this->mesaService->crear($request->validated()),
        ], 201);
    }

    public function update(UpdateMesaRequest $request, Mesa $mesa): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $this->mesaService->actualizar($mesa, $request->validated()),
        ]);
    }

    public function destroy(Mesa $mesa): JsonResponse
    {
        $resultado = $this->mesaService->eliminar($mesa);

        return response()->json([
            'message' => "Mesa {$resultado} correctamente.",
            'status'  => 200,
            'data'    => ['id' => $mesa->id, 'resultado' => $resultado],
        ]);
    }
}