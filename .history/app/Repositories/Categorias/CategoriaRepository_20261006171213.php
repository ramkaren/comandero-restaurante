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

    public function CrearCategoria(array $data): Categoria
    {
        return Categoria::create($data);
    }

    public function ObtenerCategoria(int $categoria_id): Categoria
    {
        return Categoria::withCount('productos')->findOrFail($categoria_id);
    }
}
