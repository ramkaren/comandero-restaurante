<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Services\Categorias\CategoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    protected $categoriaService;

    public function __construct(CategoriaService $categoria)
    {
        $this->categoriaService = $categoria;
    }

    public function getAll(Request $request): JsonResponse
    {
        $response = $this->categoriaService->GetRowAll($request);

        return response()->json($response, $response['status']);
    }

    public function getByID(Request $request): JsonResponse
    {
        $request->validate(['categoria_id' => ['required', 'integer']]);
        $response = $this->categoriaService->GetCategoriaByID($request->integer('categoria_id'));

        return response()->json($response, $response['status']);
    }

    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $response = $this->categoriaService->StoreCategoria($request->validated());

        return response()->json($response, $response['status']);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse
    {
        $response = $this->categoriaService->UpdateCategoria($categoria, $request->validated());

        return response()->json($response, $response['status']);
    }

    public function destroy(Categoria $categoria): JsonResponse
    {
        $response = $this->categoriaService->DeleteCategoria($categoria);

        return response()->json($response, $response['status']);
    }
}