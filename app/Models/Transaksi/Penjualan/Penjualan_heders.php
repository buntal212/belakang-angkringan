<?php

namespace App\Models\Transaksi\Penjualan;

use App\Models\User;
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
