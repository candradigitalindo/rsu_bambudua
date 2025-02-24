<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasUuids;
    protected $fillable = ['lokasiloket_id', 'prefix', 'nomor', 'status'];

    public function lokasiloket()
    {
        return $this->belongsTo(LokasiLoket::class);
    }
}
