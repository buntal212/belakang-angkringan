<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kodemenu', 'angkringan_id', 'name', 'kategori', 'harga', 'flag', 'gambar'])]
class MasterMenu extends Model
{
    use HasFactory;

    /**
     * Default values for attributes.
     */
    protected $attributes = [
        'flag' => 'tidak aktif',
        'harga' => 0,
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke User (sebagai angkringan).
     */
    public function angkringan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'angkringan_id');
    }
}
