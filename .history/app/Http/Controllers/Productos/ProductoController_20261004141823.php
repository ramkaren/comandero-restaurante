<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use App\Services\Productos\ProductoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{

    protected $productoService;

    public function __construct(ProductoService $producto)
    {
        $this->productoService = $producto;
    }

    public function getAll(Request $request):JsonResponse{
        $response = $this->productoService->GetRowAll($request);
        return response()->json($response);
    }

    public function getByID(Request $request):JsonResponse{
        $response = $this->productoService->GetRowAll($request);
        return response()->json($response);
    }

    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $producto->update($request->validated());

        return response()->json([
            'message' => 'Producto actualizado correctamente.',
            'data' => $producto->fresh()->load('categoria'),
        ]);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->update(['activo' => false]);

        return response()->json([
            'message' => 'Producto desactivado correctamente.',
            'data' => ['id' => $producto->id, 'activo' => $producto->activo],
        ]);
    }
}
