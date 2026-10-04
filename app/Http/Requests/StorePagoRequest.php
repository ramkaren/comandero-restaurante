<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metodo' => ['required', Rule::in(['efectivo', 'tarjeta', 'transferencia'])],
            'monto' => ['required', 'numeric', 'gt:0'],
            'monto_recibido' => ['nullable', 'numeric', 'gt:0', 'required_if:metodo,efectivo'],
            'referencia' => ['nullable', 'string', 'max:120', 'required_if:metodo,transferencia'],
        ];
    }
}