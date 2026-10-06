<?php

namespace App\Services\Productos;

use App\Repositories\Productos\CrearProductoRepository;
use Exception;

class CrearProductoService
{
    protected $crearProductoRepositorie;

    public function __construct(CrearProductoRepository $repository)
    {
        $this->crearProductoRepositorie = $repository;
    }

    public function CrearProducto(array $data): array
    {
        try {
            $producto = $this->crearProductoRepositorie->CrearProducto($data);

            return [
                'message' => 'Producto creado correctamente.',
                'status' => 201,
                'data' => $producto,
            ];
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}