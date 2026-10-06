<?php

namespace App\Services\Productos;

use App\Models\Producto;
use App\Repositories\Productos\ActualizarProductoRepository;
use Exception;

class ActualizarProductoService
{
    protected $actualizarProductoRepositorie;

    public function __construct(ActualizarProductoRepository $repository)
    {
        $this->actualizarProductoRepositorie = $repository;
    }

    public function ActualizarProducto(Producto $producto, array $data): array
    {
        try {
            $data = $this->actualizarProductoRepositorie->ActualizarProducto($producto, $data);

            return [
                'message' => 'Producto actualizado correctamente.',
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