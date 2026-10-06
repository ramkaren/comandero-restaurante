<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    protected $categoriasService;

    public function __construct(ListarCategoriasService $service)
    {
        $this->categoriasService = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $response = $this->listarCategoriasService->ListarCategorias($request);

        return response()->json($response, $response['status']);
    }
}
