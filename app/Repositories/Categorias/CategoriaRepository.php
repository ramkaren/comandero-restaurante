<?php

namespace App\Repositories\Categorias;

use App\Models\Categoria;

class CategoriaRepository
{
    public function GetCategorias(int $perPage)
    {
        return Categoria::query()
            ->withCount('productos')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function GetCategoriaByID(int $categoria_id): Categoria
    {
        return Categoria::withCount('productos')->findOrFail($categoria_id);
    }

    public function StoreCategoria(array $data): Categoria
    {
        return Categoria::create($data);
    }

    public function UpdateCategoria(Categoria $categoria, array $data): Categoria
    {
        $categoria->update($data);

        return $categoria->fresh()->loadCount('productos');
    }

    public function DeleteCategoria(Categoria $categoria): array
    {
        $categoria->update(['activa' => false]);

        return ['id' => $categoria->id, 'activa' => $categoria->fresh()->activa];
    }
}