<?php

namespace App\Services\Productos;

use App\Repositories\Productos\ProductoRepository;
use Exception;

class ProductoService
{

    protected $productoRepositorie;

    public function __construct(ProductoRepository $producto)
    {
        $this->productoRepositorie = $producto;
    }

    public function GetRowAll(): array
    {
        try {

            $data = "";

            return [
                "message" => "success",
                "status" => 200,
                "data" => $data
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "status" => 500
            ];
        }
    }
}
