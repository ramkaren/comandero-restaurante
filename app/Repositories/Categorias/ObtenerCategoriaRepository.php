<?php

namespace App\Repositories\Categorias;

use App\Models\Categoria;

class ObtenerCategoriaRepository
{
    public function ObtenerCategoria(int $categoria_id): Categoria
    {
        return Categoria::withCount('productos')->findOrFail($categoria_id);
    }
}