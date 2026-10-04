<?php

namespace App\Repositories\Productos;

use App\Models\Producto;

class ProductoRepository
{

    public function GetProductos(int $perPage)
    {
        return Producto::query()
            ->with('categoria')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function GetProductoByID(int $producto_id): Producto
    {
        return Producto::findOrFail($producto_id);
    }
}
