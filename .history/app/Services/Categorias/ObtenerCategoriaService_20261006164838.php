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

    public function ObtenerCategoria(int $categoria_id): array
    {
        try {
            $data = $this->obtenerCategoriaRepositorie->ObtenerCategoria($categoria_id);

            return ['message' => 'success', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }
}