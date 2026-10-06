<?php

namespace App\Services\Categorias;

use App\Models\Categoria;
use App\Repositories\Categorias\CategoriaRepository;
use Exception;
use Illuminate\Http\Request;

class CategoriaService
{
    protected $categoriaRepositorie;

    public function __construct(CategoriaRepository $categoria)
    {
        $this->categoriaRepositorie = $categoria;
    }

    public function GetRowAll(Request $request): array
    {
        try {
            $perPage = min(max($request->integer('per_page', 15), 1), 100);
            $data = $this->categoriaRepositorie->GetCategorias($perPage);

            return ['message' => 'success', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function GetCategoriaByID(int $categoria_id): array
    {
        try {
            $data = $this->categoriaRepositorie->GetCategoriaByID($categoria_id);

            return ['message' => 'success', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function StoreCategoria(array $data): array
    {
        try {
            $categoria = $this->categoriaRepositorie->StoreCategoria($data);

            return ['message' => 'Categoría creada correctamente.', 'status' => 201, 'data' => $categoria];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function UpdateCategoria(Categoria $categoria, array $data): array
    {
        try {
            $categoria = $this->categoriaRepositorie->UpdateCategoria($categoria, $data);

            return ['message' => 'Categoría actualizada correctamente.', 'status' => 200, 'data' => $categoria];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function DeleteCategoria(Categoria $categoria): array
    {
        try {
            $data = $this->categoriaRepositorie->DeleteCategoria($categoria);

            return ['message' => 'Categoría desactivada correctamente.', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }
}