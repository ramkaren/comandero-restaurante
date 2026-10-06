<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use App\Services\Productos\ActualizarProductoService;
use Illuminate\Http\JsonResponse;

class ActualizarProductoController extends Controller
{
    protected $actualizarProductoService;

    public function __construct(ActualizarProductoService $service)
    {
        $this->actualizarProductoService = $service;
    }

    
}