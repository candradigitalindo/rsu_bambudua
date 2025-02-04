<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LokasiLoket extends Model
{
    use HasUuids;
    protected $fillable = [
        'lokasi_loket', 'prefix_antrian'
    ];

    public function loket()
    {
        return $this->hasMany(Loket::class, 'lokasiloket_id');
    }
}

