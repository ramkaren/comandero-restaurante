<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentasReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hasta = ['nullable', 'date_format:Y-m-d'];

        if ($this->filled('desde')) {
            $hasta[] = 'after_or_equal:desde';
        }

        return [
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => $hasta,
        ];
    }
}