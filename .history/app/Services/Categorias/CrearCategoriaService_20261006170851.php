<?php

namespace App\Services\Categorias;

use App\Repositories\Categorias\CrearCategoriaRepository;
use Exception;

class CrearCategoriaService
{
    protected $crearCategoriaRepositorie;

    public function __construct(CrearCategoriaRepository $repository)
    {
        $this->crearCategoriaRepositorie = $repository;
    }

    
}