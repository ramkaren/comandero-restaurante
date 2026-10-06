<?php

namespace App\Repositories\Categorias;

use App\Models\Categoria;

class CategoriaRepository
{
    public function ListarCategorias(int $perPage)
    {
        return Categoria::query()
            ->withCount('productos')
            ->orderBy('id')
            ->paginate($perPage);
    }
}
