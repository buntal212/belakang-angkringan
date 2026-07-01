<?php

namespace App\Services\Penjualan;

use App\Models\PenjualanHeader;
use App\Models\PenjualanRinci;
use App\Models\Transaksi\Penjualan\Penjualan_heders;
use App\Models\Transaksi\Penjualan\Penjualan_rinci;
use Illuminate\Support\Facades\DB;

class PenjualanService
{
    public function list(array $filters)
    {
        return Penjualan_heders::query()
            ->with(['rinci.menu'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_transaksi', 'like', "%{$search}%")
                      ->orWhere('angkringan_id', 'like', "%{$search}%")
                      ->orWhereHas('rinci.menu', function ($mq) use ($search) {
                          $mq->where('nama_menu', 'like', "%{$search}%");
                      });
                });
            })
            ->when($filters['dateFrom'] ?? null, function ($query, $dateFrom) {
                $query->whereDate('tanggal_transaksi', '>=', $dateFrom);
            })
            ->when($filters['dateTo'] ?? null, function ($query, $dateTo) {
                $query->whereDate('tanggal_transaksi', '<=', $dateTo);
            })
            ->when($filters['angkringan_id'] ?? null, function ($query, $angkringanId) {
                $query->where('angkringan_id', $angkringanId);
            })
            ->latest('tanggal_transaksi')
            ->simplePaginate(
                $filters['per_page'] ?? 10,
                ['*'],
                'page',
                $filters['page'] ?? 1
            );
    }

    public function bayar(array $data)
    {
        return DB::transaction(function () use ($data) {
            $penjualan = Penjualan_heders::where('id', $data['id'])
                ->where('no_transaksi', $data['no_transaksi'])
                ->firstOrFail();

            if ($penjualan->flag == 2) {
                abort(422, 'Transaksi sudah lunas dan tidak dapat dibayar ulang.');
            }

            if ($data['dibayar'] < $penjualan->total_harga) {
                abort(422, 'Uang bayar kurang dari total harga.');
            }

            $penjualan->update([
                'flag' => 2,
                'tgl_bayar' => now(),
                'user_bayar' => auth()->id(),
                'uang_bayar' => $data['total'],
                'metode_bayar' => $data['metode_bayar'],
            ]);

            return $penjualan->fresh(['rinci.menu']);
        });
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();

            $noTransaksi = $this->generateNoTransaksi();

            $totalHarga = collect($data['items'])->sum(function ($item) {
                return (float) $item['harga'] * (int) $item['qty'];
            });

            // $angkringanId = $user->id == 1
            //     ? ($data['angkringan_id'] ?? null)
            //     : $user->id;

            $header = Penjualan_heders::create([
                'no_transaksi' => $noTransaksi,
                'tanggal_transaksi' => now(),
                'user_id' => $user->id,
                'angkringan_id' => $data['kode_angkringan'],
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
