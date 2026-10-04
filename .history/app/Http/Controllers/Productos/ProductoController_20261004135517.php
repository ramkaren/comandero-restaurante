<?php

namespace App\Http\Controllers\Productos;

use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return response()->json(
            Producto::query()->with('categoria')->orderBy('id')->paginate($perPage)
        );
    }

    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $producto->update($request->validated());

        return response()->json([
            'message' => 'Producto actualizado correctamente.',
            'data' => $producto->fresh()->load('categoria'),
        ]);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->update(['activo' => false]);

        return response()->json([
            'message' => 'Producto desactivado correctamente.',
            'data' => ['id' => $producto->id, 'activo' => $producto->activo],
        ]);
    }
}