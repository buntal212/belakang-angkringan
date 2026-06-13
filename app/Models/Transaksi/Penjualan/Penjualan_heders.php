<?php

namespace App\Models\Transaksi\Penjualan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan_heders extends Model
{
    use HasFactory;
    protected $table = 'penjualan_header';
    protected $guarded = ['id'];

    public function rinci()
    {
        return $this->hasMany(Penjualan_rinci::class, 'header_id', 'id');
    }
}
