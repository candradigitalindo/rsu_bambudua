<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Loket extends Model
{
    use HasUuids;
    protected $table = 'lokets';
    protected $fillable = ['lokasiloket_id', 'user_id', 'kode_loket'];

    public function lokasiloket()
    {
        return $this->belongsTo(LokasiLoket::class);
    }
}
