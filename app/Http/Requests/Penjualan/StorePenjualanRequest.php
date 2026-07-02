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
            'id' => 'nullable|integer',
            'notrans' => 'nullable|string',
            'tgl' => 'nullable|date',

            'kode_angkringan' => 'required|integer',
            'atasnama' => 'required|string|max:100',
            'catatan' => 'nullable|string',
            'metode_bayar' => 'nullable|string',

            'dibayar' => 'nullable|numeric|min:0',
            'kembalian' => 'nullable|numeric',

            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer',
            'items.*.kodemenu' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_angkringan.required' => 'Kode Angkringan Tidak Boleh Kosong...!!!',
            'atasnama.required' => 'Atas Nama / Pemesan Harus Diisi',

            'items.required' => 'Item pesanan wajib diisi.',
            'items.min' => 'Minimal 1 item pesanan.',

            'items.*.kodemenu.required' => 'Menu wajib dipilih.',
            'items.*.qty.required' => 'Jumlah wajib diisi.',
            'items.*.qty.min' => 'Jumlah minimal 1.',
            'items.*.harga.required' => 'Harga wajib diisi.',
            'items.*.subtotal.required' => 'Subtotal wajib diisi.',
        ];
    }
}
