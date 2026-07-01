<?php

namespace App\Models\Transaksi\Penjualan;

use App\Models\Menus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan_rinci extends Model
{
    use HasFactory;
    protected $table = 'penjualan_rinci';
    protected $guarded = ['id'];

    public function menu()
    {
        return $this->belongsTo(Menus::class, 'menu_id', 'id');
    }
}

