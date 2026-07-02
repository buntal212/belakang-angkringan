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
                      ->orWhere('atasnama', 'like', "%{$search}%")
                      ->orWhereHas('rinci.menu', function ($mq) use ($search) {
                          $mq->where('name', 'like', "%{$search}%");
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
            $id = $data['id'] ?? null;

            if (!empty($id)) {
                $header = Penjualan_heders::findOrFail($id);

                $header->update([
                    'angkringan_id' => $data['kode_angkringan'],
                    'atasnama'     => $data['atasnama'] ?? null,
                    'keterangan'   => $data['catatan'] ?? null,
                ]);

                $header->rinci()->delete();
            } else {
                $header = Penjualan_heders::create([
                    'no_transaksi'      => $this->generateNoTransaksi(),
                    'tanggal_transaksi' => now(),
                    'user_id'           => $user->id,
                    'angkringan_id'     => $data['kode_angkringan'],
                    'flag'              => 1,
                    'atasnama'          => $data['atasnama'] ?? null,
                    'keterangan'        => $data['catatan'] ?? null,
                    'total_harga'       => 0,
                ]);
            }
            $totalHarga = 0;

            foreach ($data['items'] as $item) {

                $jumlah = (int) $item['qty'];
                $harga  = (float) $item['harga'];
                $subtotal = $jumlah * $harga;

                $totalHarga += $subtotal;

                Penjualan_rinci::create([
                    'header_id'     => $header->id,
                    'menu_id'       => $item['kodemenu'],
                    'jumlah'        => $jumlah,
                    'harga_satuan'  => $harga,
                    'subtotal'      => $subtotal,
                ]);
            }

            $header->update([
                'total_harga' => $totalHarga,
            ]);

            return $header->load('rinci.menu');
        });
    }
    private function generateNoTransaksi(): string
    {
        $user = auth()->user();

         return sprintf(
            'TR-%02d-%s-%03d',
            $user->id,
            now()->format('YmdHis'),
            random_int(100, 999)
        );
    }
}
