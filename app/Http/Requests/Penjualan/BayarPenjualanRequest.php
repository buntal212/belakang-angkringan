<?php

namespace App\Http\Requests\Penjualan;

use Illuminate\Foundation\Http\FormRequest;

class BayarPenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:penjualan_header,id',
            'no_transaksi' => 'required|string',
            'total' => 'nullable|numeric|min:0',
            'dibayar' => 'required|numeric|min:0',
            'metode_bayar' => 'required|string|in:CASH,QRIS,TRANSFER',
        ];
    }
}
