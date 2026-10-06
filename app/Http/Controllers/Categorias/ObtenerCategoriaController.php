<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Services\Categorias\ObtenerCategoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObtenerCategoriaController extends Controller
{
    protected $obtenerCategoriaService;

    public function __construct(ObtenerCategoriaService $service)
    {
        $this->obtenerCategoriaService = $service;
    }

    public function show(Request $request): JsonResponse
    {
        $request->validate(['categoria_id' => ['required', 'integer']]);
        $response = $this->obtenerCategoriaService->ObtenerCategoria($request->integer('categoria_id'));

        return response()->json($response, $response['status']);
    }
}