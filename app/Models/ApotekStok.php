<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ApotekStok extends Model
{
    use HasUuids;
    protected $fillable = [
        'product_apotek_id',
        'expired_at',
        'status',
        'status_expired',
    ];
    public function productApotek()
    {
        return $this->belongsTo(ProductApotek::class, 'product_apotek_id');
    }
}
