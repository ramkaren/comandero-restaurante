<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActividadIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modulo'   => ['nullable', 'in:sesion,cobros,pedidos,mesas,productos,usuarios'],
            'user_id'  => ['nullable', 'integer'],
            'fecha'    => ['nullable', 'date_format:Y-m-d'],
            'search'   => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}