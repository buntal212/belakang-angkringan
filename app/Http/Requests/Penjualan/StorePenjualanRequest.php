<?php

namespace App\Http\Requests\Penjualan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePenjualanRequest extends FormRequest
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
            'kode_angkringan' => 'required',
            'catatan' => 'nullable|string',
            'metode_bayar' => 'nullable|string',
            'dibayar' => 'nullable|numeric|min:0',
            'kembalian' => 'nullable|numeric',

            'items' => 'required|array|min:1',
            'items.*.kodemenu' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_ankringan' => 'Kode Angkringan Tidak Boleh Kosong...!!!',
            'items.required' => 'Item pesanan wajib diisi',
            'items.min' => 'Minimal 1 item pesanan',
            'items.*.kodemenu.required' => 'Menu wajib dipilih',
            'items.*.qty.min' => 'Jumlah minimal 1',
            'metode_bayar.required' => 'Metode bayar wajib dipilih',
        ];
    }
}
