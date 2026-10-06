<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
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

    public function getAll(Request $request): JsonResponse
    {
        $response = $this->productoService->GetRowAll($request);
        return response()->json($response);
    }

    public function getByID(Request $request): JsonResponse
    {
        $response = $this->productoService->GetProductoByID($request->producto_id);
        return response()->json($response);
    }

    public function store(StoreProductoRequest $request): JsonResponse
    {
        $response = $this->productoService->CrearProducto($request->validated());

        return response()->json($response, $response['status']);
    }

    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $response = $this->productoService->ActualizarProducto($producto, $request->validated());

        return response()->json($response, $response['status']);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $response = $this->roductoService->EliminarProducto($producto);

        return response()->json($response, $response['status']);
    }
}
