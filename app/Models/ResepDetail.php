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
        'satuan',
        'qty',
        'aturan_pakai',
        'expired_at',
        'product_apotek_id',
        'harga',
        'total_harga',
        'status',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    /**
     * Mendefinisikan relasi ke model ProductApotek.
     */
    public function productApotek()
    {
        return $this->belongsTo(ProductApotek::class, 'product_apotek_id');
    }
}
