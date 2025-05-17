<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HistoriApotek extends Model
{
    use HasUuids;
    protected $fillable = [
        'product_apotek_id',
        'jumlah',
        'type',
        'expired_at',
        'keterangan',
    ];
    public function productApotek()
    {
        return $this->belongsTo(ProductApotek::class, 'product_apotek_id');
    }
}
