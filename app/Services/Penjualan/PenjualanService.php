<?php

namespace App\Services\Penjualan;

use App\Models\PenjualanHeader;
use App\Models\PenjualanRinci;
use App\Models\Transaksi\Penjualan\Penjualan_heders;
use App\Models\Transaksi\Penjualan\Penjualan_rinci;
use Illuminate\Support\Facades\DB;

class PenjualanService
{
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();

            $noTransaksi = $this->generateNoTransaksi();

            $totalHarga = collect($data['items'])->sum(function ($item) {
                return (float) $item['harga'] * (int) $item['qty'];
            });

            $angkringanId = $user->id == 1
                ? ($data['angkringan_id'] ?? null)
                : $user->id;

            $header = Penjualan_heders::create([
                'no_transaksi' => $noTransaksi,
                'tanggal_transaksi' => now(),
                'user_id' => $user->id,
                'angkringan_id' => $angkringanId,
                'total_harga' => $totalHarga,
                'flag' => 1,
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $jumlah = (int) $item['qty'];
                $harga = (float) $item['harga'];

                Penjualan_rinci::create([
                    'header_id' => $header->id,
                    'menu_id' => $item['kodemenu'],
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $jumlah * $harga,
                ]);
            }

            return $header->load('rinci');
        });
    }

    private function generateNoTransaksi(): string
    {
        return 'TRX' . now()->format('YmdHis');
    }
}
