<?php

namespace App\Services\Categorias;

use App\Models\Categoria;
use App\Repositories\Categorias\EliminarCategoriaRepository;
use Exception;

class EliminarCategoriaService
{
    protected $eliminarCategoriaRepositorie;

    public function __construct(EliminarCategoriaRepository $repository)
    {
        $this->eliminarCategoriaRepositorie = $repository;
    }

    
}