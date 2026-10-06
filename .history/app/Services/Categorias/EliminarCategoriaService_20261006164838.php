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

    public function EliminarCategoria(Categoria $categoria): array
    {
        try {
            $data = $this->eliminarCategoriaRepositorie->EliminarCategoria($categoria);

            return ['message' => 'Categoría desactivada correctamente.', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }
}