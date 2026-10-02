<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $producto = $this->route('producto');

        return [
            'categoria_id' => ['sometimes', 'required', 'integer', 'exists:categorias,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('productos', 'nombre')->ignore($producto->getKey())],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0'],
            'disponible' => ['sometimes', 'required', 'boolean'],
            'activo' => ['sometimes', 'required', 'boolean'],
        ];
    }
}