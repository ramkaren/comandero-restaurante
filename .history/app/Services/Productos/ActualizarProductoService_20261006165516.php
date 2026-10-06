<?php

namespace App\Services\Productos;

use App\Models\Producto;
use App\Repositories\Productos\ActualizarProductoRepository;
use Exception;

class ActualizarProductoService
{
    protected $actualizarProductoRepositorie;

    public function __construct(ActualizarProductoRepository $repository)
    {
        $this->actualizarProductoRepositorie = $repository;
    }

    
}