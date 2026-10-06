<?php

namespace App\Services\Categorias;

use App\Repositories\Categorias\ListarCategoriasRepository;
use Exception;
use Illuminate\Http\Request;

class ListarCategoriasService
{
    protected $listarCategoriasRepositorie;

    public function __construct(ListarCategoriasRepository $repository)
    {
        $this->listarCategoriasRepositorie = $repository;
    }

    public function ListarCategorias(Request $request): array
    {
        try {
            $perPage = min(max($request->integer('per_page', 15), 1), 100);
            $data = $this->listarCategoriasRepositorie->ListarCategorias($perPage);

            return ['message' => 'success', 'status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'status' => 500];
        }
    }
}