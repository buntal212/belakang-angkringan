<?php

namespace App\Models\Transaksi\Penjualan;

use App\Models\MasterMenu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan_rinci extends Model
{
    use HasFactory;
    protected $table = 'penjualan_rinci';
    protected $guarded = ['id'];

    public function menu()
    {
        return $this->belongsTo(MasterMenu::class, 'menu_id', 'kodemenu');
    }
}

