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

    public function CrearProducto(array $data): Producto
    {
        $producto = Producto::create($data);

        return $producto->load('categoria');
    }

    public function EliminarProducto(Producto $producto): array
    {
        $producto->update(['activo' => false]);

        return ['id' => $producto->id, 'activo' => $producto->fresh()->activo];
    }
}
