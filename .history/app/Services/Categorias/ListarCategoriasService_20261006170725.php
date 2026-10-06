<?php

namespace App\Services\Categorias;

use App\Repositories\Categorias\ListarCategoriasRepository;
use Exception;
use Illuminate\Http\Request;

class ListarCategoriasService
{
    protected $listarCategoriasRepositorie;

    public function __construct(ListarCategoriasRepository $repository)
    {
        $this->listarCategoriasRepositorie = $repository;
    }

    
}