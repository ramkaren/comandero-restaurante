<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagarCuentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metodo'                   => ['required', 'in:efectivo,tarjeta,transferencia'],
            'monto'                    => ['required', 'numeric', 'min:0.01'],
            'monto_recibido'           => ['required_if:metodo,efectivo', 'nullable', 'numeric', 'min:0'],
            'referencia'               => ['nullable', 'string', 'max:100'],
            'confirmacion_externa'     => ['accepted_if:metodo,tarjeta'],
            'transferencia_verificada' => ['accepted_if:metodo,transferencia'],
        ];
    }
}
