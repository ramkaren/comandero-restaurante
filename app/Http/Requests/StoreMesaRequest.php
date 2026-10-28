<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero'    => ['required', 'integer', 'min:1', 'unique:mesas,numero'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:50'],
            // "ocupada" no se asigna a mano: la fija el sistema al abrir una cuenta.
            'estado'    => ['nullable', 'in:disponible,reservada,fuera_de_servicio'],
            'activa'    => ['nullable', 'boolean'],
        ];
    }
}