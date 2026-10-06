<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Services\Productos\EliminarProductoService;
use Illuminate\Http\JsonResponse;

class EliminarProductoController extends Controller
{
    protected $eliminarProductoService;

    public function __construct(EliminarProductoService $service)
    {
        $this->eliminarProductoService = $service;
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $response = $this->eliminarProductoService->EliminarProducto($producto);

        return response()->json($response, $response['status']);
    }
}