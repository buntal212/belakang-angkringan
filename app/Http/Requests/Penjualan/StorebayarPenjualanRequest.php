<?php

namespace App\Http\Requests\Penjualan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorebayarPenjualanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'id'            => 'required|integer|exists:penjualan_h,id',
            'no_transaksi'  => 'required|string',
            'total'         => 'required|numeric|min:0',
            'dibayar'       => 'required|numeric|min:0',
            'metode_bayar'  => 'required|string|in:CASH,QRIS,TRANSFER',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'               => 'ID transaksi tidak boleh kosong.',
            'id.exists'                 => 'Data transaksi tidak ditemukan.',

            'no_transaksi.required'     => 'Nomor transaksi tidak boleh kosong.',

            'total.required'            => 'Total pembayaran tidak boleh kosong.',
            'total.numeric'             => 'Total pembayaran harus berupa angka.',

            'dibayar.required'          => 'Nominal pembayaran tidak boleh kosong.',
            'dibayar.numeric'           => 'Nominal pembayaran harus berupa angka.',
            'dibayar.min'               => 'Nominal pembayaran tidak boleh kurang dari 0.',

            'metode_bayar.required'     => 'Metode pembayaran wajib dipilih.',
            'metode_bayar.in'           => 'Metode pembayaran tidak valid.',
        ];
    }
}
