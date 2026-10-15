<?php

namespace App\Http\Controllers\Productos;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use App\Models\Categoria;
use App\Services\Productos\ProductoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{

    protected $productoService;

    public function __construct(ProductoService $producto)
    {
        $this->productoService = $producto;
    }

    public function getAll(Request $request): JsonResponse
    {
        $response = $this->productoService->GetRowAll($request);
        return response()->json($response);
    }

    public function getByID(Request $request): JsonResponse
    {
        $response = $this->productoService->GetProductoByID($request->producto_id);
        return response()->json($response);
    }

    public function store(StoreProductoRequest $request): JsonResponse
    {
        $response = $this->productoService->CrearProducto($request->validated());

        return response()->json($response, $response['status']);
    }

    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $response = $this->productoService->ActualizarProducto($producto, $request->validated());

        return response()->json($response, $response['status']);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $response = $this->productoService->EliminarProducto($producto);

        return response()->json($response, $response['status']);
    }
    public function store(Request $request): JsonResponse
{
    $data = $request->validate([
        'nombre'       => 'required|string|max:255',
        'descripcion'  => 'nullable|string',
        'precio'       => 'required|numeric|min:0',
        'categoria_id' => 'required|exists:categorias,id',
        'disponible'   => 'boolean',
    ]);

    $producto = Producto::create($data)->load('categoria');

    return response()->json([
        'message' => 'success',
        'status'  => 201,
        'data'    => $producto,
    ], 201);
}
public function groupedByCategory(Request $request): JsonResponse
{
    $request->validate([
        'search'       => 'nullable|string|max:100',
        'categoria_id' => 'nullable|integer',
        'page'         => 'nullable|integer|min:1',
        'per_category' => 'nullable|integer|min:1|max:50',
    ]);

    $perCategory = (int) $request->input('per_category', 5);
    $page        = (int) $request->input('page', 1);

    $categorias = Categoria::where('activa', true)->orderBy('nombre')->get(['id', 'nombre']);

    $grupos   = [];
    $lastPage = 1;

    foreach ($categorias as $categoria) {
        if ($request->filled('categoria_id') && $categoria->id !== (int) $request->categoria_id) {
            continue;
        }

        $query = Producto::where('categoria_id', $categoria->id)
            ->where('activo', true)
            ->when($request->filled('search'), fn ($q) => $q->where('nombre', 'like', '%' . $request->search . '%'))
            ->orderBy('nombre');

        $total = (clone $query)->count();
        if ($total === 0) {
            continue;
        }

        $lastPage = max($lastPage, (int) ceil($total / $perCategory));

        $productos = $query->skip(($page - 1) * $perCategory)->take($perCategory)->get();
        if ($productos->isEmpty()) {
            continue;
        }

        $grupos[] = [
            'categoria' => $categoria,
            'total'     => $total,
            'productos' => $productos,
        ];
    }

    return response()->json([
        'message' => 'success',
        'status'  => 200,
        'data'    => [
            'categorias'   => $categorias,
            'grupos'       => $grupos,
            'page'         => $page,
            'last_page'    => $lastPage,
            'per_category' => $perCategory,
        ],
    ]);
}

}
