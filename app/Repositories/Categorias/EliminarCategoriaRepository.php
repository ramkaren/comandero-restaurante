<?php

namespace App\Repositories\Categorias;

use App\Models\Categoria;

class EliminarCategoriaRepository
{
    public function EliminarCategoria(Categoria $categoria): array
    {
        $categoria->update(['activa' => false]);

        return ['id' => $categoria->id, 'activa' => $categoria->fresh()->activa];
    }
}