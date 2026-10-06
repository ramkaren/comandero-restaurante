<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Services\Categorias\ListarCategoriasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListarCategoriasController extends Controller
{
    protected $listarCategoriasService;

    public function __construct(ListarCategoriasService $service)
    {
        $this->listarCategoriasService = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $response = $this->listarCategoriasService->ListarCategorias($request);

        return response()->json($response, $response['status']);
    }
}