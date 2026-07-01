<?php

namespace App\Http\Requests\Penjualan;

use Illuminate\Foundation\Http\FormRequest;

class ListPenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'search' => 'nullable|string',
            'dateFrom' => 'nullable|date',
            'dateTo' => 'nullable|date',
            'angkringan_id' => 'nullable',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
