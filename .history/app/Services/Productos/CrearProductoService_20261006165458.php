<?php

namespace App\Services\Productos;

use App\Repositories\Productos\CrearProductoRepository;
use Exception;

class CrearProductoService
{
    protected $crearProductoRepositorie;

    public function __construct(CrearProductoRepository $repository)
    {
        $this->crearProductoRepositorie = $repository;
    }

    
}