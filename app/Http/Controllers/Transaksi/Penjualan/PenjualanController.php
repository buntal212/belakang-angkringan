<?php

namespace App\Http\Controllers\Transaksi\Penjualan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Penjualan\BayarPenjualanRequest;
use App\Http\Requests\Penjualan\ListPenjualanRequest;
use App\Http\Requests\Penjualan\StorePenjualanRequest;
use App\Models\Transaksi\Penjualan\Penjualan_heders;
use App\Services\Penjualan\PenjualanService;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    protected PenjualanService $service;
    public function __construct(PenjualanService $service)
    {
        $this->service = $service;
    }
    public function bayar(BayarPenjualanRequest $request)
    {
        $data = $this->service->bayar($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil disimpan',
            'data' => $data,
        ]);
    }

    public function index(ListPenjualanRequest $request)
    {
        $data = $this->service->list($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data penjualan berhasil diambil',
            'data' => $data,
        ]);
    }

   public function store(StorePenjualanRequest $request)
    {
        $data = $this->service->store($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penjualan berhasil disimpan',
            'data' => $data,
        ]);
    }
}
