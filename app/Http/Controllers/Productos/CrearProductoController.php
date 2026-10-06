<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Services\Productos\CrearProductoService;
use Illuminate\Http\JsonResponse;

class CrearProductoController extends Controller
{
    protected $crearProductoService;

    public function __construct(CrearProductoService $service)
    {
        $this->crearProductoService = $service;
    }

    public function store(StoreProductoRequest $request): JsonResponse
    {
        $response = $this->crearProductoService->CrearProducto($request->validated());

        return response()->json($response, $response['status']);
    }
}