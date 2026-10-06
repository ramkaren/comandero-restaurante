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

    public function ActualizarCategoria(Categoria $categoria, array $data): array
    {
        try {
            $categoria = $this->actualizarCategoriaRepositorie->ActualizarCategoria($categoria, $data);

            return ['message' => 'Categoría actualizada correctamente.', 'status' => 200, 'data' => $categoria];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }
}