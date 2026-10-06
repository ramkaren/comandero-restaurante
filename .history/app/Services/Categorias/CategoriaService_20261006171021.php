<?php

namespace App\Services\Categorias;

use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;

class CategoriaService
{

    protected $categoriasRepositorie;

    public function __construct(ListarCategoriasRepository $repository)
    {
        $this->categoriasRepositorie = $repository;
    }

    public function ListarCategorias(Request $request): array
    {
        try {
            $perPage = min(max($request->integer('per_page', 15), 1), 100);
            $data = $this->categoriasRepositorie->ListarCategorias($perPage);

            return ['message' => 'success', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function CrearCategoria(array $data): array
    {
        try {
            $categoria = $this->categoriasRepositorie->CrearCategoria($data);

            return ['message' => 'Categoría creada correctamente.', 'status' => 200, 'data' => $categoria];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function ObtenerCategoria(int $categoria_id): array
    {
        try {
            $data = $this->categoriasRepositorie->ObtenerCategoria($categoria_id);

            return ['message' => 'success', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function ActualizarCategoria(Categoria $categoria, array $data): array
    {
        try {
            $categoria = $this->categoriasRepositorie->ActualizarCategoria($categoria, $data);

            return ['message' => 'Categoría actualizada correctamente.', 'status' => 200, 'data' => $categoria];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function EliminarCategoria(Categoria $categoria): array
    {
        try {
            $data = $this->eliminarCategoriaRepositorie->EliminarCategoria($categoria);
            return ['message' => 'Categoría desactivada correctamente.', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }
}
