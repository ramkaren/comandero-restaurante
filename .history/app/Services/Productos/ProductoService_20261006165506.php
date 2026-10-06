<?php

namespace App\Services\Productos;

use App\Repositories\Productos\ProductoRepository;
use Exception;
use Illuminate\Http\Request;

class ProductoService
{

    protected $productoRepositorie;

    public function __construct(ProductoRepository $producto)
    {
        $this->productoRepositorie = $producto;
    }

    public function GetRowAll(Request $request): array
    {
        try {

            $perPage = min(max($request->integer('per_page', 15), 1), 100);

            $data = $this->productoRepositorie->GetProductos($perPage);

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

    public function GetProductoByID($producto_id): array
    {
        try {

            $data = $this->productoRepositorie->GetProductoByID($producto_id);

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

    public function EliminarProducto(Producto $producto): array
    {
        try {
            $data = $this->productoRepositorie->EliminarProducto($producto);

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
