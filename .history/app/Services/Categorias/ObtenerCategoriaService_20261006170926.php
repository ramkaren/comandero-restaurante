<?php

namespace App\Services\Categorias;

use App\Repositories\Categorias\ObtenerCategoriaRepository;
use Exception;

class ObtenerCategoriaService
{
    protected $obtenerCategoriaRepositorie;

    public function __construct(ObtenerCategoriaRepository $repository)
    {
        $this->obtenerCategoriaRepositorie = $repository;
    }

    
}