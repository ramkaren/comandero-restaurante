<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Services\Categorias\EliminarCategoriaService;
use Illuminate\Http\JsonResponse;

class EliminarCategoriaController extends Controller
{
    protected $eliminarCategoriaService;

    public function __construct(EliminarCategoriaService $service)
    {
        $this->eliminarCategoriaService = $service;
    }

    public function destroy(Categoria $categoria): JsonResponse
    {
        $response = $this->eliminarCategoriaService->EliminarCategoria($categoria);

        return response()->json($response, $response['status']);
    }
}