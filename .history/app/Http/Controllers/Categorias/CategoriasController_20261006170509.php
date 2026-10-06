<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    protected $categoriasService;

    public function __construct(ListarCategoriasService $service)
    {
        $this->categoriasService = $service;
    }

    public function getAll(Request $request): JsonResponse
    {
        $response = $this->categoriasService->ListarCategorias($request);
        return response()->json($response);
    }

    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $response = $this->categoriasService->CrearCategoria($request->validated());
        return response()->json($response);
    }

    public function getCategoriByID(Request $request): JsonResponse
    {
        $request->validate(['categoria_id' => ['required', 'integer']]);
        $response = $this->categoriasService->ObtenerCategoria($request->integer('categoria_id'));
        return response()->json($response);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse
    {
        $response = $this->categoriasService->ActualizarCategoria($categoria, $request->validated());
        return response()->json($response);
    }
}
