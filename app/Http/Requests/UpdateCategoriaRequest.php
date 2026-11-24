<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoria = $this->route('categoria');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('categorias', 'nombre')->ignore($categoria->getKey())],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'activa' => ['sometimes', 'required', 'boolean'],
        ];
    }
}