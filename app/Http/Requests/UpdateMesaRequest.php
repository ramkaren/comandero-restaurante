<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero'    => ['sometimes', 'integer', 'min:1', Rule::unique('mesas', 'numero')->ignore($this->route('mesa'))],
            'capacidad' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'estado'    => ['sometimes', 'in:disponible,reservada,fuera_de_servicio'],
            'activa'    => ['sometimes', 'boolean'],
        ];
    }
}