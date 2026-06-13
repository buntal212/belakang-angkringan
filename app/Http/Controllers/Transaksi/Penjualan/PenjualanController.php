<?php

namespace App\Http\Controllers\Transaksi\Penjualan;

use App\Http\Controllers\Controller;
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
    public function index(){
        $data = Penjualan_heders::all();
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
