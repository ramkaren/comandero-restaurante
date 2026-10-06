<?php

namespace App\Services\Productos;

use App\Models\Producto;
use App\Repositories\Productos\EliminarProductoRepository;
use Exception;

class EliminarProductoService
{
    protected $eliminarProductoRepositorie;

    public function __construct(EliminarProductoRepository $repository)
    {
        $this->eliminarProductoRepositorie = $repository;
    }

    public function EliminarProducto(Producto $producto): array
    {
        try {
            $data = $this->eliminarProductoRepositorie->EliminarProducto($producto);

            return [
                'message' => 'Producto desactivado correctamente.',
                'status' => 200,
                'data' => $data,
            ];
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}