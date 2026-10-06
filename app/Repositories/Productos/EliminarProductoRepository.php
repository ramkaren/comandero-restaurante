<?php

namespace App\Repositories\Productos;

use App\Models\Producto;

class EliminarProductoRepository
{
    public function EliminarProducto(Producto $producto): array
    {
        $producto->update(['activo' => false]);

        return ['id' => $producto->id, 'activo' => $producto->fresh()->activo];
    }
}