<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Services\Categorias\ActualizarCategoriaService;
use Illuminate\Http\JsonResponse;

class ActualizarCategoriaController extends Controller
{
    protected $actualizarCategoriaService;

    public function __construct(ActualizarCategoriaService $service)
    {
        $this->actualizarCategoriaService = $service;
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse
    {
        $response = $this->actualizarCategoriaService->ActualizarCategoria($categoria, $request->validated());

        return response()->json($response, $response['status']);
    }
}