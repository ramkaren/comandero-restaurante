<?php

namespace App\Repositories\Categorias;

use App\Models\Categoria;

class CrearCategoriaRepository
{
    public function CrearCategoria(array $data): Categoria
    {
        return Categoria::create($data);
    }
}