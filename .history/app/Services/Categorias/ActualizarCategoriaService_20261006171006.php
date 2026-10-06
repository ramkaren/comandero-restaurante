<?php

namespace App\Services\Categorias;

use App\Models\Categoria;
use App\Repositories\Categorias\ActualizarCategoriaRepository;
use Exception;

class ActualizarCategoriaService
{
    protected $actualizarCategoriaRepositorie;

    public function __construct(ActualizarCategoriaRepository $repository)
    {
        $this->actualizarCategoriaRepositorie = $repository;
    }

   
}