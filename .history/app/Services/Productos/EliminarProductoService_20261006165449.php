<?php

namespace App\Services\Productos;

use App\Models\Producto;
use App\Repositories\Productos\EliminarProductoRepository;
use Exception;

class EliminarProductoService
{
    protected $eliminarProductoRepositorie;

    public function __construct(EliminarProductoRepository $repository)
    {
        $this->eliminarProductoRepositorie = $repository;
    }

    
}