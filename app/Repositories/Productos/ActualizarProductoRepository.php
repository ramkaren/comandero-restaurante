<?php

namespace App\Repositories\Productos;

use App\Models\Producto;

class ActualizarProductoRepository
{
    public function ActualizarProducto(Producto $producto, array $data): Producto
    {
        $producto->update($data);

        return $producto->fresh()->load('categoria');
    }
}