<?php

namespace App\Repositories\Productos;

use App\Models\Producto;

class CrearProductoRepository
{
    public function CrearProducto(array $data): Producto
    {
        $producto = Producto::create($data);

        return $producto->load('categoria');
    }
}