<?php

namespace App\Repositories\Categorias;

use App\Models\Categoria;

class ActualizarCategoriaRepository
{
    public function ActualizarCategoria(Categoria $categoria, array $data): Categoria
    {
        $categoria->update($data);

        return $categoria->fresh()->loadCount('productos');
    }
}