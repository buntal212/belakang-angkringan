<?php

namespace App\Models\Transaksi\Penjualan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan_rinci extends Model
{
    use HasFactory;
    protected $table = 'penjualan_rinci';
    protected $guarded = ['id'];
}

