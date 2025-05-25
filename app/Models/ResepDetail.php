<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ResepDetail extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'resep_id',
        'nama_obat',
        'qty',
        'aturan_pakai',
        'expired_at',
        'product_apotek_id',
        'harga',
        'total_harga',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }
}
