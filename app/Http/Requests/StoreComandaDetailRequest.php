<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComandaDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['producto_id' => ['required', 'integer', 'exists:productos,id'], 'cantidad' => ['required', 'integer', 'min:1', 'max:99']];
    }
}