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

    public function CrearCategoria(array $data): array
    {
        try {
            $categoria = $this->crearCategoriaRepositorie->CrearCategoria($data);

            return ['message' => 'Categoría creada correctamente.', 'status' => 201, 'data' => $categoria];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }
}