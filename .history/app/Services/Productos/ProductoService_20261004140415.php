<?php

namespace App\Services\Productos;

use Exception;

class ProductoService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
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
